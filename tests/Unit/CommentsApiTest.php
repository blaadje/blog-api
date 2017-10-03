<?php

use App\Comment;

class CommentsApiTest extends \Tests\TestCase {

  public function setUp () {
    parent::Setup();
    \Illuminate\Support\Facades\Artisan::call('migrate');
  }

  public function testGetComments () {
    $post = factory(\App\Post::class)->create();
    $comment = factory(Comment::class)->create(['commentable_type' => 'Post', 'commentable_id' => $post->id]);
    $comment2 = factory(Comment::class)->create(['commentable_type' => 'Post', 'commentable_id' => $post->id]);
    $comment3 = factory(Comment::class)->create(['commentable_type' => 'Post', 'commentable_id' => $post->id, 'reply' => $comment2->id]);
    $response = $this->call('GET', '/comments', ['type' => 'Post', 'id' => $post->id]);
    $comments = json_decode($response->getContent());
    $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
    $this->assertEquals(2, count($comments));
    $this->assertSame(0, $comments[0]->reply);
    $this->assertSame($comment2->id, $comments[0]->id);
    $this->assertSame(1, count($comments[0]->replies));
    $this->assertSame($comment->id, $comments[1]->id);
  }

  public function testFieldsForJson () {
    $post = factory(\App\Post::class)->create();
    $comment = factory(Comment::class)->create(['commentable_type' => 'Post', 'commentable_id' => $post->id]);
    $reply = factory(Comment::class)->create(['commentable_type' => 'Post', 'commentable_id' => $post->id, 'reply' => $comment->id]);
    $response = $this->call('GET', '/comments', ['type' => 'Post', 'id' => $post->id]);
    $comments = json_decode($response->getContent());
    $this->assertObjectNotHasAttribute('email', $comments[0]);
    $this->assertObjectNotHasAttribute('ip', $comments[0]);
    $this->assertObjectHasAttribute('email_md5', $comments[0]);
    $this->assertSame(md5($comment->ip), $comments[0]->ip_md5);
    $this->assertObjectNotHasAttribute('email', $comments[0]->replies[0]);
    $this->assertObjectNotHasAttribute('ip', $comments[0]->replies[0]);
  }
}