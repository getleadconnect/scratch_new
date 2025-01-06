<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShortLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
			$table->string('name')->nullable();
            $table->unsignedBigInteger('vender_id')->nullable();
            $table->unsignedBigInteger('offer_id')->nullable();            
            $table->string('code')->nullable();
            $table->string('link')->nullable();
			$table->text('url')->nullable();
            $table->tinyInteger('bill_number_only_apply_from_list')->default(0);
			$table->tinyInteger('email_required')->nullable();
			$table->tinyInteger('branch_required')->default(0);
			$table->integer('click_count')->default(0);
			$table->tinyInteger('custom_field')->nullable();
            $table->tinyInteger('type')->default(1);            
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
        Schema::dropIfExists('short_links');
    }
}
