<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $pageTitle = 'Comments';
        $comments  = Comment::where('review_id', 0)->with(['user', 'product'])->paginate(getPaginate());
        return view('admin.comment.index', compact('pageTitle', 'comments'));
    }

    public function destroy($id) {
        $comment  = Comment::findOrfail($id);
        $comment->delete();
        $notify[] = ['success', 'Comment deleted'];
        return back()->withNotify($notify);
    }

    public function repliesList($commentId) {
        $pageTitle = 'Replies';
        $comment   = Comment::findOrFail($commentId);
        $replies   = $comment->replies()->with(['user', 'product'])->paginate(getPaginate());
        return view('admin.comment.reply.index', compact('pageTitle', 'replies'));
    }

    public function deleteReply($id) {
        $comment  = Comment::findOrFail($id);
        $comment->delete();
        $notify[] = ['success','Reply deleted'];
        return back()->withNotify($notify);
    }
}
