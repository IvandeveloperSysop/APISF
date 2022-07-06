<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppShareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_share', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('period_id');
            $table->longText('image')->nullable();
            $table->longText('comment')->nullable();
            $table->string('url');
            $table->integer('status');
            $table->integer('id_admin')->nullable();
            $table->integer('validate')->nullable();
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
        Schema::dropIfExists('app_share');
    }
}
