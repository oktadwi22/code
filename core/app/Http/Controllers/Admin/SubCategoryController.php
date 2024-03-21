<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\Form;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        $pageTitle     = 'Sub Categories';
        $subCategories = SubCategory::searchable(['name', 'category:name'])->with('category', 'reviewers')->paginate(getPaginate());
        $categories    = Category::active()->get();
        return view('admin.product.sub_categories', compact('pageTitle', 'subCategories', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name'        => 'required|unique:sub_categories,name'
        ]);

        $subCategory              = new SubCategory();
        $subCategory->category_id = $request->category_id;
        $subCategory->name        = $request->name;
        $subCategory->save();

        $notify[] = ['success', 'Subcategory created successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'name'        => 'required|unique:sub_categories,name,' . $id
        ]);

        $subCategory              = SubCategory::findOrFail($id);
        $subCategory->category_id = $request->category_id;
        $subCategory->status      = $request->status;
        $subCategory->name        = $request->name;
        $subCategory->save();

        $notify[] = ['success', 'Subcategory updated successfully'];
        return back()->withNotify($notify);
    }

    public function attributes($categoryId)
    {
        $pageTitle   = 'Category Attributes';
        $subCategory = SubCategory::findOrFail($categoryId);
        $form        = Form::where('id', $subCategory->form_id)->where('act', 'subcategory_attributes')->first();

        return view('admin.product.attribute_info', compact('pageTitle', 'form', 'subCategory'));
    }

    public function syncReviewers(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->reviewers()->sync($request->reviewer_id);
        $notify[] = ['success', 'Reviewers updated successfully'];
        return back()->withNotify($notify);
    }

    public function saveAttributes($categoryId)
    {
        $formProcessor = new FormProcessor();
        $subCategory   = SubCategory::findOrFail($categoryId);
        $formExists    = Form::where('id', $subCategory->form_id)->where('act', 'subcategory_attributes')->exists();

        $form                 = $formProcessor->generate('subcategory_attributes', $formExists,'id');
        $subCategory->form_id = $form->id;
        $subCategory->save();

        $notify[] = ['success', 'Form updated successfully'];
        return back()->withNotify($notify);
    }
}
