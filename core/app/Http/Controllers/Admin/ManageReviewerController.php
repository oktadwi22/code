<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Reviewer;
use App\Models\SubCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ManageReviewerController extends Controller
{
    public function allReviewers()
    {
        $pageTitle = 'All Reviewers';
        $reviewers = $this->reviewerData();
        $subCategories = SubCategory::active()->get();
        return view('admin.reviewers.list', compact('pageTitle', 'reviewers', 'subCategories'));
    }

    public function approvedProducts($id)
    {
        $reviewer  = Reviewer::findOrFail($id);
        $products  = $reviewer->approvedProducts()->with(['category', 'subCategory'])->paginate(getPaginate());
        $pageTitle = 'Approved Products';
        return view('admin.reviewers.approved_products', compact('pageTitle', 'products'));
    }

    public function syncSubcategories(Request $request, $id)
    {
        $reviewer = Reviewer::findOrFail($id);
        $reviewer->subcategories = $request->subcategory_id;
        $reviewer->save();
        $notify[] = ['success', 'Subcategories Updated For Reviewer'];
        return back()->withNotify($notify);
    }

    protected function reviewerData($scope = null)
    {
        $reviewers = Reviewer::with(['subCategories'])->withCount('approvedProducts');
        if ($scope) {
            $reviewers = $reviewers->$scope();
        }

        if (request()->author_id) $reviewers = $reviewers->where('author_id', request()->author_id);
        return $reviewers->searchable(['name', 'email', 'username'])->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function status(Request $request, $id)
    {
        
        $reviewer = Reviewer::findOrFail($id);

        if ($reviewer->status == Status::USER_ACTIVE) {
            $request->validate([
                'reason' => 'required|string|max:255'
            ]);
            $reviewer->status = Status::USER_BAN;
            $reviewer->ban_reason = $request->reason;
            notify($reviewer, 'REVIEWER_BANNED', ['reason' => $request->reason]);
            $notify[] = ['success', 'Reviewer banned successfully'];
        } else {
            $reviewer->status = Status::USER_ACTIVE;
            $reviewer->ban_reason = null;
            $notify[] = ['success', 'Reviewer unbanned successfully'];
        }
        $reviewer->save();
        return back()->withNotify($notify);
    }

    public function save(Request $request, $id = 0)
    {
        $isRqueird = $id ? 'nullable' : 'required';

        $request->validate([
            'image'    => [$isRqueird, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'name'     => 'required',
            'username' => "$isRqueird|unique:reviewers,username,$id",
            'email'    => "$isRqueird|email|unique:reviewers,email,$id",
            'password' => $isRqueird,
        ]);

        if ($id) {
            $reviewer = Reviewer::findOrFail($id);
            $notification = 'Reviewer updated successfully';
        } else {
            $reviewer = new Reviewer();
            $notification = 'Reviewer added successfully';
        }

        if ($request->hasFile('image')) {
            try {
                $old = $reviewer->image;
                $reviewer->image = fileUploader($request->image, getFilePath('reviewerProfile'), getFileSize('reviewerProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        if ($request->password) {
            $reviewer->password = bcrypt($request->password);
        }

        $reviewer->name = $request->name;
        $reviewer->email = $request->email;
        $reviewer->username = $request->username;
        $reviewer->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
}
