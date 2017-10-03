<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentsController extends Controller
{
  public function index () {
    $comments = \App\Comment::allFor(\Illuminate\Support\Facades\Input::get('type'), \Illuminate\Support\Facades\Input::get('id'));
    return \Illuminate\Support\Facades\Response::json($comments, 200, [], JSON_NUMERIC_CHECK);
  }
}
