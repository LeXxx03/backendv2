<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('messageid')->autoincrement();
            $table->integer('user1id');
            $table->integer('user2id');
            $table->tinyInteger('status');
            $table->char('messagetext', 255);
            $table->timestamps();

            //constraintek

            $table->foreign('user1id')->references('userid')->on('users');
            $table->foreign('user2id')->references('userid')->on('users');


        });
    }


    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};