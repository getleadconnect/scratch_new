<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblScratchOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_scratch_offers', function (Blueprint $table) {
            $table->increments('pk_int_scratch_offers_id');
            $table->string('vchr_scratch_offers_name')->nullable();
            $table->unsignedBigInteger('fk_int_user_id');
			$table->string('vchr_scratch_offers_image')->nullable();
			$table->string('mobile_image')->nullable();
            $table->integer('int_status');
			$table->integer('type_id')->nullable();
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
        Schema::dropIfExists('tbl_scratch_offers');
    }
}
