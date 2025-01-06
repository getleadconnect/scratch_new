<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlApiTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_gl_api_tokens', function (Blueprint $table) {
            $table->increments('pk_int_token_id');
            $table->unsignedBigInteger('fk_int_user_id');
            $table->string('vchr_token');
            $table->integer('int_status');
			$table->integer('created_by')->nullable();
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
        Schema::dropIfExists('tbl_gl_api_tokens');
    }
}
