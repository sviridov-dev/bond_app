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
        Schema::create('watch_list_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bond_id');
            
            // Create a unique pair constraint
            $table->unique(['user_id', 'bond_id']);
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('bond_id')->references('id')->on('bonds');
            $table->date('audit_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_list_entries');
    }
};
