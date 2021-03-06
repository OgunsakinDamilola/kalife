<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->integer('title')->default(1);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_name')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->longText('address')->nullable();
            $table->integer('gender')->nullable();
            $table->string('agency_name')->nullable();
            $table->string('agent_id')->nullable();;
            $table->string('office_number')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('password');
            $table->integer('account_status')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
