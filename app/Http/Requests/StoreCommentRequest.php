<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function wantsJson () {
      return true;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      \Illuminate\Support\Facades\Validator::extend('canReply', function($attribute, $value, $parameter) {
        if(!$value) {
          return true;
        }
        $comment = \App\Comment::find($value);
        if($comment) {
          return $comment->reply == 0;
        }
        return false;
      });

      return [
        'username' => 'required|max:255',
        'email' => 'required|email',
        'reply' => 'canReply'
      ];
    }
}
