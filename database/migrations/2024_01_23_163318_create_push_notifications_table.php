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
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_name');
            $table->unsignedBigInteger('receiver_id');
            $table->string('receiver_name');
            $table->unsignedBigInteger('sender_role_id')->nullable();
            $table->string('sender_role_name')->nullable();
            $table->unsignedBigInteger('receiver_role_id')->nullable();
            $table->string('receiver_role_name')->nullable();
            $table->string('message_title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
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
        Schema::dropIfExists('push_notifications');
    }
};
