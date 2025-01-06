<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblScratchOffersListingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_scratch_offers_listing', function (Blueprint $table) {
            $table->increments('pk_int_scratch_offers_listing_id');
            $table->integer('fk_int_scratch_offers_id');
            $table->integer('int_scratch_offers_count');
            $table->text('txt_description');
			$table->string('image')->nullable();
            $table->integer('int_scratch_offers_balance')->nullable();
            $table->integer('fk_int_user_id');
			$table->integer('int_winning_status');
            $table->integer('int_status');
			$table->integer('created_by')->nullable();
			$table->integer('type_id')->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     $table->integer('int_status');
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_scratch_offers_listing');
    }
}
