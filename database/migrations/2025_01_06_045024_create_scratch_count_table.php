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
        Schema::create('scratch_count', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('fk_int_user_id');
			$table->integer('total_count')->nullable();
			$table->integer('used_count')->nullable();
			$table->integer('balance_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scratch_count');
    }
};
