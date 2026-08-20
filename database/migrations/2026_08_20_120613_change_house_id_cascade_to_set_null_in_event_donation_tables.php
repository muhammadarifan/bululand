<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_money_transactions', function (Blueprint $table) {
            $table->dropForeign(['house_id']);
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('set null');
        });

        Schema::table('event_item_donations', function (Blueprint $table) {
            $table->dropForeign(['house_id']);
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('event_money_transactions', function (Blueprint $table) {
            $table->dropForeign(['house_id']);
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
        });

        Schema::table('event_item_donations', function (Blueprint $table) {
            $table->dropForeign(['house_id']);
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
        });
    }
};
