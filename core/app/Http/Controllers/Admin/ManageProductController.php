<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ManageProductController extends Controller
{
    public function details($slug)
    {
        $pageTitle = 'Product Details';
        $product = Product::where('slug', $slug)->firstOrFail();;
        return view('admin.product.details', compact('pageTitle', 'product'));
    }

    public function pending()
    {
        $pageTitle = 'Pending Products';
        $products  = $this->productData('pending');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function softRejected()
    {
        $pageTitle = 'Soft Rejected Products';
        $products  = $this->productData('softRejected');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function hardRejected()
    {
        $pageTitle = 'Hard Rejected Products';
        $products  = $this->productData('hardRejected');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function down()
    {
        $pageTitle = 'Soft Disabled Products';
        $products  = $this->productData('down');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function permanentDown()
    {
        $pageTitle = 'Permanent Disabled Products';
        $products  = $this->productData('permanentDown');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function approved()
    {
        $pageTitle = 'Approved Products';
        $products = $this->productData('approved');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function all()
    {
        $pageTitle = 'All Products';
        $products = $this->productData();
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    private function productData($scope = null)
    {
        $products = $scope ? Product::$scope() : Product::query();
        if (request()->author_id) $products = $products->where('user_id', request()->author_id);
        $products = $products->with(['author', 'category', 'subCategory'])->searchable(['title', 'author:username'])->orderBy('id', 'desc')->paginate(getPaginate());
        return $products;
    }

    public function toggleFeature($id)
    {
        $product              = Product::findOrFail($id);

        if ($product->status != Status::PRODUCT_APPROVED) {
            $notify[] = ['error', 'Product is not approved'];
            return back()->withNotify($notify);
        }

        $product->is_featured = !$product->is_featured;
        $product->save();
        $notify[] = ['success', 'Featured changed successfully'];
        return back()->withNotify($notify);
    }
}
