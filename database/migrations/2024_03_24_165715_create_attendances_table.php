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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('supervisor_user_id')->nullable();
            $table->date('date');
            $table->string('in_latitude')->nullable();
            $table->string('in_longitude')->nullable();
            $table->string('in_location')->nullable();
            $table->string('out_latitude')->nullable();
            $table->string('out_longitude')->nullable();
            $table->string('out_location')->nullable();
            $table->json('in_json_location')->nullable();
            $table->json('out_json_location')->nullable();
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
        Schema::dropIfExists('attendances');
    }
};
