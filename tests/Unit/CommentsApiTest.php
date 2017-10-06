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

  public function testPostComment () {
    $post = factory(\App\Post::class)->create();
    $comment = factory(Comment::class)->make(['commentable_id' => $post->id, 'commentable_type' => 'Post']);
    $response = $this->call('POST', '/comments', $comment->getAttributes());
    $response_comment = json_decode($response->getCOntent());
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals(1, Comment::count());
    $this->assertEquals(md5(\Illuminate\Support\Facades\Request::ip()), $response_comment->ip_md5);
  }

  public function testPostCommentOnFakeContent () {
    $comment = factory(Comment::class)->make(['commentable_id' => 3, 'commentable_type' => 'Post']);
    $response = $this->call('POST', '/comments', $comment->getAttributes());
    $this->assertEquals(422, $response->getStatusCode());
    $this->assertEquals(0, Comment::count());
  }

  public function testPostWithFakeEmail () {
    $post = factory(\App\Post::class)->create();
    $comment = factory(Comment::class)->make(['commentable_id' => $post->id, 'commentable_type' => 'Post', 'email' => 'fake@']);
    $response = $this->call('POST', '/comments', $comment->getAttributes());
    $json = json_decode($response->getCOntent());
    $this->assertEquals(422, $response->getStatusCode(), $response->getContent());
    $this->assertEquals(0, Comment::count());
    $this->objectHasAttribute('email', $json);
  }

  public function testPostCommentWithFalseReply () {
    $post = factory(\App\Post::class)->create();
    $comment = factory(Comment::class)->make(['commentable_id' => $post->id, 'commentable_type' => 'Post', 'reply' => 3]);
    $response = $this->call('POST', '/comments', $comment->getAttributes());
    $json = json_decode($response->getCOntent());
    $this->assertEquals(422, $response->getStatusCode(), $response->getContent());
    $this->assertEquals(0, Comment::count());
    $this->objectHasAttribute('reply', $json);
  }

  public function testPostReplyOnReply () {
    $post = factory(\App\Post::class)->create();
    $comment = factory(Comment::class)->create(['commentable_id' => $post->id, 'commentable_type' => 'Post']);
    $reply = factory(Comment::class)->create(['commentable_id' => $post->id, 'commentable_type' => 'Post', 'reply' => $comment->id]);
    $reply2 = factory(Comment::class)->make(['commentable_id' => $post->id, 'commentable_type' => 'Post', 'reply' => $reply->id]);
    $response = $this->call('POST', '/comments', $reply2->getAttributes());
    $json = json_decode($response->getContent());
    $this->assertEquals(422, $response->getStatusCode(), $response->getContent());
    $this->assertEquals(2, Comment::count());
    $this->objectHasAttribute('reply', $json);
  }

  public function testDeleteComment () {
    $post = factory(\App\Post::class)->create();
    $comment = factory(Comment::class)->create(['commentable_id' => $post->id, 'commentable_type' => 'Post', 'ip' => \Illuminate\Support\Facades\Request::ip()]);
    $response = $this->call('DELETE', '/comments/' . $comment->id);
    $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
    $this->assertEquals(0, Comment::count());
  }

  public function testDeleteCommentWithoutGoodIp () {
    $post = factory(\App\Post::class)->create();
    $comment = factory(Comment::class)->create(['commentable_id' => $post->id, 'commentable_type' => 'Post']);
    $response = $this->call('DELETE', '/comments/' . $comment->id);
    $this->assertEquals(403, $response->getStatusCode(), $response->getContent());
    $this->assertEquals(1, Comment::count());
  }
  public function testCascadingDelete () {
    $post = factory(\App\Post::class)->create();
    $comment = factory(Comment::class)->create(['commentable_id' => $post->id, 'commentable_type' => 'Post', 'ip' => \Illuminate\Support\Facades\Request::ip()]);
    $reply = factory(Comment::class)->create(['commentable_id' => $post->id, 'commentable_type' => 'Post', 'reply' => $comment->id]);
    $response = $this->call('DELETE', '/comments/' . $comment->id);
    $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
    $this->assertEquals(0, Comment::count());
  }
}