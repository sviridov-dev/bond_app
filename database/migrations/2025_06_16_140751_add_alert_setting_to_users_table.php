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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->integer('lead_time')->default(10)->after('role'); // Default lead time in minutes
            $table->boolean('alert_enabled')->default(false)->after('lead_time'); // Whether alert is enabled
            $table->timestamp('alert_time')->nullable()->after('alert_enabled');
            $table->string('time_zone')->default('UTC')->after('alert_time');    // Time zone of the event
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('lead_time');
            $table->dropColumn('alert_enabled');
            $table->dropColumn('alert_time');
            $table->dropColumn('time_zone');
        });
    }
};
