<?php

use App\Comment;

class CommentsApiTest extends \Tests\TestCase {

  public function setUp () {
    parent::Setup();
    \Illuminate\Support\Facades\Artisan::call('migrate');
  }

  public function testGetComments () {
    $comment = factory(Comment::class)->create(['commentable_type' => 'Post', 'commentable_id' => 1]);
    $this->assertEquals(1, Comment::count());
  }
}