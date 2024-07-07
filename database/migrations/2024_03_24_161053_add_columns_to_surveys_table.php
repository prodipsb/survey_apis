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
        Schema::table('surveys', function (Blueprint $table) {
            $table->string('tracked_location')->nullable()->after('longitude');
            $table->enum('survey_type', ['New', 'Archive'])->nullable()->after('weeklyHoliday');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            Schema::dropIfExists('tracked_location');
            Schema::dropIfExists('survey_type');
        });
    }
};
