<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->string('main_criterion'); // ex: qualite, cout, etc.
            $table->string('sub_criterion');  // ex: consistency, communication, etc.
            $table->float('main_weight');     // ex: 0.3 pour 30%
            $table->integer('sub_weight');    // ex: 3
            $table->integer('score');         // sur 10
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_scores');
    }
};
