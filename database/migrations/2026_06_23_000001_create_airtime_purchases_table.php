<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airtime_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_number');
            $table->decimal('amount_btc', 16, 8)->nullable();
            $table->decimal('amount_sats', 16, 2)->nullable();
            $table->decimal('amount_kwacha', 16, 2);
            $table->decimal('convenience_fee', 16, 2)->default(0);
            $table->decimal('total_sats', 16, 2)->nullable();
            $table->decimal('network_fee', 16, 2)->default(400);
            $table->string('qr_code_path')->nullable();
            $table->text('lightning_invoice_address')->nullable();
            $table->string('checking_id')->nullable();
            $table->string('checkout_url')->nullable();
            $table->string('payment_status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('airtime_request_id')->nullable();
            $table->string('airtime_status')->nullable();
            $table->json('airtime_response')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airtime_purchases');
    }
};
