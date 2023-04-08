<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritedGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorited_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); //usuario logado
            $table->unsignedBigInteger('game_api_id'); //jogo adicionado da api
            $table->timestamps();


            //aqio coloca uma chave estrangeira user_id que faz referencia ao id de usuarios
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorited_games');
    }
}
