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
        Schema::create('service_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('issue_id');
            $table->timestamps();

            $table->foreign('service_id')
            ->references('id') 
            ->on('device_services')
            ->onDelete('cascade');  

            $table->foreign('issue_id')
            ->references('id') 
            ->on('device_service_issues')
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
        Schema::dropIfExists('service_issues');
    }
};
