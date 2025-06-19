<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScratchApiUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scratch_api_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('api_key')->nullable();
            $table->string('mobile')->nullable();
            $table->string('amount')->nullable();
            $table->string('status')->nullable();
            $table->string('redeem')->nullable();
            $table->string('email')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('bill_no')->nullable();
			$table->unsignedBigInteger('offer_id')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('scratch_api_users');
    }
}
