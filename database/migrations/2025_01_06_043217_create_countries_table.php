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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
			$table->string('name',100)->nullable();
            $table->string('country_code',10)->nullable();
            $table->integer('tax')->nullable();
            $table->string('code',100)->nullable();
            $table->string('currency');
			$table->string('currency_code')->nullable();
            $table->string('flags')->nullable();
            $table->tinyInteger('status')->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
