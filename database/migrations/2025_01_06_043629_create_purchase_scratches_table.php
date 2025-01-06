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
        Schema::create('purchase_scratches', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('fk_int_user_id')->nullable();
            $table->string('narration',500)->nullable();
            $table->integer('scratch_count')->nullable();
            $table->tinyInteger('status');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_scratches');
    }
};
