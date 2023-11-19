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
        Schema::create('surveys', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->string('surveySubmittedUserName')->nullable();
            $table->string('surveySubmittedUserEmail')->nullable();
            $table->string('surveySubmittedUserPhone')->nullable();
            $table->string('surveySubmittedUserAvatar')->nullable();
            $table->string('binHolderName')->nullable();
            $table->string('binHolderMobile')->nullable();
            $table->string('binHolderEmail')->nullable();
            $table->string('binHolderNid')->nullable();
            $table->string('binNumber')->nullable();
            $table->string('commissioneRate')->nullable();
            $table->date('date')->nullable();
            $table->date('businessStartDate')->nullable();
            $table->string('division')->nullable();
            $table->string('subDivision')->nullable();
            $table->string('circle')->nullable();
            $table->string('shopName')->nullable();
            $table->string('brandName')->nullable();
            $table->string('areaOrshoppingMall')->nullable();
            $table->string('businessRegisteredAddress')->nullable();
            $table->string('outletAddress')->nullable();
            $table->string('category')->nullable();
            $table->string('subCategory')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('numberOfOutlet')->nullable();
            $table->integer('numberOfCounter')->nullable();
            $table->string('differentBin')->nullable();
            $table->string('transactionType')->nullable();
            $table->string('posSoftwareProvider')->nullable();
            $table->string('nrbApproved')->nullable();
            $table->string('thirdPartyName')->nullable();
            $table->integer('monthlyAverageSales')->nullable();
            $table->integer('monthlyAverageCustomer')->nullable();
            $table->string('onlineSaleAvailable')->nullable();
            $table->string('onlineSaleParcent')->nullable();
            $table->string('onlineOrderMode')->nullable();
            $table->string('productInfo')->nullable();
            $table->string('productName')->nullable();
            $table->string('productUnit')->nullable();
            $table->float('unitPrice')->nullable();
            $table->integer('vatParcent')->nullable();
            $table->integer('sdPercent')->nullable();
            $table->float('priceIncludingVat')->nullable();
            $table->float('priceExcludingVat')->nullable();
            $table->string('stockKeeping')->nullable();
            $table->string('posSoftware')->nullable();
            $table->string('posPrinter')->nullable();
            $table->string('pcOrLaptop')->nullable();
            $table->string('mushak')->nullable();
            $table->string('router')->nullable();
            $table->string('networking')->nullable();
            $table->string('surveillance')->nullable();
            $table->string('mobileOperator')->nullable();
            $table->string('operatorCoverage')->nullable();
            $table->string('shopPic')->nullable();
            $table->string('binCertificate')->nullable();
            $table->timestamps();

            DB::statement('ALTER TABLE surveys ADD FULLTEXT search(surveySubmittedUserName, surveySubmittedUserEmail, surveySubmittedUserPhone, binHolderName, binHolderMobile, binHolderEmail, shopName, brandName, productName)');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surveys');
    }
};
