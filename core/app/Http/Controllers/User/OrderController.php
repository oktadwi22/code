<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReviewCategory;

class OrderController extends Controller
{
    public function orderList()
    {
        $pageTitle = 'Purchase History';
        $author    = auth()->user();
        $orders    = $author->orders()->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.orders.list', compact('pageTitle', 'orders', 'author'));
    }

    public function store()
    {
        $cartItems = auth()->user()->cartItems;

        if (count($cartItems) == 0) {
            $notify[] = ['error', 'No items in your cart'];
            return back()->withNotify($notify);
        }
        return redirect()->route('user.deposit.index');
    }

    public function details($orderId)
    {
        $orderBy          = request()->order_by;
        $reviewCategories = ReviewCategory::active()->get();
        $order            = Order::where('user_id', auth()->id())->where('id', $orderId)->firstOrFail();
        $pageTitle        = 'Purchased Item: '.$order->trx;
        $purchasedItems   = OrderItem::where('order_id', $order->id)->latest('id')->searchable(['product:title', 'purchase_code'])->where('is_refunded', Status::NO)->paginate(getPaginate());

        return view($this->activeTemplate . 'user.download', compact('pageTitle', 'purchasedItems', 'reviewCategories'));
    }
}
