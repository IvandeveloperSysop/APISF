<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_points', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('period_id');
            $table->integer('type_id');
            $table->integer('points');
            $table->integer('status');
            $table->integer('ticket_id')->nullable();
            $table->integer('refer_or_minigames_id')->nullable();
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
        Schema::dropIfExists('extra_points');
    }
}
