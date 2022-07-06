<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nickName');
            $table->string('email')->unique();
            $table->string('token')->nullable();
            $table->longText('imageUrl')->nullable();
            $table->longText('image')->nullable();
            $table->longText('imageResizeUrl')->nullable();
            $table->longText('imageResize')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('country')->nullable();
            $table->integer('cp')->nullable();
            $table->string('street',150)->nullable();
            $table->string('suburb',150)->nullable();
            $table->string('city',150)->nullable();
            $table->string('state',150)->nullable();
            $table->string('cellPhone')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('providerSocial')->nullable();
            $table->longText('idSocial')->nullable();
            // $table->integer('id_admin')->nullable();
            $table->integer('version_id')->nullable();
            $table->integer('notifications')->nullable();
            $table->dateTime('tokenExpiration')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('status')->nullable();
            $table->integer('validFriends')->nullable();
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
