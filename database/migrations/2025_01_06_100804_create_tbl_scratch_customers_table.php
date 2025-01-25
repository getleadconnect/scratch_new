<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblScratchCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_scratch_customers', function (Blueprint $table) {
            $table->increments('pk_int_scratch_customers_id');
            $table->unsignedBigInteger('fk_int_user_id');
            $table->string('vchr_name')->nullable();
            $table->string('vchr_mobno')->nullable();
			 $table->string('email')->nullable();
			$table->string('vchr_dob')->nullable();
            $table->string('vchr_billno')->nullable();
            $table->integer('int_status')->nullable();
			$table->integer('type_id')->nullable();
			$table->string('offer_text')->nullable();
            $table->unsignedBigInteger('fk_int_offer_id');
			$table->text('extrafield_values')->nullable();
			$table->string('created_by')->nullable();
			$table->unsignedBigInteger('branch_id')->nullable();
			$table->unsignedBigInteger('campaign_id')->nullable();
			$table->string('unique_id')->nullable();
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
        Schema::dropIfExists('tbl_scratch_customers');
    }
}
