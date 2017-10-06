<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentsController extends Controller
{
  public function index () {
    $comments = \App\Comment::allFor(\Illuminate\Support\Facades\Input::get('type'), \Illuminate\Support\Facades\Input::get('id'));
    return \Illuminate\Support\Facades\Response::json($comments, 200, [], JSON_NUMERIC_CHECK);
  }

  public function store (\App\Http\Requests\StoreCommentRequest $request) {
    $model_id = \Illuminate\Support\Facades\Input::get('commentable_id');
    $model = \Illuminate\Support\Facades\Input::get('commentable_type');
    if(\App\Comment::isCommentable($model, $model_id)) {
      $comment = \App\Comment::create([
        'commentable_id' => \Illuminate\Support\Facades\Input::get('commentable_id'),
        'commentable_type' => \Illuminate\Support\Facades\Input::get('commentable_type'),
        'content' => \Illuminate\Support\Facades\Input::get('content'),
        'email' => \Illuminate\Support\Facades\Input::get('email'),
        'username' => \Illuminate\Support\Facades\Input::get('username'),
        'reply' => \Illuminate\Support\Facades\Input::get('reply', 0),
        'ip' => $request->ip()
      ]);
      return \Illuminate\Support\Facades\Response::json($comment, 200, [], JSON_NUMERIC_CHECK);
    } else {
      return \Illuminate\Support\Facades\Response::json("Ce contenu c'est pas commentable", 422);
    }
  }

  public function destroy ($id) {
    $comment = \App\Comment::find($id);
    if($comment->ip == \Illuminate\Support\Facades\Request::ip()) {
      \App\Comment::where('reply', '=', $comment->id)->delete();
      $comment->delete();
      return \Illuminate\Support\Facades\Response::json($comment, 200, [], JSON_NUMERIC_CHECK);
    } else {
      return \Illuminate\Support\Facades\Response::json('Ce commentaire ne vous appartient pas', 403);
    }
  }
}
