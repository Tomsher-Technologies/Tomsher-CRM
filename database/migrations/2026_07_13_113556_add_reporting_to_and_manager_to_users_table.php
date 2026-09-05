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
            $table->unsignedInteger('reporting_to_id')->nullable()->after('banned');
            $table->unsignedInteger('manager_id')->nullable()->after('reporting_to_id');

            $table->foreign('reporting_to_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['reporting_to_id']);
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['reporting_to_id', 'manager_id']);
        });
    }
};
