<?php

use App\Comment;

class CommentsApiTest extends \Tests\TestCase {

  public function setUp () {
    parent::Setup();
    \Illuminate\Support\Facades\Artisan::call('migrate');
  }

  public function testGetComments () {
    $comment = Comment::create(['content' => 'Salut']);
    $this->assertEquals(1, Comment::count());
  }
}