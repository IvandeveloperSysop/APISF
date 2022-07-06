<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopWinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_winners', function (Blueprint $table) {
            $table->id();
            $table->integer('period_id');
            $table->integer('user_id');
            $table->integer('promo_id');
            $table->integer('position');
            $table->integer('award_id');
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
        Schema::dropIfExists('top_winners');
    }
}
