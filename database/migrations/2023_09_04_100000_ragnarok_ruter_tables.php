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
            $table->string('order_status')->nullable();
            $table->char('payer_app_id')->nullable()->index();
            $table->string('payer_platform')->nullable();
            $table->string('payer_version')->nullable();
            $table->string('payer_phone_type')->nullable();
            $table->char('app_id', 64)->nullable()->index();
            $table->string('app_instance_id', 64)->nullable();
            $table->string('payer_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->decimal('amount');
            $table->decimal('vat');
            $table->decimal('vat_percentage')->nullable();
            $table->decimal('credit_amount')->nullable();
            $table->string('transaction_type')->nullable();
            $table->unsignedBigInteger('ticket_number');
            $table->string('ticket_type')->nullable();
            $table->unsignedInteger('ticket_type_id');
            $table->string('ticket_status')->nullable();
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');
            $table->string('stop_from')->nullable();
            $table->string('stop_to')->nullable();
            $table->string('zone_from')->nullable();
            $table->string('zone_to')->nullable();
            $table->unsignedInteger('zones');
            $table->unsignedSmallInteger('zones_all')->nullable();
            $table->string('distribution_type')->nullable();
            $table->string('cs_ordered_by')->nullable();
            $table->text('cs_comment')->nullable();
            $table->string('cs_invoice_ref')->nullable();
            $table->string('owner')->nullable();
            $table->dateTime('event_time')->nullable();
            $table->string('platform_version')->nullable();
        });

        Schema::create('ruter_passengers', function(Blueprint $table)
        {
            $table->id('id');
            $table->unsignedBigInteger('transaction_id')->index();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('profile_id');
            $table->string('profile')->nullable();
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
