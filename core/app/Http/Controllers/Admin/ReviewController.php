<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Review;
use App\Models\User;

class ReviewController extends Controller
{
    public function index()
    {
        $pageTitle = 'Reviews';
        $reviews = Review::latest()->with(['product']);
        if (request()->author_id) $reviews->where('author_id', request()->author_id);
        $reviews = $reviews->paginate(getPaginate());
        return view('admin.review.index', compact('pageTitle', 'reviews'));
    }

    public function  destroy($id)
    {

        $review              = Review::findOrFail($id);
        $product             = $review->product;
        $review->delete();

        $user               = User::findOrFail($product->user_id);
        $user->total_review = $user->reviews()->count();
        $user->avg_rating   = $user->reviews()->avg('rating');
        $user->save();

        $product->total_review = $product->reviews()->count();
        $product->avg_rating   = $product->reviews()->avg('rating');
        $product->save();


        $rating = Rating::where('value', '>=', @$product->avg_rating ?? 0)->first() ?? Rating::orderBy('value', 'desc')->first();

        if($rating){
            $rating->product_count -= 1;
            if ($rating->product_count == 0) $rating->product_count = 0;
            $rating->save();
        }

        $notify[] = ['success', 'Review deleted successfully'];
        return back()->withNotify($notify);
    }
}
