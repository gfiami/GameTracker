<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); //usuario que fez a review
            $table->unsignedBigInteger('game_api_id'); //id do jogo que corresponde a review
            $table->string('rating', 10);
            $table->string('game_name', 50);
            $table->boolean('approved')->nullable()->default(true);
            $table->timestamps();

            //aqio coloca uma chave estrangeira user_id que faz referencia ao id de usuarios
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('review', 1000); //texto da review criado pelo usuario

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
