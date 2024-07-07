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
        
        Schema::create('device_service_circles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_service_id')->index();
            $table->text('comment')->nullable();
            $table->string('delivered_image')->nullable();
            $table->enum('status', ['Pending', 'Received', 'Processing', 'Realy For Delivered', 'AO Received', 'Delivered']);
            $table->timestamps();

            $table->foreign('device_service_id')
            ->references('id') 
            ->on('device_services')
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
        Schema::dropIfExists('device_service_circles');
    }
};
