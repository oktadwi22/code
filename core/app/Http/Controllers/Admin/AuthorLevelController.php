<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AuthorLevel;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class AuthorLevelController extends Controller
{
    public function list()
    {
        $pageTitle    = 'Author Level List';
        $authorLevels = AuthorLevel::orderBy('minimum_earning')->searchable(['name'])->paginate(getPaginate());
        return view('admin.author_level.list', compact('pageTitle', 'authorLevels'));
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'image'           => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'name'            => 'required',
            'minimum_earning' => 'required|numeric|gt:-1',
            'fee'             => 'required|numeric|between:1,100',
        ]);

        if ($id) {
            $authorLevel  = AuthorLevel::findOrFail($id);
            $notification = 'Author level updated successfully';
        } else {
            $authorLevel  = new AuthorLevel();
            $notification = 'Author level added successfully';
        }

        if ($request->hasFile('image')) {
            try {
                $old                = $authorLevel->image;
                $authorLevel->image = fileUploader($request->image, getFilePath('authorLevel'), getFileSize('authorLevel'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $authorLevel->name            = $request->name;
        $authorLevel->minimum_earning = $request->minimum_earning;
        $authorLevel->fee             = $request->fee;
        $authorLevel->details         = $request->details;
        $authorLevel->save();

        $authors = User::active()->where('is_author', Status::YES)->get();

        $authors->each(function ($author) use ($authorLevel) {
            $minimumEarning = $author->balance;

            $eligibleLevels = AuthorLevel::active()
                ->where('minimum_earning', '<=', $minimumEarning)
                ->pluck('id')
                ->toArray();

            if (in_array($authorLevel->id, $eligibleLevels) && !$author->authorLevels->contains($authorLevel->id)) {
                $author->authorLevels()->attach($authorLevel->id);
            }
        });
        
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return AuthorLevel::changeStatus($id);
    }

}
