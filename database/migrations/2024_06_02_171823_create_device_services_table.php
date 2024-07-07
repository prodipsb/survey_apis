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
        Schema::create('device_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->string('binNumber');
            $table->unsignedBigInteger('device_id');
            $table->string('device_serial_number');
            $table->Text('comment')->nullable();
            $table->enum('status', ['Pending', 'Received', 'Processing', 'Realy For Delivered', 'AO Received', 'Delivered']);
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id') 
            ->on('users')
            ->onDelete('cascade');  

   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_services');
    }
};
