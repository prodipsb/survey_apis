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
        Schema::create('survey_archives', function (Blueprint $table) {
            $table->id();
            $table->string('bin_number')->unique();
            $table->string('bin_holder_name')->nullable();
            $table->string('bin_holder_address')->nullable();
            $table->string('division')->nullable();
            $table->string('circle')->nullable();
            $table->string('commissionerate')->nullable();
            $table->string('zone')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
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
        Schema::dropIfExists('survey_archives');
    }
};
