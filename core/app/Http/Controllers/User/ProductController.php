<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Form;
use App\Models\Product;
use App\Models\SubCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function selectCategory()
    {
        $pageTitle = 'Select Category';

        $categories = Category::active()->whereHas('subCategories', function ($subcategory) {
            $subcategory->active();
        })->with(['subCategories' => function ($subcategory) {
            $subcategory->active();
        }])->get();

        return view($this->activeTemplate . 'user.product.select_category', compact('pageTitle', 'categories'));
    }

    public function upload()
    {
        $categoryId    = request()->category;
        $subCategoryId = request()->sub_category;

        if (!$categoryId || !$subCategoryId) {
            return to_route('user.product.upload.category');
        }

        $categories = Category::active()->whereHas('subCategories', function ($subcategory) {
            $subcategory->active();
        })->with(['subCategories' => function ($subcategory) {
            $subcategory->active();
        }])->get();


        $pageTitle   = 'Upload Product';
        $category    = Category::active()->findOrFail($categoryId);
        $subCategory = SubCategory::active()->where('category_id', $categoryId)->findOrFail($subCategoryId);
        $form        = Form::where('id', $subCategory->form_id)->where('act', 'subcategory_attributes')->first();

        return view($this->activeTemplate . 'user.product.upload', compact('pageTitle', 'form', 'categories'));
    }

    public function edit($slug)
    {
        $product   = Product::where('slug', $slug)->firstOrFail();
        $pageTitle = 'Edit Product';
        $form      = Form::where('id', $product->subCategory->form_id)->where('act', 'subcategory_attributes')->first();

        return view($this->activeTemplate . 'user.product.edit', compact('pageTitle', 'form', 'product'));
    }

    public function saveProduct(Request $request, $id = null)
    {
        $thumbnailWidth  = explode('x', getFileSize('productThumbnail'))[0];
        $thumbnailHeight = explode('x', getFileSize('productThumbnail'))[1];
        $previewHeight   = explode('x', getFileSize('productPreview'))[0];
        $previewWidth    = explode('x', getFileSize('productPreview'))[1];
        $optional        = $id ? 'nullable' : 'required';

        $validationRule = [
            'title'         => 'required',
            'description'   => 'required',
            'category'      => 'required',
            'price'         => 'required|numeric|gt:0',
            'price_cl'      => 'required|numeric|gt:0',
            'thumbnail'     => [$optional, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png']), "dimensions:width=$thumbnailWidth,height=$thumbnailHeight"],
            'preview_image' => [$optional, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png']), "dimensions:width=$previewHeight,height=$previewWidth"],
            'main_file'     => [$optional, new FileTypeValidate(['zip'])],
            'demo_url'      => 'nullable|url'
        ];

        $subcategory        = SubCategory::active()->findOrFail($request->sub_category);
        $category           = Category::active()->findOrFail($request->category);
        $form               = Form::where('id', $subcategory->form_id)->where('act', 'subcategory_attributes')->first();

        $allValidationRule = $validationRule;
        $formProcessor     = null;
        if ($form) {
            $formProcessor      = new FormProcessor();
            $formValidationRule = $formProcessor->valueValidation(@$form->form_data);

            $allValidationRule  = array_merge($allValidationRule, $formValidationRule);
        }

        $request->validate($allValidationRule);

        $product = null;

        if (!$id) {
            $product          = new Product();
            $product->slug    = productSlug($request->title);
            $product->user_id = auth()->id();
        } else {
            $product = Product::findOrFail($id);
        }

        if ($id && $request->hasFile('main_file') && $product->product_updated == Status::PRODUCT_UPDATE_PENDING) {
            $notify[] = ['error', 'You have a pending submission'];
            return back()->withNotify($notify);
        }

        $product->title = $request->title;

        $this->uploadScreenshot($request, $product, $id);
        $this->uploadThumbnail($request, $product);
        $this->uploadPreviewImage($request, $product);
        $this->uploadMainFile($request, $product, $id);

        $attributeInfo            = [];
        if ($formProcessor) {
            $attributeInfo = $formProcessor->processFormData($request, $form->form_data);
        }
        $product->tags            = $request->tags;
        $purifier                 = new \HTMLPurifier();
        $product->description     = htmlspecialchars_decode($purifier->purify($request->description));
        $product->category_id     = $category->id;
        $product->sub_category_id = $subcategory->id;
        $product->price           = $request->price;
        $product->price_cl        = $request->price_cl;
        $product->demo_url        = $request->demo_url;
        $product->attribute_info  = $attributeInfo;
        $product->save();

        if ($request->message) {
            $activity             = new Activity();
            $activity->user_id    = auth()->id();
            $activity->message    = $request->message;
            $activity->product_id = $product->id;
            $activity->save();
        }

        if ($request->hasFile('main_file')) {
            $notify[] = ['info', 'Your submission is under review'];
        }

        $notify[] = ['success', 'Product information saved successfully'];

        return back()->withNotify($notify);
    }

    public function productActivities($slug)
    {
        $pageTitle = 'Activity Log';
        $product   = Product::where('status', '!=', Status::PRODUCT_HARD_REJECTED)->countComment()->where('slug', $slug)->firstOrFail();

        abort_if($product->user_id != auth()->id(), 404);
        $activities = $product->activities()->with(['user', 'reviewer'])->paginate(getPaginate());

        return view($this->activeTemplate . 'user.product.activities', compact('pageTitle', 'product', 'activities'));
    }

    public function replyActivity(Request $request, $productId)
    {
        $request->validate([
            'message' => 'required'
        ]);

        $activity             = new Activity();
        $activity->message    = $request->message;
        $activity->product_id = $productId;
        $activity->user_id    = auth()->id();
        $activity->save();

        $notify[] = ['success', 'Your message submitted successfully'];
        return back()->withNotify($notify);
    }

    private function uploadThumbnail($request, &$product)
    {
        if ($request->hasFile('thumbnail')) {
            try {
                $slug = $product->slug;
                $product->thumbnail = fileUploader(
                    $request->thumbnail,
                    getFilePath('productThumbnail') . '/' . $slug,
                    getFileSize('productThumbnail'),
                    $product->thumbnail ? explode('/', $product->thumbnail)[1] : null
                );
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your preview image'];
                return back()->withInput($request->all())->withNotify($notify);
            }
        }
    }

    private function uploadMainFile($request, &$product, $id)
    {
        if ($request->hasFile('main_file')) {
            try {
                $slug = $product->slug;

                if ($id) {
                    $product->product_updated = Status::PRODUCT_UPDATE_PENDING;
                }

                $product->temp_file = fileUploader(
                    $request->main_file,
                    getFilePath('productFile') . '/' . $slug,
                    null,
                    @$product->temp_file ? explode('/', $product->temp_file)[1] : null
                );
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your file'];
                return back()->withInput($request->all())->withNotify($notify);
            }
        }
    }

    private function uploadPreviewImage($request, &$product)
    {
        if ($request->hasFile('preview_image')) {
            try {
                $slug = $product->slug;

                $product->preview_image = fileUploader(
                    $request->preview_image,
                    getFilePath('productPreview') . '/' . $slug,
                    getFileSize('productPreview'),
                    $product->preview_image ? explode('/', $product->preview_image)[1] : null
                );

                $product->inline_preview_image = fileUploader(
                    $request->preview_image,
                    getFilePath('productInlinePreview') . '/' . $slug,
                    getFileSize('productInlinePreview'),
                    $product->inline_preview_image ? explode('/', $product->inline_preview_image)[1] : null
                );
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your preview image'];
                return back()->withInput($request->all())->withNotify($notify);
            }
        }
    }

    private function uploadScreenshot($request, &$product, $id)
    {
        if ($request->hasFile('screenshots')) {

            try {
                $slug          = $product->slug;
                $zipPath       = $request->file('screenshots')->path();
                $extractedPath = getFilePath('screenshots') . '/' . $slug . '/screenshots';

                $zip = new \ZipArchive;
                $zip->open($zipPath);
                $invalidFile = false;

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

                    if (!in_array($fileExtension, ['png', 'jpg', 'jpeg']) || strpos($filename, '/') != false) {
                        $invalidFile = true;
                        break;
                    }
                }

                if ($invalidFile) {
                    $notify[] = ['error', 'You have to upload images only'];
                    return back()->withInput($request->all())->withNotify($notify);
                }

                fileManager()->makeDirectory($extractedPath);

                // delete old files
                if ($id && is_dir($extractedPath)) {
                    fileManager()->removeDirectory($extractedPath);
                }

                $zip->extractTo($extractedPath);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t extract and upload your screenshots'];
                return back()->withNotify($notify);
            }
        }
    }
}
