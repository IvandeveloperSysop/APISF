<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            // $table->integer('user_id');
            $table->integer('period_score_id');
            $table->integer('state_id')->nullable();
            $table->string('numTicket')->nullable();
            $table->string('file_url');
            $table->date('date_ticket');
            $table->longText('image')->nullable();
            $table->longText('comment')->nullable();
            $table->string('store')->nullable();
            $table->integer('points');
            $table->integer('status');
            $table->integer('validate');
            $table->integer('total_points')->nullable();
            $table->integer('id_admin')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
