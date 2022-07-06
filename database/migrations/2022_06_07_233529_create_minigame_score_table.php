<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMinigameScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minigame_score', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id');
            $table->integer('level_game');
            $table->integer('extra_point_id')->nullable();
            $table->integer('points');
            $table->integer('status');
            $table->integer('refer_id');
            $table->foreignId('mini_game_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('minigame_score');
    }
}
