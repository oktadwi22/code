<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Page;
use App\Models\Frontend;
use App\Models\Language;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Review;
use Illuminate\Support\Facades\Cookie;

class SiteController extends Controller
{
    public function index()
    {
        $pageTitle = 'Home';
        $sections  = Page::where('tempname', $this->activeTemplate)->where('slug', '/')->first();
        return view($this->activeTemplate . 'home', compact('pageTitle', 'sections'));
    }

    public function pages($slug)
    {
        $page      = Page::where('tempname', $this->activeTemplate)->where('slug', $slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections  = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle', 'sections'));
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        $user      = auth()->user();
        $sections  = Page::where('tempname', $this->activeTemplate)->where('slug', 'contact')->first();
        return view($this->activeTemplate . 'contact', compact('pageTitle', 'user', 'sections'));
    }

    public function products()
    {
        $pageTitle = "Products";
        $request   = request();
        $query     = Product::approved()->allActive();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereJsonContains("tags", $search)
                    ->orWhere("title", "Like", "%" . $search . "%")
                    ->orWhereHas('author', function ($qq) use ($search) {
                        $qq->where("username", "Like", "%" . $search . "%");
                    });
            });
        }

        
        if ($request->min_price) $query->where('price', '>=', $request->min_price - gs()->personal_buyer_fee);
        if ($request->max_price) $query->where('price', '<=', $request->max_price - gs()->personal_buyer_fee);
        if ($request->category && $request->category != 'all') $query->where('category_id', $request->category);
        if ($request->sub_category) $query->where('sub_category_id', $request->sub_category);
        if ($request->rating && $request->rating != 'all') $query->where('avg_rating', $request->rating);

        if ($request->date_range && $request->date_range != 'all') {
            $dateRange = $request->date_range;
            $query->where('created_at', '>=', now()->subDays($dateRange));
        }

        if ($request->sort_by) {
            $sortBy     = $request->sort_by;
            $query->when($sortBy == 'best_selling', function ($query) {
                $query->orderByDesc('total_sold');
            })->when($sortBy == 'best_rated', function ($query) {
                $query->orderByDesc('avg_rating');
            })->when($sortBy == 'new_item', function ($query) {
                $query->orderByDesc('created_at');
            });
        }

        $products          = $query->with(['reviews', 'users', 'author'])->orderBy('id', 'desc')->paginate(getPaginate(12));
        $productsAnyDate   = Product::approved()->count();
        $productsLastYear  = Product::approved()->where('created_at', '>=', Carbon::now()->subYear())->count();
        $productsLastMonth = Product::approved()->where('created_at', '>=', Carbon::now()->subMonth())->count();
        $productsLastWeek  = Product::approved()->where('created_at', '>=', Carbon::now()->subWeek())->count();
        $productsLastDay   = Product::approved()->where('created_at', '>=', Carbon::now()->subDay())->count();
        $ratings           = Rating::get();

        $categories        = Category::active()->get();

        return view(
            $this->activeTemplate . 'products',
            compact(
                'pageTitle',
                'categories',
                'products',
                'ratings',
                'productsAnyDate',
                'productsLastYear',
                'productsLastMonth',
                'productsLastWeek',
                'productsLastDay'
            )
        );
    }

    public function productDetails($slug)
    {
        $product = Product::with('author')->countComment()->where(['slug' => $slug])->firstOrFail();

        if (in_array($product->status, [Status::PRODUCT_PERMANENT_DOWN, Status::PRODUCT_HARD_REJECTED])) {
            abort(404);
        }

        abort_if(!$product->my_product && $product->status != Status::PRODUCT_APPROVED, 404);

        $author    = $product->author;
        $pageTitle = $product->title;
        $seoContents['keywords']           = Frontend::where('data_keys', 'seo.data')->first('data_values')?->data_values->keywords;

        $seoContents['social_title']       = $product->title;
        $seoContents['description']        = strLimit(strip_tags($product->description), 150);
        $seoContents['social_description'] = strLimit(strip_tags($product->description), 150);
        $seoContents['image']              = getImage(getFilePath('productPreview') . '/' . productFilePath($product, 'preview_image'), getFileSize('productPreview'));
        $seoContents['image_size']         = getFileSize('productPreview');

        return view($this->activeTemplate . 'product_details', compact('pageTitle', 'product', 'author', 'seoContents'));
    }

    public function productReviews($slug)
    {
        $product = Product::with(['author'])->countComment()->where(['slug' => $slug])->firstOrFail();
        abort_if($product->status == !Status::PRODUCT_APPROVED && !$product->my_product, 404);

        $reviewId = request()->review_id;
        $reviews = Review::where(['product_id' => $product->id])->with(['user', 'replies', 'category']);
        if ($reviewId)
            $reviews->where('id', $reviewId);
        $reviews = $reviews->paginate(getPaginate());
        $pageTitle = 'Reviews';
        return view($this->activeTemplate . 'product_reviews', compact('pageTitle', 'product', 'reviews'));
    }

    public function productComments($slug)
    {
        $product = Product::with(['author'])->countComment()->where(['slug' => $slug])->firstOrFail();

        abort_if($product->status == !Status::PRODUCT_APPROVED && !$product->my_product, 404);

        $commentId = request()->comment_id;
        $comments = Comment::where(['product_id' => $product->id, 'parent_id' => 0, 'review_id' => 0])
            ->when($commentId, function ($query) use ($commentId) {
                $query->where('id', $commentId);
            })
            ->with(['user', 'user.orderItems', 'product', 'replies' => function ($replyQuery) {
                $replyQuery->with('user');
            }])->paginate(getPaginate());
        $pageTitle = 'Comments';

        return view($this->activeTemplate . 'product_comments', compact('pageTitle', 'product', 'comments'));
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];
        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug, $id)
    {
        $policy = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        return view($this->activeTemplate . 'policy', compact('policy', 'pageTitle'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language)
        $lang = 'en';
        session()->put('lang', $lang);
        return back();
    
    }

    public function cookieAccept()
    {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy()
    {
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        return view($this->activeTemplate . 'cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font/RobotoMono-Regular.ttf');
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX = ($imgWidth - $textWidth) / 2;
        $textY = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view($this->activeTemplate . 'maintenance', compact('pageTitle', 'maintenance'));
    }
}
