<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScratchWebCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scratch_web_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('name')->nullable();
            $table->bigInteger('mobile')->nullable();
            $table->integer('country_code')->nullable();
			$table->string('vchr_mobile',50)->nullable();
			$table->string('email')->nullable();
			$table->integer('redeemed_agent')->nullable();
			$table->unsignedBigInteger('offer_id')->nullable();
			$table->unsignedBigInteger('offer_list_id')->nullable();
			$table->text('offer_text')->nullable();
			$table->string('short_code',20)->nullable();
			$table->integer('bill_no')->nullable();
            $table->string('short_link')->nullable();
			$table->string('api_key',500)->nullable();
            $table->string('amount')->nullable();
			$table->tinyInteger('win_status')->nullable();
            $table->tinyInteger('redeem')->nullable();
			$table->ipAddress('ip_address')->nullable();
            $table->string('branch_id')->nullable();
			$table->string('redeem_source',20)->nullable();
			$table->tinyInteger('type_id')->nullable();
			$table->dateTime('redeemed_on')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('scratch_web_customers');
    }
}
