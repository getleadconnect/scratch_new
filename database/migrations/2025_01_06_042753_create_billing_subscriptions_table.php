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
        Schema::create('billing_subscriptions', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('fk_int_user_id');
			$table->unsignedBigInteger('vendor_id');
            $table->integer('no_of_licenses')->nullable();
            $table->integer('plan_type')->nullable();
            $table->integer('services')->nullable();
            $table->integer('billing_id')->nullable();
            $table->integer('amount');
			$table->integer('promo_code_id')->nullable();
            $table->integer('promo_code_value')->nullable();
            $table->integer('additional_discount')->nullable();
            $table->integer('currency')->nullable();
			$table->integer('start_date')->nullable();
			$table->integer('end_date')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_subscriptions');
    }
};
