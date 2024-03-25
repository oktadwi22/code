<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductCollection;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $pageTitle = 'Cart';
        $sessionId = request()->session()->getId();
        $cartItems = [];

        if (auth() && auth()->check()) {
            $cartItems = Cart::where('user_id', auth()->id())->with('product','product.author')->get();
        } else {
            $cartItems = Cart::where('session_id', $sessionId)->with('product','product.author')->get();
        }

        return view($this->activeTemplate . 'cart', compact('pageTitle', 'cartItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'license' => 'required',
            'product_id' => 'required|exists:products,id'
        ]);

        if(!in_array($request->license, [1,2])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid license']);
        }

        $cartItems   = $request->session()->get('cart', []);
        $product     = Product::approved()->find($request->product_id);
        $newCartItem = $this->getDataForCart($product, null, null, $request);

        if (!$newCartItem) {
            $response = ['status' => 'error', 'message' => 'Item already added to cart'];
            return $request->ajax() ? response()->json($response) : back()->withNotify([$response]);
        }

        $cartItems[] = $newCartItem;

        $request->session()->put('cart', $cartItems);

        $response = ['status' => 'success', 'message' => 'Item added to cart'];
        return $request->ajax() ? response()->json($response) : back()->withNotify([$response]);
    }

    public function delete($productId)
    {
        $cart = Cart::where('product_id', $productId)->first();

        if (!$cart) {
            $status  = 'error';
            $message = 'Item not found';
        } else {
            $status  = 'success';
            $message = 'Item removed from cart';
            $cart->delete();
        }

        $response = ['status' => $status, 'message' => $message];
        return request()->ajax() ? response()->json($response): back()->withNotify([$response]);
    }

    private function getDataForCart($product, $license = null, $isExtended = 0, $request = null)
    {
        
        $productId         = $product->id;
        $license           = request()->license ?? Status::PERSONAL_LICENSE;
        $isPersonalLicense = $license == Status::PERSONAL_LICENSE;

        $existingCartItem = Cart::where('product_id', $productId);

        if (auth()->check())
            $existingCartItem->where('user_id', auth()->id());
        else {
            $sessionId = $request->session()->getId();
            $existingCartItem->where('session_id', $sessionId);
        }

        $existingCartItem = $existingCartItem->first();
        
        if ($existingCartItem) {
            return false;
        }
        $cart                  = new Cart();
        $cart->product_id      = $product->id;
        $cart->title           = $product->title;
        $cart->category_id     = $product->category_id;
        $cart->category        = @$product->category->name;
        $cart->license         = $license;
        $cart->is_extended     = $isExtended ?? 0;
        $cart->extended_amount = $cart->is_extended ? gs()->twelve_month_extended_fee : 0;
        $cart->price           = $isPersonalLicense ? $product->price : $product->price_cl;
        $cart->buyer_fee       = $isPersonalLicense ? gs()->personal_buyer_fee : gs()->commercial_buyer_fee;
        $cart->quantity        = 1;

        $cart->user_id    = auth()->check() ? auth()->id() : null;
        $cart->session_id = request()->session()->getId();
        $cart->save();

        return $cart->toArray();
    }


    public function collectionToCart($collectionId)
    {
        $collection = ProductCollection::findOrFail($collectionId);
        $products = $collection->products;

        $cartItems = request()->session()->get('cart', []);

        foreach ($products as $product) {
            $newCartItem = $this->getDataForCart($product);
            $cartItems[] = $newCartItem;

            request()->session()->put('cart', $cartItems);
        }

        $notify[] = ['success', 'Items added to cart'];
        return back()->withNotify($notify);
    }

    public function toggleExtended($id)
    {
        $cart = Cart::find($id);

        if (!$cart) {
            return response()->json(['status' => 'error', 'message' => 'Item not found']);
        }

        $cart->is_extended = !$cart->is_extended;
        $cart->extended_amount = $cart->is_extended ? gs()->twelve_month_extended_fee : 0;
        $cart->save();

        return response()->json(['status' => 'success', 'message' => 'Cart item updated']);
    }
}
