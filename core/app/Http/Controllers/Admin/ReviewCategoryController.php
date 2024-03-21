<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewCategory;
use Illuminate\Http\Request;

class ReviewCategoryController extends Controller
{
    public function index()
    {
        $pageTitle = 'Review Catgories';
        $reviewCategories = ReviewCategory::searchable(['name'])->paginate(getPaginate());
        return view('admin.product.review_categories', compact('pageTitle', 'reviewCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:review_categories,name'
        ]);

        $reviewCategory       = new ReviewCategory();
        $reviewCategory->name = $request->name;
        $reviewCategory->save();

        $notify[] = ['success', 'Review category created'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:review_categories,name,' . $id
        ]);

        $reviewCategory = ReviewCategory::findOrFail($id);
        $reviewCategory->name = $request->name;
        $reviewCategory->save();

        $notify[] = ['success', 'Review category updated'];
        return back()->withNotify($notify);
    }

    public function toggleStatus($id)
    {
        return ReviewCategory::changeStatus($id);
    }
}
