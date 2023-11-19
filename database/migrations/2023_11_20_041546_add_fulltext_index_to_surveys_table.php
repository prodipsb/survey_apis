<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->index([
                'surveySubmittedUserName',
                'surveySubmittedUserEmail',
                'surveySubmittedUserPhone',
                'binHolderName',
                'binHolderMobile',
                'binHolderEmail',
                'shopName',
                'brandName',
                'productName',
            ], 'fulltext_search');
        });

       // DB::statement('ALTER TABLE surveys ADD FULLTEXT search(surveySubmittedUserName, surveySubmittedUserEmail, surveySubmittedUserPhone, binHolderName, binHolderMobile, binHolderEmail, shopName, brandName, productName)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropIndex('fulltext_search');
        });
    }
};
