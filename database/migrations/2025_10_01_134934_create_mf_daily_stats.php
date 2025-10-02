<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mf_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained('sectors')->onDelete('cascade');
            $table->foreignId('amc_id')->constrained('amcs')->onDelete('cascade');
            $table->foreignId('mutual_fund_id')->constrained('mutual_funds')->onDelete('cascade');
            $table->date('inception_date')->nullable();
            $table->decimal('offer', 15, 4)->nullable();
            $table->decimal('repurchase', 15, 4)->nullable();
            $table->decimal('nav', 15, 4)->nullable();
            $table->date('validity_date')->nullable();
            $table->decimal('front_end', 10, 4)->nullable();
            $table->decimal('back_end', 10, 4)->nullable();
            $table->decimal('contingent', 10, 4)->nullable();
            $table->decimal('market', 15, 4)->nullable();
            $table->foreignId('trustee_id')->nullable()->constrained('trustees')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['mutual_fund_id', 'validity_date'], 'fund_date_unique');
            $table->index('amc_id');
            $table->index('category');
            $table->index('validity_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('my_daily_stats');
    }
};
