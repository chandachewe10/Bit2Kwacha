<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('airtime_purchases', function (Blueprint $table) {
            $table->text('lightning_invoice_address')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('airtime_purchases', function (Blueprint $table) {
            $table->string('lightning_invoice_address')->nullable()->change();
        });
    }
};
