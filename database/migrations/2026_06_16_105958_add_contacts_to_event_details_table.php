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
        Schema::table('event_details', function (Blueprint $table) {
            $table->json('contacts')->nullable()->after('youtube_url');
            $table->dropColumn(['contact_name', 'contact_phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_details', function (Blueprint $table) {
            $table->dropColumn('contacts');
            $table->string('contact_name')->nullable()->after('youtube_url');
            $table->string('contact_phone')->nullable()->after('contact_name');
        });
    }
};
