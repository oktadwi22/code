<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Comment;
use App\Models\Form;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCollection;
use App\Models\Rating;
use App\Models\RefundActivity;
use App\Models\RefundRequest;
use App\Models\Review;
use App\Models\ReviewCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthorController extends Controller
{
    public function showProfile($username = null)
    {
        $author      = User::active()->where("username", $username)->firstOrFail();
        $collections = ProductCollection::public()->where('user_id', $author->id)->limit(3)->get();
        $pageTitle   = 'Profile';
        return view($this->activeTemplate . 'user.profile', compact('author', 'pageTitle', 'collections'));
    }

    public function portfolio($username = null)
    {
        abort_if(!$username, 404);

        $sortBy    = request()->sort_by;
        $orderBy   = request()->order_by ?? 'title';
        $pageTitle = 'Portfolio';
        $author    = User::active()->author()->where("username", $username)->firstOrFail();
        $products  = $author->products()->searchable(['title'])->with('author', 'users');

        // if author is not the visitor
        if ($author->id != auth()->id()) {
            $products = $products->approved();
        }

        if ($author->id == auth()->id()) {
            $products->whereNotIn('status', [Status::PRODUCT_PERMANENT_DOWN, Status::PRODUCT_HARD_REJECTED]);
        }

        if ($orderBy) {
            $direction                       = 'desc';
            if ($orderBy == 'title') $direction = 'asc';
            $products->orderBy($orderBy, $direction);
        }

        $products = $products->paginate(getPaginate(18));

        return view($this->activeTemplate . 'user.portfolio.portfolio', compact('pageTitle', 'author', 'products'));
    }

    public function followers($author = null)
    {
        $pageTitle = 'Followers';
        $author    = $author ? User::where('username', $author)->firstOrFail() : auth()->user();
        $followers = $author->followers()->paginate(getPaginate());

        return view($this->activeTemplate . 'user.followers', compact('pageTitle', 'followers', 'author'));
    }

    public function following($author = null)
    {
        $pageTitle  = 'Following';
        $author     = $author ? User::where('username', $author)->firstOrFail() : auth()->user();
        $followings = $author->follows()->paginate(getPaginate(10));

        return view($this->activeTemplate . 'user.following', compact('pageTitle', 'author', 'followings'));
    }

    public function hiddenItems()
    {
        $pageTitle = 'Hidden Items';
        $status    = request()->status;
        $author    = auth()->user();
        $products  = Product::where('user_id', $author->id)->orderBy("id")->whereIn('status', [Status::PRODUCT_DOWN, Status::PRODUCT_SOFT_REJECTED, Status::PRODUCT_PENDING]);

        if (!is_null($status) && $status != 1) {
            $products->where('status', $status);
        }

        $products = $products->paginate(getPaginate());

        return view($this->activeTemplate . 'user.hidden_items', compact('pageTitle', 'author', 'products'));
    }

    public function uploadProduct()
    {
        $pageTitle = 'Upload Product';
        return view($this->activeTemplate . 'user.upload_product', compact('pageTitle'));
    }

    public function refunds()
    {
        $pageTitle               = 'Refunds';
        $author                  = auth()->user();
        $refundRequests          = $author->refundRequests()->with('orderItem.product')->get();
        $submittedRefundRequests = $author->submittedRefundRequests()->with('orderItem.product')->get();
        $refundRequests          = $refundRequests->merge($submittedRefundRequests)->sortBy('status');

        return view($this->activeTemplate . 'user.refunds', compact('pageTitle', 'author', 'refundRequests'));
    }

    public function refundActivity($id)
    {
        $pageTitle               = 'Refund Activity';
        $author                  = auth()->user();
        $refundRequests          = $author->refundRequests()->with('orderItem')->get();
        $submittedRefundRequests = $author->submittedRefundRequests()->with('orderItem')->get();
        $refundRequests          = $refundRequests->merge($submittedRefundRequests);
        $refundRequest           = $refundRequests->find($id);
        abort_if(!$refundRequest, 404);

        $refundActivites = $refundRequest->activites()->with(['seller', 'buyer'])->get();

        return view($this->activeTemplate . 'user.refund_activity', compact('pageTitle', 'author', 'refundRequest', 'refundActivites'));
    }

    public function refundActivityReply(Request $request, $id)
    {

        $request->validate([
            'message' => 'required',
        ]);

        $refundRequest               = RefundRequest::findOrFail($id);
        $activity                    = new RefundActivity();
        $activity->refund_request_id = $id;
        $activity->message           = $request->message;
        $activity->buyer_id          = $refundRequest->buyer_id == auth()->id() ? $refundRequest->buyer_id : null;
        $activity->seller_id         = $refundRequest->user_id  == auth()->id() ? $refundRequest->user_id : null;
        $activity->save();

        $notify = ['success', 'Your message has been submitted'];
        return back()->withNotify($notify);
    }

    public function earning()
    {
        $pageTitle = 'Earning';
        $author    = auth()->user();

        $recentSales  = $author->soldItems()->with(['order' => function ($query) {
            $query->paid();
        }])->latest()->with('product')->limit(10)->get();

        $soldItems    = $author->soldItems()->where("is_refunded", Status::NO)->with(['order' => function ($query) {
            $query->paid();
        }]);

        $todayEarningQuery = (clone $soldItems)->whereDate('order_items.created_at', now());
        $todayEarning      = (clone $todayEarningQuery)->sum('seller_earning');
        $thisWeekEarning   = (clone $soldItems)->whereBetween('order_items.created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('seller_earning');
        $thisMonthEarning  = (clone $soldItems)->whereMonth('order_items.created_at', now()->month)->sum('seller_earning');
        $thisYearEarning   = (clone $soldItems)->whereYear('order_items.created_at', now()->year)->sum('seller_earning');
        $totalEarning      = (clone $soldItems)->sum('seller_earning');

        return view($this->activeTemplate . 'user.earning', compact('pageTitle', 'author', 'recentSales', 'todayEarning', 'thisWeekEarning', 'thisMonthEarning', 'thisYearEarning', 'totalEarning'));
    }

    public function download()
    {
        $pageTitle        = 'Purchased Item';
        $orderBy          = request()->order_by;
        $reviewCategories = ReviewCategory::active()->get();
        $purchasedItems   = auth()->user()->purchasedItems()->whereHas('order', function ($query) {
            $query->paid();
        })->latest()->searchable(['product:title', 'purchase_code'])->where('is_refunded', Status::NO);
        $purchasedItems = $purchasedItems->paginate(getPaginate());

        return view($this->activeTemplate . 'user.download', compact('pageTitle', 'purchasedItems', 'reviewCategories'));
    }

    public function saveReview(Request $request, $productId)
    {
        $request->validate([
            'review'          => 'required',
            'rating'          => 'required|min:1|max:5|integer',
            'purchase_code'   => 'required',
            'review_category' => 'required'
        ]);

        try {
            $purchasedProducts = auth()->user()->purchasedItems()->pluck('product_id')->toArray();
            $product           = Product::approved()->whereIn('id', $purchasedProducts)->findOrFail($productId);
            $user              = User::findOrFail($product->user_id);
            $review            = Review::where(['product_id' => $productId, 'user_id' => auth()->id()])->first();
            $isNewReview       = false;

            if (!$review) {
                $review      = new Review();
                $isNewReview = true;
                $rating      = Rating::where('value', '>=', $product->avg_rating)->first();

                if (!$rating) {
                    $rating = Rating::orderBy('value', 'desc')->first();
                }

                $rating->product_count++;
                $rating->save();
            }

            $review->user_id            = auth()->id();
            $review->product_id         = $productId;
            $review->author_id          = $product->user_id;
            $review->review_category_id = $request->review_category;
            $review->review             = $request->review;
            $review->rating             = $request->rating;
            $review->save();
            $user->save();
            $product->save();

            $user->total_review = $user->reviews()->count();
            $user->avg_rating   = $user->reviews()->avg('rating');
            $user->save();

            $product->total_review = $product->reviews()->count();
            $product->avg_rating   = $product->reviews()->avg('rating');
            $product->save();

            $author = $product->author;
            if ($isNewReview && @$author->email_settings->buyer_review_notification) {
                $data = [
                    'purchase_code'   => $request->purchase_code,
                    'review'          => $request->review,
                    'rating'          => displayRating($request->rating),
                    'review_category' => ReviewCategory::find($request->review_category)->name,
                    'link'            => route('product.reviews', ['slug' => $product->slug, 'review_id' => $review->id])
                ];
                notify($author, 'BUYER_REVIEW', $data);
            }

            $notify[] = ['success', 'Your review submitted successfully'];
            return back()->withNotify($notify);
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function saveComment(Request $request, $productId)
    {
        $request->validate([
            'text' => 'required',
        ]);

        $product = Product::approved()->find($productId);

        $comment               = new Comment();
        $comment->user_id      = auth()->id();
        $comment->product_id   = $productId;
        $comment->text         = $request->text;
        $comment->author_reply = $product->author->id === auth()->id();
        $comment->parent_id    = $request->parent_id ?? 0;
        $comment->save();

        $author = $product->author;

        if (@$author->email_settings->comment_notification) {
            notify($author, 'COMMENTED', [
                'author'       => $author->username,
                'product_name' => $product->title,
                'comment'      => $comment->text,
                'username'     => auth()->user()->username,
                'url'          => route('product.comments', ['slug' => $product->slug, 'comment_id' => $comment->id])
            ]);
        }

        $notify[] = ['success', 'Your comment submitted succesfully'];
        return back()->withNotify($notify);
    }

    public function commentList()
    {
        $pageTitle  = 'Comment List';
        $author     = auth()->user();
        $notReplied = request()->not_replied;
        $comments   = $author->comments()->searchable(['text', 'product:title'])->where('parent_id', 0)->where('review_id', 0)->with('product', 'user');

        if ($notReplied) {
            $comments->withCount('replies')->having('replies_count', 0);
        }
        $comments = $comments->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'user.comments.index', compact('pageTitle', 'comments', 'author'));
    }

    public function repliesList($commentId)
    {
        $pageTitle = 'Reply List';
        $author    = auth()->user();
        $comment   = Comment::with('replies')->findOrFail($commentId);
        $replies   = $comment->replies()->paginate(getPaginate());
        return view($this->activeTemplate . 'user.comments.replies.index', compact('pageTitle', 'comment', 'replies', 'author'));
    }

    public function deleteReply($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        $notify[] = ['success', 'Reply deleted successfully'];
        return back()->withNotify($notify);
    }

    public function deleteComment($id)
    {
        $comment = Comment::whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);
        $comment->delete();
        $notify[] = ['success', 'Comment deleted successfully'];
        return back()->withNotify($notify);
    }

    public function reviewList()
    {
        $pageTitle  = 'Reviews';
        $author     = auth()->user();
        $reviews    = $author->reviews()->searchable(['review', 'product:title'])->with('product', 'user');
        $notReplied = request()->not_replied;
        $reviews    = $reviews->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'user.reviews.index', compact('pageTitle', 'reviews', 'author'));
    }

    public function favorites()
    {
        $pageTitle        = 'Favorites';
        $orderBy          = request()->order_by;
        $favoriteProducts = auth()->user()->favoriteProducts();

        if ($orderBy) {
            $favoriteProducts->orderBy($orderBy);
        }

        $favoriteProducts = $favoriteProducts->get();
        return view($this->activeTemplate . 'user.favorites', compact('pageTitle', 'favoriteProducts'));
    }

    public function collections()
    {
        $pageTitle   = 'Collections';
        $sortBy      = request()->sort_by;
        $orderColumn = $sortBy == 'date' ? 'created_at' : 'name';
        $collections = auth()->user()->collections()->orderBy($orderColumn)->with('products', 'user')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.collection.list', compact('pageTitle', 'collections'));
    }

    public function collectionDetails($username, $id)
    {
        $pageTitle  = 'Collection Details';
        $collection = ProductCollection::findOrFail($id);
        return view($this->activeTemplate . 'user.collection.details', compact('pageTitle', 'collection'));
    }

    public function storeCollection(Request $request)
    {
        $collectionImageSize = explode('x', getFileSize('productCollection'));
        $collectionImageW    = $collectionImageSize[0];
        $collectionImageH    = $collectionImageSize[1];

        $request->validate([
            'name' => [
                'required',
                Rule::unique('product_collections')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png']), "dimensions:width=$collectionImageW,height=$collectionImageH"]
        ]);

        $collection            = new ProductCollection();
        $collection->user_id   = auth()->id();
        $collection->is_public = $request->is_public ?? 0;
        $collection->name      = $request->name;

        if ($request->hasFile('image')) {
            try {
                $collection->image = fileUploader($request->image, getFilePath('productCollection'), getFileSize('productCollection'));
            } catch (\Exception $e) {
                $notify[] = ['success', 'Could not upload image'];
                return back()->withNotify($notify);
            }
        }

        $collection->save();

        if (request()->ajax()) {
            return response()->json(['status' => 'success', 'collection' => $collection]);
        } else {
            $notify[] = ['success', "Collection added successfully"];
            return back()->withNotify($notify);
        }
    }

    public function updateCollection(Request $request, $id)
    {
        $collectionImageSize = explode('x', getFileSize('productCollection'));
        $collectionImageW    = $collectionImageSize[0];
        $collectionImageH    = $collectionImageSize[1];

        $request->validate([
            'name'  => 'required',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png']), "dimensions:width=$collectionImageW,height=$collectionImageH"]
        ]);

        $collection            = ProductCollection::where('user_id', auth()->id())->findOrFail($id);
        $collection->user_id   = auth()->id();
        $collection->is_public = $request->is_public ?? 0;
        $collection->name      = $request->name;
        if ($request->hasFile('image')) {
            try {
                $collection->image = fileUploader($request->image, getFilePath('productCollection'), getFileSize('productCollection'), $collection->image);
            } catch (\Exception $e) {
                $notify[] = ['error', 'Could not upload image'];
                return back()->withNotify($notify);
            }
        }
        $collection->save();

        $notify[] = ['success', 'Collection updated successfully'];
        return back()->withNotify($notify);
    }

    public function deleteCollection($id)
    {
        $collection = ProductCollection::where('user_id', auth()->id())->find($id);

        if (!$collection) {
            $notify[] = ['error', 'Collection not found'];
            return back()->withNotify($notify);
        }

        try {
            fileManager()->removeFile($collection->image);
        } catch (\Exception $e) {
            $notify[] = ['error', 'Could not delete image'];
            return back()->withNotify($notify);
        }

        $collection->delete();

        $notify[] = ['success', 'Collection deleted successfully'];
        return back()->withNotify($notify);
    }

    public function storeProductsToCollection(Request $request, $productId)
    {

        $product = Product::allActive()->find($productId);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'No product found', 'data' => []]);
        }

        $product->collections()->sync($request->collection_id);
        return response()->json(['status' => 'success', 'data' => $product->collections]);
    }

    public function getProductsCollections($id)
    {
        $product = Product::allActive()->find($id);
        $data    = $product->collections->pluck('id')->toArray();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        $product = Product::approved()->find($request->product_id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found']);
        }

        $message = $product->users->contains(auth()->id()) ? 'Product removed from favorite' : 'Product added to favorite';

        $product->users()->toggle(auth()->id());

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function removeFavorite(Request $request)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        $product = Product::allActive()->approved()->find($request->product_id);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found']);
        }

        $product->users()->detach(auth()->id());
        return response()->json(['status' => 'success', 'message' => 'Product removed from favorites']);
    }

    public function sells()
    {
        $author         = auth()->user();
        $pageTitle      = 'All Items for sale';

        $soldItems      = $author->soldItems()->where('is_refunded', Status::NO);
        $orders         = $author->orders();

        $search   = request()->search;
        $allSales = $soldItems->searchable(['purchase_code', 'product:title'])->latest()->with('product')->paginate(getPaginate(10));

        return view($this->activeTemplate . 'user.statement', compact('pageTitle', 'allSales', 'author'));
    }

    public function follow(User $user)
    {
        $isFollowing = auth()->user()->follows->contains($user->id);
        $message     = $isFollowing ? 'You have unfollowed ' . $user->fullname : $message = 'You are now following ' . $user->fullname;
        $notify[]    = ['success', $message];

        auth()->user()->follows()->toggle($user->id);

        return back()->withNotify($notify);
    }

    public function saveSettings(Request $request)
    {

        $user          = auth()->user();
        $thumbnailSize = explode('x', getFileSize('authorThumbnail'));
        $coverImgSize  = explode('x', getFileSize('authorCoverImg'));

        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
            'avatar'    => "nullable|mimes:png,jpg,jpeg|dimensions:width=$thumbnailSize[0],height=$thumbnailSize[1]",
            'cover_img' => "nullable|mimes:png,jpg,jpeg|dimensions:width=$coverImgSize[0],height=$coverImgSize[1]",
        ]);

        $user->firstname             = $request->firstname;
        $user->lastname              = $request->lastname;
        $purifier                    = new \HTMLPurifier();
        $user->bio                   = htmlspecialchars_decode($purifier->purify($request->bio));
        $user->social_media_settings = $request->social_media_settings;
        $emailSettings               = $request->email_settings ?? [];

        foreach ($emailSettings as $key => $value) {
            $emailSettings[$key] = (bool) $value;
        }

        $user->email_settings = $emailSettings;
        $user->address        = [
            'address' => $request->address,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];

        if ($request->hasFile('avatar')) {
            try {
                $user->avatar = fileUploader(
                    $request->file('avatar'),
                    getFilePath('authorThumbnail'),
                    getFileSize('authorThumbnail'),
                    $user->avatar
                );
            } catch (\Exception $e) {
                $notify[] = ['error', 'Could not upload your avatar'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('cover_img')) {
            try {
                $user->cover_img = fileUploader(
                    $request->file('cover_img'),
                    getFilePath('authorCoverImg'),
                    getFileSize('authorCoverImg'),
                    $user->cover_img
                );
            } catch (\Exception $e) {
                $notify[] = ['error', 'Could not upload your cover image'];
                return back()->withNotify($notify);
            }
        }

        $user->save();
        $notify[] = ['success', 'Settings saved successfully'];
        return back()->withNotify($notify);
    }

    public function checkout()
    {
        $pageTitle = 'Checkout';
        return view($this->activeTemplate . 'user.checkout', compact('pageTitle'));
    }

    public function sendMailToAuthor(Request $request, $authorId)
    {
        $request->validate([
            'email'   => 'required|email',
            'message' => 'required'
        ]);

        $author = User::active()->where('id', '!=', auth()->id())->findOrFail($authorId);
        $email  = $request->email;

        notify($author, 'MAIL_TO_AUTHOR', [
            'email' => $email,
            'username' => auth()->user()->username,
            'message' => $request->message
        ], ['email']);

        $notify[] = ['success', 'Your email has been sent'];
        return back()->withNotify($notify);
    }

    public function reviewReply(Request $request, $productId, $reviewId)
    {
        $request->validate([
            'reply' => 'required'
        ]);

        $product               = Product::approved()->allActive()->findOrFail($productId);
        $comment               = new Comment();
        $comment->user_id      = auth()->id();
        $comment->product_id   = $productId;
        $comment->text         = $request->reply;
        $comment->author_reply = $product->author->id === auth()->id();
        $comment->parent_id    = $request->parent_id ?? 0;
        $comment->review_id    = $reviewId;
        $comment->save();

        $notify[] = ['success', 'Your reply submitted successfully'];
        return back()->withNotify($notify);
    }

    public function downloadProduct($purchaseCode)
    {
        $user      = auth()->user();
        $orderItem = $user->purchasedItems()->where('purchase_code', $purchaseCode)->first();
        $product   = @$orderItem->product;

        abort_if(($user->id != @$orderItem->user_id || @$orderItem->is_refunded), 404);

        $downloadPath = getFilePath('productFile') . '/' . $product->slug . '/' . @$product->file;

        if (!file_exists($downloadPath) || !is_file($downloadPath)) {
            $notify[] = ['error', 'File does not exist'];
            return back()->withNotify($notify);
        }

        $orderItem->downloads = 1;
        $orderItem->save();

        return response()->download($downloadPath);
    }

    public function refundRequest(Request $request, $purchaseCode)
    {
        $request->validate([
            'reason' => 'required'
        ]);

        $orderItem = OrderItem::where('purchase_code', $purchaseCode)->where('is_refunded', Status::NO)->whereHas('order', function ($query) {
            $query->where('user_id', auth()->id());
        })->firstOrFail();

        $refundRequest = RefundRequest::whereHas('orderItem', function ($query) use ($purchaseCode) {
            $query->where('purchase_code', $purchaseCode);
        })->first();

        $refundRequest                = new RefundRequest();
        $refundRequest->user_id       = $orderItem->product->user_id; // author's id
        $refundRequest->buyer_id      = auth()->id();
        $refundRequest->order_item_id = $orderItem->id;
        $refundRequest->reason        = $request->reason;
        $refundRequest->amount        = $orderItem->product_price + $orderItem->buyer_fee + $orderItem->extended_amount;
        $refundRequest->save();

        $author = User::active()->findOrFail($refundRequest->user_id);
        notify($author, 'NEW_REFUND_REQUEST', [
            'refundable_amount' => showAmount($orderItem->seller_earning),
            'buyer'             => auth()->user()->username,
            'purchase_code'     => $orderItem->purchase_code,
            'reason'            => $refundRequest->reason
        ], ['email']);

        $notify[] = ['success', 'Your refund request is under review'];
        return back()->withNotify($notify);
    }

    public function acceptRefundRequest(Request $request, $refundId)
    {
        $refundRequest          = RefundRequest::where('user_id', auth()->id())->findOrFail($refundId);
        $refundRequest->status  = Status::APPROVED_REFUND;
        $refundRequest->save();

        $buyer                  = $refundRequest->buyer;
        $orderItem              = $refundRequest->orderItem;
        $orderItem->is_refunded = Status::YES;
        $orderItem->save();

        $product = $orderItem->product;
        $product->total_sold = max(0, $product->total_sold - 1);
        $product->save();

        $author              = $orderItem->product->author;
        $author->balance    -= $orderItem->seller_earning;
        $author->total_sold  = max(0, $author->total_sold - 1);
        $author->save();

        // seller's transaction
        $sellerTransaction               = new Transaction();
        $sellerTransaction->user_id      = $author->id;
        $sellerTransaction->amount       = $orderItem->seller_earning;
        $sellerTransaction->trx_type     = '-';
        $sellerTransaction->trx          = getTrx();
        $sellerTransaction->details      = 'Refund Amount Subtracted';
        $sellerTransaction->remark       = 'refund';
        $sellerTransaction->post_balance = $author->balance;
        $sellerTransaction->save();

        // buyer's transaction
        $buyer           = $refundRequest->buyer;
        $buyer->balance += $refundRequest->amount;
        $buyer->save();

        $buyerTransaction               = new Transaction();
        $buyerTransaction->user_id      = $buyer->id;
        $buyerTransaction->amount       = $refundRequest->amount;
        $buyerTransaction->trx_type     = '+';
        $buyerTransaction->trx          = getTrx();
        $buyerTransaction->details      = 'Refund Amount Added';
        $buyerTransaction->remark       = 'refund';
        $buyerTransaction->post_balance = $buyer->balance;
        $buyerTransaction->save();

        notify($buyer, 'REFUND_APPROVED', [
            'purchase_code' => @$refundRequest->orderItem->purchase_code,
            'buyer'         => $buyer->username,
            'author'        => $author->username,
            'amount'        => $refundRequest->amount,
            'message'       => $request->message ?? 'Your refund was approved',
        ]);

        $notify[] = ['success', 'Refund request accepted successfully'];
        return back()->withNotify($notify);
    }

    public function rejectRefundRequest(Request $request, $refundId)
    {
        $refundRequest         = RefundRequest::where('user_id', auth()->id())->findOrFail($refundId);
        $buyer                 = $refundRequest->buyer;
        $refundRequest->status = Status::REJECTED_REFUND;
        $refundRequest->save();

        notify($buyer, 'REFUND_REJECTED', [
            'purchase_code' => @$refundRequest->orderItem->purchase_code,
            'buyer'         => $buyer->username,
            'author'        => @auth()->user()->username,
            'message'       => @$request->message ?? 'Your refund was rejected',
        ]);

        $notify[] = ['success', 'You rejected the refund request'];
        return back()->withNotify($notify);
    }

    public function authorInfoForm()
    {
        if (auth()->user()->is_author == 1) {
            $notify[] = ['error', 'You are already an author'];
            return to_route('user.home')->withNotify($notify);
        }

        $pageTitle = 'Author Information';
        return view($this->activeTemplate . 'user.author.form', compact('pageTitle'));
    }

    public function authorInfoFormSubmit(Request $request)
    {
        $form           = Form::where('act', 'author_info')->first();
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $authorInfo        = $formProcessor->processFormData($request, $formData);
        $user              = auth()->user();
        $user->author_info = $authorInfo;
        $user->is_author   = Status::YES;
        $user->save();

        return to_route('user.home');
    }
}
