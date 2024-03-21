<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Form;
use Illuminate\Http\Request;

class AuthorInfoController extends Controller
{
    public function setting()
    {
        $pageTitle = 'Author Information Setting';
        $form = Form::where('act', 'author_info')->first();
        return view('admin.author.info_form', compact('pageTitle', 'form'));
    }

    public function settingUpdate(Request $request)
    {
        $formProcessor = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'], $generatorValidation['messages']);
        $exist = Form::where('act', 'author_info')->first();
        if ($exist) {
            $isUpdate = true;
        } else {
            $isUpdate = false;
        }
        $formProcessor->generate('author_info', $isUpdate, 'act');

        $notify[] = ['success', 'Author data updated successfully'];
        return back()->withNotify($notify);
    }
}
