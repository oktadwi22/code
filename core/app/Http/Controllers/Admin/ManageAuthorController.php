<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\User;

class ManageAuthorController extends Controller
{
    public function list()
    {
        $pageTitle = 'Authors';
        $authors   = User::active()->where('is_author', Status::YES)->searchable(['username', 'email']);
        $authors   = $authors->paginate(getPaginate());
        return view('admin.author.list', compact('pageTitle', 'authors'));
    }
}
