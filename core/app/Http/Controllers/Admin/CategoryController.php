<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\Form;
use App\Models\ReviewCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $pageTitle  = 'Categories';
        $categories = Category::searchable(['name'])->paginate(getPaginate());
        return view('admin.product.categories', compact('pageTitle', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|unique:categories,name',
            'image' => ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        $category       = new Category();
        $category->name = $request->name;
        $category->save();

        if ($request->hasFile('image')) {
            try {
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'));
                $category->save();
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['success', 'Category created successfully.'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $id
        ]);

        $category              = Category::findOrFail($id);
        $category->name        = $request->name;
        $category->status      = $request->status ?? 0;

        if ($request->hasFile('image')) {
            try {
                $old = $category->image;
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $category->save();

        $notify[] = ['success', 'Category updated successfully'];
        return back()->withNotify($notify);
    }

    public function toggleFeature($id)
    {
        Category::changeStatus($id, 'featured');
        $notify[] = ['success', 'Featured changed successfully'];
        return back()->withNotify($notify);
    }

    public function reviewCategories()
    {
        $pageTitle        = 'Review Categories';
        $reviewCategories = ReviewCategory::active()->paginate(getPaginate());
        return view('admin.category.review_categories', compact('pageTitle', 'reviewCategories'));
    }

    public function saveReviewCategories(Request $request, $id)
    {
        $request->validate([
            'name' => 'required'
        ]);

        if ($id) {
            $reviewCategory = ReviewCategory::findOrFail($id);
            $notification = 'Review Category updated successfully';
        } else {
            $reviewCategory = new ReviewCategory();
            $notification = 'Review Category added successfully';
        }

        $reviewCategory->name = $request->name;
        $reviewCategory->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function reviewCategoryStatus($id)
    {
        return ReviewCategory::changeStatus($id);
    }

    public function attributes($categoryId)
    {
        $pageTitle = 'Category Attributes';
        $category  = Category::findOrFail($categoryId);
        $form      = Form::where('id', $category->form_id)->where('act', 'category_attributes')->first();
        return view('admin.product.attribute_info', compact('pageTitle', 'form'));
    }

    public function saveAttributes($categoryId)
    {
        $formProcessor = new FormProcessor();
        $category = Category::findOrFail($categoryId);
        $formExists = Form::where('id', $category->form_id)->where('act', 'category_attributes')->exists();
        $form = $formProcessor->generate('category_attributes', $formExists);

        $category->form_id = $form->id;
        $category->save();

        $notify[] = ['success', 'Form updated successfully'];
        return back()->withNotify($notify);
    }
}
