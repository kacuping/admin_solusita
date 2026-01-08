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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('order_address')->nullable()->after('transaction_date');
            $table->decimal('order_lat', 10, 7)->nullable()->after('order_address');
            $table->decimal('order_lng', 10, 7)->nullable()->after('order_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['order_address', 'order_lat', 'order_lng']);
        });
    }
};

