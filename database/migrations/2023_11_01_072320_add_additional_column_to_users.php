<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->unsignedBigInteger('reporting_role_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('user_type')->nullable();
            $table->string('gender')->nullable();
            $table->string('bin_no')->nullable();
            $table->text('bio')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('division')->nullable();
            $table->string('location')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->dateTime('date_of_joining')->nullable();

            $table->dateTime('last_login')->nullable();
            $table->dateTime('last_logout')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('status')->nullable();

            $table->fullText(['name', 'phone', 'bin_no', 'user_type', 'location']); // adding full-text search indexes

            
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
