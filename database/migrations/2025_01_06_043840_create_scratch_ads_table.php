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
        Schema::create('scratch_ads', function (Blueprint $table) {
            $table->id();
			$table->string('image',200)->nullable();
            $table->string('video',200)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->tinyInteger('status');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scratch_ads');
    }
};
