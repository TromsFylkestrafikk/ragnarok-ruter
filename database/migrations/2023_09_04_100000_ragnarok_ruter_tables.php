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
        Schema::create('ruter_transactions', function(Blueprint $table)
        {
            $table->id('id');
            $table->char('order_id', 64)->index();
            $table->date('order_date');
            $table->time('order_time');
            $table->string('order_status', 45)->nullable();
            $table->string('payer_app_id', 45)->nullable();
            $table->string('payer_platform', 45)->nullable();
            $table->string('payer_version', 45)->nullable();
            $table->string('payer_phone_type', 45)->nullable();
            $table->string('app_id', 45)->nullable();
            $table->string('app_instance_id', 45)->nullable();
            $table->string('payer_id', 45)->nullable();
            $table->string('payment_id', 45)->nullable();
            $table->string('payment_method', 45)->nullable();
            $table->string('payment_status', 45)->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('vat', 10, 2);
            $table->decimal('vat_percentage', 10, 2)->nullable();
            $table->decimal('credit_amount', 10, 2)->nullable();
            $table->string('transaction_type', 45)->nullable();
            $table->unsignedBigInteger('ticket_number');
            $table->string('ticket_type', 45)->nullable();
            $table->unsignedInteger('ticket_type_id');
            $table->string('ticket_status', 45)->nullable();
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');
            $table->string('stop_from', 45)->nullable();
            $table->string('stop_to', 45)->nullable();
            $table->string('zone_from', 45)->nullable();
            $table->string('zone_to', 45)->nullable();
            $table->unsignedInteger('zones');
            $table->unsignedSmallInteger('zones_all')->nullable();
            $table->string('distribution_type', 45)->nullable();
            $table->string('cs_ordered_by', 45)->nullable();
            $table->string('cs_comment', 45)->nullable();
            $table->string('cs_invoice_ref', 45)->nullable();
            $table->string('owner', 45)->nullable();
            $table->dateTime('event_time')->nullable();
            $table->string('platform_version', 45)->nullable();
        });

        Schema::create('ruter_passengers', function(Blueprint $table)
        {
            $table->id('id');
            $table->unsignedBigInteger('transaction_id')->index();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('profile_id');
            $table->string('profile', 45)->nullable();
            $table->unsignedInteger('count');

            $table->foreign('transaction_id')
                ->references('id')
                ->on('ruter_transactions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruter_transactions');
        Schema::dropIfExists('ruter_passengers');
    }
};
