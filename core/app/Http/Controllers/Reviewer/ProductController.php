<?php

namespace App\Http\Controllers\Reviewer;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function pendingProducts()
    {
        $pageTitle = 'Pending Products';
        $products = $this->productData('pending', true)->whereIn('assigned_to', [0, auth('reviewer')->id()])->paginate(getPaginate());
        return view('reviewer.product.list', compact('pageTitle', 'products'));
    }

    public function awatingProducts()
    {
        $pageTitle = 'Product Waiting For Review';
        $reviewer  = auth('reviewer')->user();

        $products = Product::whereIn('sub_category_id', $reviewer->subcategories ?? [])->where(function ($q) {
            $q->where('status', Status::PRODUCT_PENDING)->orWhere('product_updated', Status::PRODUCT_UPDATE_PENDING);
        })->with(['category', 'subCategory'])->whereIn('assigned_to', [0, auth('reviewer')->id()])
            ->where('status', '!=', Status::PRODUCT_PERMANENT_DOWN)
            ->where('status', '!=', Status::PRODUCT_UPDATE_HARD_REJECT)
            ->where('status', '!=', Status::PRODUCT_HARD_REJECTED)->paginate(getPaginate());


        return view('reviewer.product.list', compact('pageTitle', 'products'));
    }

    public function assignedProducts()
    {
        $pageTitle = 'Assigned Products';
        $products = $this->productData(null, true)->where('assigned_to', auth('reviewer')->id())->paginate(getPaginate());

        return view('reviewer.product.list', compact('pageTitle', 'products'));
    }
    public function approvedProducts()
    {
        $pageTitle = 'Approved Products';
        $products = $this->ownProductData('approved');
        return view('reviewer.product.list', compact('pageTitle', 'products'));
    }

    public function softRejectedProducts()
    {
        $pageTitle = 'Soft Rejected Products';
        $products = $this->ownProductData('softRejected');
        return view('reviewer.product.list', compact('pageTitle', 'products'));
    }

    public function hardRejectedProducts()
    {
        $pageTitle = 'Hard Rejected Products';
        $products = $this->ownProductData('hardRejected');
        return view('reviewer.product.list', compact('pageTitle', 'products'));
    }

    public function downProducts()
    {
        $pageTitle = 'Soft Disabled Products';
        $products = $this->ownProductData('down');
        return view('reviewer.product.list', compact('pageTitle', 'products'));
    }

    public function permanentDownProducts()
    {
        $pageTitle = 'Permanently Disabled Products';
        $products = $this->ownProductData('permanentDown');
        return view('reviewer.product.list', compact('pageTitle', 'products'));
    }

    public function assignProduct($slug)
    {
        $product = $this->productData(null, true)->where('assigned_to', Status::NO)->where('status', Status::PRODUCT_PENDING)->where('slug', $slug)->firstOrFail();

        $notify[]             = ['success', 'You started review this product'];
        $product->assigned_to = auth('reviewer')->id();
        $product->save();
        return back()->withNotify($notify);
    }

    public function details($slug)
    {
        $pageTitle = 'Product Details';
        $product = $this->productData(null, true)->where('slug', $slug)->firstOrFail();

        if ($product->assigned_to && $product->assigned_to != auth('reviewer')->id()) {
            abort(404);
        }

        $activities = $product->activities()
            ->with('user', 'reviewer')
            ->paginate(getPaginate(10));

        return view('reviewer.product.details', compact('pageTitle', 'product', 'activities'));
    }

    public function updatedProducts()
    {
        $pageTitle = 'Updated Products';
        $products = $this->ownProductData(null, true)->where('product_updated', Status::ENABLE)->paginate(getPaginate());
        return view('reviewer.product.list', compact('pageTitle', 'products'));
    }

    public function approveProduct($productId)
    {
        $product = $this->ownProductData(null, true)->findOrFail($productId);

        $product->approved_by  = auth('reviewer')->id();
        $product->published_at = now();

        $newFile            = $product->temp_file;
        $product->temp_file = null;
        $product->file      = $newFile;
        $product->status    = Status::PRODUCT_APPROVED;
        $product->save();

        $statusMessage         = "[Approve]";
        $activity              = new Activity();
        $activity->message     = $statusMessage . 'Your product has been approved';
        $activity->product_id  = $productId;
        $activity->status      = Status::PRODUCT_APPROVED;
        $activity->reviewer_id = auth('reviewer')->id();
        $activity->save();

        $author = $product->author;
        if (@$author->email_settings->review_notification) {
            notify(
                $author,
                'PRODUCT_APPROVED',
                [
                    'author'           => $author->username,
                    'message'          => $activity->message,
                    'product_name'     => $product->title,
                    'product_category' => $product->category->title,
                    'url'              => route('product.details', $product->slug),
                    'approved_time'    => showDateTime(now())
                ],
                ['email']
            );
        }

        $notify[] = ['success', 'Product approved successfully'];
        return back()->withNotify($notify);
    }

    public function rejectProduct(Request $request, $productId, $type)
    {
        $isRequired = $type == Status::PRODUCT_HARD_REJECTED ? 'nullable' : 'required';

        $request->validate([
            'reason' => $isRequired,
        ]);

        $product         = $this->ownProductData(null, true)->findOrFail($productId);
        $product->status = $type;
        $product->save();

        $statusMessage = '';
        $notifyMessage = "Product rejected successfully";

        if ($type == Status::PRODUCT_SOFT_REJECTED) {
            $statusMessage = "[Soft Reject] ";
            $notifyMessage = "Product soft rejected successfully";
        } elseif ($type == Status::PRODUCT_HARD_REJECTED) {
            $statusMessage = "[Hard Reject] ";
            $notifyMessage = "Product hard rejected successfully";
        } elseif ($type == Status::PRODUCT_DOWN) {
            $statusMessage = "[Down] ";
            $notifyMessage = "Product down successfully";
        } elseif ($type == Status::PRODUCT_PERMANENT_DOWN) {
            $statusMessage = "[Permanent Down] ";
            $notifyMessage = "Product permanent down successfully";
        }

        $activity = new Activity();

        if ($type == Status::PRODUCT_PERMANENT_DOWN) {
            $message = $request->message;
        } elseif ($type == Status::PRODUCT_HARD_REJECTED) {
            $message = 'The product has been rejected';
        } else {
            $message = $request->reason;
        }

        $activity->message = $statusMessage . $message;
        $activity->status = $type;
        $activity->product_id = $productId;
        $activity->reviewer_id = auth('reviewer')->id();
        $activity->save();

        if (in_array($type, [Status::PRODUCT_HARD_REJECTED, Status::PRODUCT_PERMANENT_DOWN]) && !OrderItem::where('product_id', $product->id)->exists()) {
            fileManager()->removeFile(getFilePath('productFile') . '/' . $product->slug . '/' . $product->file);
        }

        $author = $product->author;
        $notificationData = [
            'author'           => $author->username,
            'product_name'     => $product->title,
            'product_category' => $product->category->name,
            'review_time'      => showDateTime(now()),
            'message'          => $activity->message,
        ];

        if (@$author->email_settings->review_notification) {
            switch ($type) {
                case Status::PRODUCT_SOFT_REJECTED:
                    $notificationData['edit_url'] = route('user.product.edit', $product->slug);
                    notify($author, 'PRODUCT_SOFT_REJECTED', $notificationData, ['email']);
                    break;
                case Status::PRODUCT_HARD_REJECTED:
                    notify($author, 'PRODUCT_HARD_REJECTED', $notificationData, ['email']);
                    break;
                case Status::PRODUCT_DOWN:
                    $notificationData['edit_url'] = route('user.product.edit', $product->slug);
                    notify($author, 'PRODUCT_DOWN', $notificationData, ['email']);
                    break;
                case Status::PRODUCT_PERMANENT_DOWN:
                    notify($author, 'PRODUCT_PERMANENT_DOWN', $notificationData, ['email']);
                    break;
            }
        }

        return back()->withNotify([['success', $notifyMessage]]);
    }


    public function approveUpdate($productId)
    {
        $product                  = $this->ownProductData(null, true)->findOrFail($productId);
        $oldFile                  = $product->file;
        $newFile                  = $product->temp_file;
        $product->last_updated    = now();
        $product->temp_file       = null;
        $product->file            = $newFile;
        $product->product_updated = Status::PRODUCT_NO_UPDATE;
        $product->status          = Status::PRODUCT_APPROVED;
        $product->save();

        fileManager()->removeFile(getFilePath('productFile') . '/' . $product->slug . '/' . $oldFile);

        $statusMessage         = "[Update Approved] ";
        $activity              = new Activity();
        $activity->message     = $statusMessage . 'Your update has been approved';
        $activity->reviewer_id = auth('reviewer')->id();
        $activity->product_id  = $productId;
        $activity->save();

        $author = $product->author;
        if (@$author->email_settings->review_notification) {
            notify($author, 'PRODUCT_UPDATE_APPROVED', [
                'author'           => $author->username,
                'product_name'     => $product->title,
                'product_category' => $product->category->name,
                'review_time'      => showDateTime(now()),
                'url'              => route('product.details', $product->slug),
                'message'          => $activity->message,
            ], ['email']);
        }

        $notify[] = ['success', 'Update patch approved successfully'];
        return back()->withNotify($notify);
    }

    public function rejectUpdate(Request $request, $productId, $type)
    {
        $request->validate([
            'reason' => 'required'
        ]);

        $product = $this->ownProductData(null, true)->findOrFail($productId);
        $newFile = $product->temp_file;

        $product->temp_file = null;
        $product->product_updated = $type;
        $product->save();

        $activity = new Activity();
        $statusMessage = '';

        if ($type == Status::PRODUCT_UPDATE_SOFT_REJECT) {
            $statusMessage = "[Update Soft Reject] ";
        } elseif ($type == Status::PRODUCT_UPDATE_HARD_REJECT) {
            $statusMessage = "[Update Hard Reject] ";
        }

        $activity->message = $statusMessage . $request->reason;
        $activity->reviewer_id = auth('reviewer')->id();
        $activity->product_id = $productId;
        $activity->save();

        fileManager()->removeFile(getFilePath('productFile') . '/' . $product->slug . '/' . $newFile);

        $author = $product->author;
        if (@$author->email_settings->review_notification) {
            notify($author, 'PRODUCT_UPDATE_REJECTED', [
                'author'           => $author->username,
                'product_name'     => $product->title,
                'product_category' => $product->category->name,
                'review_time'      => showDateTime(now()),
                'edit_url'         => route('user.product.edit', $product->slug),
                'message'          => $activity->message,
            ], ['email']);
        }

        $notify[] = ['success', 'You rejected the update patch'];
        return back()->withNotify($notify);
    }

    private function productData($scope = null, $withoutPagination = null, $own = null)
    {
        $reviewer = auth('reviewer')->user();
        $products = Product::whereIn('sub_category_id', $reviewer->subcategories ?? [])->with(['category', 'subCategory'])->searchable(['title']);

        if ($scope) $products->$scope();
        if ($own) $products->where('assigned_to', auth('reviewer')->id());
        if ($withoutPagination) return $products;

        $products = $products->orderBy('id')->paginate(getPaginate());
        return $products;
    }

    private function ownProductData($scope = null, $withoutPagination = null)
    {
        return $this->productData($scope, $withoutPagination, true);
    }

    public function downloadProduct($productId)
    {
        $product = $this->ownProductData(null, true)->findOrFail($productId);
        $downloadPath = getFilePath('productFile') . productFilePath($product, 'file');

        if (!file_exists($downloadPath)) {
            $notify[] = ['error', 'File does not exist'];
            return back()->withNotify($notify);
        }

        return response()->download($downloadPath);
    }

    public function downloadProductTemp($productId)
    {
        $product = $this->productData(null, true)->findOrFail($productId);
        $downloadPath = getFilePath('productFile') . productFilePath($product, 'temp_file');
        return response()->download($downloadPath);
    }

    public function replyActivity(Request $request, $productId)
    {
        $request->validate([
            'message' => 'required'
        ]);

        $activity = new Activity();
        $activity->message = $request->message;
        $activity->product_id = $productId;
        $activity->reviewer_id = auth('reviewer')->id();
        $activity->save();

        $notify[] = ['success', 'Your message been submitted'];
        return back()->withNotify($notify);
    }
}
