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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('accountno')->nullable();
            $table->string('bankname')->nullable();
            $table->string('ifsccode')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('salary_type')->nullable();
            $table->decimal('total_price', 10,2)->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
