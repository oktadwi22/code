<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;

class SellController extends Controller
{
    public function allSales()
    {
        $pageTitle = "All Items for sale";
        $allSales  = Order::latest('id')->paid()->dateFilter()->searchable(['trx', "user:username"])->withCount('orderItems')->with('product.author', 'user')->paginate(getPaginate());
        return view('admin.sale.all', compact('allSales', 'pageTitle'));
    }

    public function refundedItems()
    {
        $pageTitle = 'Refunded Items';
        $sales     = OrderItem::latest('id')->searchable(['purchase_code', 'product:title'])->dateFilter()->refunded()->with(['product.author', 'order', 'order.user', 'buyer', 'product'])->paginate(getPaginate());
        return view('admin.sale.list', compact('sales', 'pageTitle'));
    }

    public function saleItems()
    {
        $pageTitle = 'Item for sale';
        $sales     = OrderItem::searchable(['purchase_code', 'product:title', 'product.author:username'])
            ->dateFilter()
            ->latest('id')
            ->with(['product.author', 'order', 'order.user', 'buyer', 'product'])
            ->whereHas('product', function ($query) {
                if (request()->author_id) {
                    $query->where('user_id', request()->author_id);
                }
            });

        $sales = $sales->paginate(getPaginate());
        return view('admin.sale.list', compact('sales', 'pageTitle'));
    }

    public function details($id)
    {
        $pageTitle = 'Sale Details';
        $sales     = OrderItem::where('order_id', $id)->with('product.author', 'buyer')->paginate(getPaginate());
        return view('admin.sale.list', compact('pageTitle', 'sales'));
    }
}
