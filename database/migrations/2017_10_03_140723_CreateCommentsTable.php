<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('comments', function (Blueprint $table) {
        $table->increments('id');
        $table->string('username');
        $table->string('email');
        $table->longText('content');
        $table->integer('commentable_id');
        $table->integer('commentable_type');
        $table->integer('reply')->default(0);
        $table->string('ip');
        $table->timestamps();
      })
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('comments')
    }
}
