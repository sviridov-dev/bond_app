<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bonds', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('ticker')->nullable();
            $table->string('ISIN')->nullable();
            $table->string('issuer_information')->nullable();
            $table->string('currency')->nullable();
            $table->string('rating')->nullable();
            $table->float('price')->nullable();
            $table->float('yield_maturity')->nullable();
            $table->float('coupon_rate')->nullable();
            $table->float('volume')->nullable();
            $table->float('duration')->nullable();
            $table->date('maturity_date')->nullable();
            $table->date('next_offer_date')->nullable();
            $table->text('additional_info')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonds');
    }
};
