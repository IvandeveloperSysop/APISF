<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->integer('promo_id');
            $table->longText('image');
            $table->string('name', 150);
            $table->longText('description');
            $table->integer('stock')->nullable();
            $table->integer('redeem')->nullable();
            $table->integer('position')->nullable();
            $table->integer('price')->nullable();
            $table->integer('max_per_user')->nullable();
            $table->integer('status_id');
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
        Schema::dropIfExists('awards');
    }
}
