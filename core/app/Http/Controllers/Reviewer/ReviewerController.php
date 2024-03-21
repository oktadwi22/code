<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ReviewerController extends Controller
{
    public function dashboard()
    {
        $pageTitle    = 'Reviewer Dashboard';
        $reviewer     = auth('reviewer')->user();
        $productQuery = Product::whereIn('sub_category_id', $reviewer->subcategories ?? []);

        $widget['pending_products']       = (clone $productQuery)->whereIntegerInRaw('assigned_to', [0, auth('reviewer')->id()])->pending()->count();
        $widget['soft_rejected_products'] = (clone $productQuery)->where('assigned_to', $reviewer->id)->softRejected()->count();
        $widget['hard_rejected_products'] = (clone $productQuery)->where('assigned_to', $reviewer->id)->hardRejected()->count();
        $widget['updated_products']       = (clone $productQuery)->where('assigned_to', $reviewer->id)->fileUpdated()->count();
        $widget['assigned_products']      = (clone $productQuery)->where('assigned_to', $reviewer->id)->count();
        $widget['approved_products']      = (clone $productQuery)->where('assigned_to', $reviewer->id)->approved()->count();

        return view('reviewer.dashboard', compact('pageTitle', 'widget'));
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $reviewer = auth('reviewer')->user();
        return view('reviewer.profile', compact('pageTitle', 'reviewer'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);
        $user = auth('reviewer')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('reviewerProfile'), getFileSize('reviewerProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return to_route('reviewer.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $reviewer = auth('reviewer')->user();
        return view('reviewer.password', compact('pageTitle', 'reviewer'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = auth('reviewer')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('reviewer.password')->withNotify($notify);
    }

}
