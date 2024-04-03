<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AuthorLevel;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReviewCategory;
use App\Models\Transaction;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orderList()
    {
        $pageTitle = 'Purchase History';
        $author    = auth()->user();
        $orders    = $author->orders()->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.orders.list', compact('pageTitle', 'orders', 'author'));
    }

    public function store(Request $request)
    {
        // $cartItems = auth()->user()->cartItems;

        // if (count($cartItems) == 0) {
        //     $notify[] = ['error', 'No items in your cart'];
        //     return back()->withNotify($notify);
        // }
        // return redirect()->route('user.deposit.index');

        $paymentType = 1;
        $user        = auth()->user();
        $cartItems   = $user->cartItems;

        // if ($paymentType == Status::ACCOUNT_BALANCE &&  getCartAmount($cartItems) > auth()->user()->balance) {
        //     $notify[] = ['error', 'Insufficient balance'];
        //     return back()->withNotify($notify);
        // }

        $order       = $this->createOrder($cartItems);

        if ($paymentType == Status::ACCOUNT_BALANCE) {
            return $this->orderFromAccountBalance($request, $order);
        }

        // $gate = GatewayCurrency::whereHas('method', function ($gate) {
        //     $gate->where('status', Status::ENABLE);
        // })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();

        // if ($paymentType == Status::GATEWAY && !$gate) {
        //     $notify[] = ['error', 'Invalid gateway'];
        //     return back()->withNotify($notify);
        // }

        // if ($paymentType == Status::GATEWAY && ($gate->min_amount > $order->amount || $gate->max_amount < $order->amount)) {
        //     $notify[] = ['error', 'Please follow deposit limit'];
        //     return back()->withNotify($notify);
        // }

        // $charge    = $paymentType == Status::ACCOUNT_BALANCE ? 0 :  $gate->fixed_charge + ($order->amount * $gate->percent_charge / 100);
        // $payable   = $order->amount + $charge;

        // $finalAmount  = $payable * ($gate->rate ?? 1);

        // $data                  = new Deposit();
        // $data->user_id         = $user->id;
        // $data->order_id        = $order->id;
        // $data->method_code     = @$gate->method_code ?? 0;
        // $data->method_currency = @$gate->currency ? strtoupper($gate->currency) : strtoupper(gs()->cur_text);
        // $data->amount          = $order->amount;
        // $data->charge          = $charge;
        // $data->rate            = @$gate->rate ?? 0;
        // $data->final_amount    = $finalAmount;
        // $data->btc_amo         = 0;
        // $data->btc_wallet      = "";
        // $data->trx             = $order->trx;
        // $data->save();

        // session()->put('Track', $data->trx);
        // return to_route('user.deposit.confirm');
    }

    private function createOrder($cartItems)
    {
        $amount         = collect($cartItems)->sum('price');
        $extendedAmount = collect($cartItems)->sum('extended_amount');
        $buyerFees      = collect($cartItems)->sum('buyer_fee');
        $amount += $extendedAmount;
        $amount += $buyerFees;

        $order          = new Order();
        $order->user_id = auth()->id();
        $order->amount  = $amount;
        $order->trx     = getTrx();
        $order->save();


        foreach ($cartItems as $cartItem) {

            $author                      = $cartItem->product->author;
            $authorLevel                 = $author->authorLevels()->orderBy('minimum_earning', 'desc')->first();
            if (!$authorLevel) $authorLevel = AuthorLevel::active()->orderBy('minimum_earning')->first();

            $sellerFee = @$authorLevel->fee ?? 0;
            $sellerFee = ($sellerFee / 100) * $cartItem->price;

            $orderItem                  = new OrderItem();
            $orderItem->user_id         = $order->user_id;
            $orderItem->order_id        = $order->id;
            $orderItem->purchase_code   = getPurchaseCode();
            $orderItem->product_id      = $cartItem->product_id;
            $orderItem->is_extended     = $cartItem->is_extended;
            $orderItem->extended_amount = $cartItem->is_extended ? $cartItem->extended_amount : 0;
            $orderItem->product_price   = $cartItem->price;
            $orderItem->buyer_fee       = $cartItem->buyer_fee;
            $orderItem->seller_fee      = $sellerFee;
            $orderItem->quantity        = $cartItem->quantity;
            $orderItem->license         = $cartItem->license;
            $orderItem->seller_earning  = ($cartItem->price - $sellerFee) + $cartItem->extended_amount;
            $orderItem->save();
        }

        return $order;
    }

    private function orderFromAccountBalance($request, $order)
    {
        $order->payment_status = Status::PAYMENT_SUCCESS;
        $order->save();

        $buyer           = $order->user;
        $buyer->balance -= $order->amount;
        $buyer->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $buyer->id;
        $transaction->amount       = $order->amount;
        $transaction->post_balance = $buyer->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = 'New Item Purchase';
        $transaction->trx          = $order->trx;
        $transaction->remark       = 'purchase';
        $transaction->save();

        $authorTransactions = [];
        foreach ($order->orderItems as $orderItem) {
            $sellerEarning       = ($orderItem->product_price + $orderItem->extended_amount);
            $author              = $orderItem->product->author;
            $author->balance    += $sellerEarning;
            $author->total_sold += 1;
            $author->save();

            $product = $orderItem->product;
            $product->total_sold += 1;
            $product->save();

            // give seller amount
            $authorTransactions[] = [
                'user_id'      => $author->id,
                'trx_type'     => '+',
                'trx'          => $order->trx,
                'remark'       => "new_sale",
                'details'      => 'Sale amount added',
                'amount'       => $sellerEarning,
                'post_balance' => $author->balance,
                'created_at'   => now()
            ];

            // substract seller fee
            $author->balance           -= $orderItem->seller_fee;
            $author->total_sold_amount += ($sellerEarning - $orderItem->seller_fee); // excluding seller fee
            $author->save();

            if ($orderItem->seller_fee > 0) {
                $authorTransactions[]       = [
                    'user_id'      => $author->id,
                    'trx_type'     => '-',
                    'trx'          => $order->trx,
                    'remark'       => 'seller_fee',
                    'details'      => 'Seller fee subtracted',
                    'amount'       => $orderItem->seller_fee,
                    'post_balance' => $author->balance,
                    'created_at'   => now()
                ];
            }

            $authorLevels = AuthorLevel::active()->where('minimum_earning', '<=', $author->total_sold_amount)->pluck('id')->toArray();
            $author->authorLevels()->sync($authorLevels);
        }

        Transaction::insert($authorTransactions);
        session()->forget('cart');
        session()->forget('Track');
        Cart::where('user_id', $order->user_id)->delete();

        $notify[] = ['success', 'Order Completed Successfully'];
        $request->session()->flash('notify', $notify);
        return response('Success');
        // return redirect('/cart');
        // return to_route('user.order.list')->withNotify($notify);
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
