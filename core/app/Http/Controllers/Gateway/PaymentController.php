<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\AuthorLevel;
use App\Models\Cart;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PaymentController extends Controller
{
    public function deposit()
    {

        $amount = getCartAmount(auth()->user()->cartItems);

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();
        $pageTitle = 'Deposit Methods';

        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'amount'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:1,2',
            'gateway'      => 'required_if:payment_type,2',
            'currency'     => 'required_if:payment_type,2'
        ], [
            'gateway.required_if' => 'The gateway field is required',
            'currency.required_if' => 'The currency field is required',
        ]);

        if (session('Track')) {
            return to_route('user.deposit.confirm');
        }

        $paymentType = $request->payment_type;
        $user        = auth()->user();
        $cartItems   = $user->cartItems;

        if ($paymentType == Status::ACCOUNT_BALANCE &&  getCartAmount($cartItems) > auth()->user()->balance) {
            $notify[] = ['error', 'Insufficient balance'];
            return back()->withNotify($notify);
        }

        $order       = $this->createOrder($cartItems);

        if ($paymentType == Status::ACCOUNT_BALANCE) {
            return $this->orderFromAccountBalance($order);
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();

        if ($paymentType == Status::GATEWAY && !$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($paymentType == Status::GATEWAY && ($gate->min_amount > $order->amount || $gate->max_amount < $order->amount)) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge    = $paymentType == Status::ACCOUNT_BALANCE ? 0 :  $gate->fixed_charge + ($order->amount * $gate->percent_charge / 100);
        $payable   = $order->amount + $charge;

        $finalAmount  = $payable * ($gate->rate ?? 1);

        $data                  = new Deposit();
        $data->user_id         = $user->id;
        $data->order_id        = $order->id;
        $data->method_code     = @$gate->method_code ?? 0;
        $data->method_currency = @$gate->currency ? strtoupper($gate->currency) : strtoupper(gs()->cur_text);
        $data->amount          = $order->amount;
        $data->charge          = $charge;
        $data->rate            = @$gate->rate ?? 0;
        $data->final_amount    = $finalAmount;
        $data->btc_amo         = 0;
        $data->btc_wallet      = "";
        $data->trx             = $order->trx;
        $data->save();

        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    private function orderFromAccountBalance($order)
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
        return to_route('user.order.list')->withNotify($notify);
    }

    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            return "Sorry, invalid URL.";
        }

        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)
            ->orderBy('id', 'DESC')->with('gateway')->first();

        if (!$deposit) {
            session()->forget('Track');
            return back();
        }

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }

        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit, $isManual = null)
    {

        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = $deposit->user;
            $user->balance += $deposit->amount;
            $user->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge       = $deposit->charge;
            $transaction->trx_type     = '+';
            $paymentVia                = $deposit->from_account_balance ? 'Account Balance' :  $deposit->gatewayCurrency()->name;
            $transaction->details      = 'Payment for Purchase Via ' . $paymentVia;
            $transaction->trx          = $deposit->trx;
            $transaction->remark       = 'payment';
            $transaction->save();


            if ($deposit->order_id) {
                $order = $deposit->order;
                $order->payment_status = Status::PAYMENT_SUCCESS;
                $order->save();

                $user->balance -= $deposit->amount;
                $user->save();

                $transaction               = new Transaction();
                $transaction->user_id      = $deposit->user_id;
                $transaction->amount       = $order->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge       = 0;
                $transaction->trx_type     = '-';
                $transaction->details      = 'Payment for Purchase Item';
                $transaction->trx          = $deposit->trx;
                $transaction->remark       = 'purchase';
                $transaction->save();

                $authorTransactions = [];

                foreach ($order->orderItems as $orderItem) {

                    $author           = $orderItem->product->author;
                    $sellerEarning    = $orderItem->product_price + $orderItem->extended_amount;
                    $author->balance += $sellerEarning;
                    $author->save();

                    $product = $orderItem->product;
                    $product->total_sold += 1;
                    $product->save();

                    // give seller amount
                    $authorTransactions[] = [
                        'user_id'      => $author->id,
                        'trx_type'     => '+',
                        'trx'          => $order->trx,
                        'remark'       => 'new_sale',
                        'details'      => 'Sale Amount Added',
                        'amount'       => $sellerEarning,
                        'post_balance' => $author->balance
                    ];

                    // cut seller fee
                    $author->balance           -= $orderItem->seller_fee;
                    $author->total_sold_amount += ($sellerEarning - $orderItem->seller_fee);  // excluding seller fee
                    $author->save();

                    if ($orderItem->seller_fee) {
                        $authorTransactions[] = [
                            'user_id'      => $author->id,
                            'trx_type'     => '-',
                            'trx'          => $order->trx,
                            'remark'       => 'seller_fee',
                            'details'      => 'Seller Fee Subtracted',
                            'amount'       => $orderItem->seller_fee,
                            'post_balance' => $author->balance
                        ];
                    }

                    $author->total_sold += 1;
                    $author->save();

                    $authorLevels = AuthorLevel::active()->where('minimum_earning', '<=', $author->total_sold_amount)->pluck('id')->toArray();
                    $author->authorLevels()->sync($authorLevels);
                }

                Transaction::insert($authorTransactions);
                session()->forget('cart');
                session()->forget('Track');
                Cart::where('user_id', $deposit->user_id)->delete();
            }

            if (!$isManual) {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $deposit->user_id;
                $adminNotification->title     = 'Payment Successful Via ' . $deposit->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'PAYMENT_APPROVE' : 'PAYMENT_COMPLETE', [
                'method_name'     => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amount),
                'amount'          => showAmount($deposit->amount),
                'charge'          => showAmount($deposit->charge),
                'rate'            => showAmount($deposit->rate),
                'trx'             => $deposit->trx,
                'post_balance'    => showAmount($user->balance)
            ]);
        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        if ($data->method_code > 999) {
            $pageTitle = 'Payment Confirm';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view($this->activeTemplate . 'user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }

        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form->form_data;

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData       = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Payment Request From ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'PAYMENT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amount),
            'amount'          => showAmount($data->amount),
            'charge'          => showAmount($data->charge),
            'rate'            => showAmount($data->rate),
            'trx'             => $data->trx
        ]);

        Cart::where('user_id', auth()->id())->delete();
        session()->forget('Track');
        session()->forget('cart');

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }

    /**
     * Create a new order
     * @param Collection $cartItems
     * @return Order
     */
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
}
