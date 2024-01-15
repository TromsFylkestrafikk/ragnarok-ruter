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
            $table->char('id', 64)->primary()->comment('Transaction ID in UUID-ish format with appended string.');
            $table->date('chunk_date')->comment('The dated chunk this order belongs to')->index();
            $table->char('order_id', 64)->index();
            $table->dateTime('order_date')->comment('Order date in local timezone (Europe/Oslo)');
            $table->string('order_status')->nullable()->comment('Always \'FINISHED\'?');
            $table->char('payer_app_instance_name', 32)->nullable()->comment('App ID of payer in format XXXX-XXXX-XX');
            $table->string('payer_app_platform')->nullable()->comment('Usually ios or android');
            $table->string('payer_app_version')->nullable()->comment('Version of app used to pay');
            $table->string('payer_phone_type')->nullable()->comment('Phone brand and model');
            $table->string('payer_os_version')->nullable()->comment('Phone OS version');
            $table->char('app_instance_name', 32)->nullable()->comment('App name/identifier in XXXX-XXXX-XX format. Used in customer relations to identify transactions.');
            $table->char('app_instance_id', 64)->nullable()->comment('App instance ID in UUID format');
            $table->string('payer_id')->nullable()->comment('UUID format');
            $table->string('payment_id')->nullable()->comment('UUID format');
            $table->string('payment_method')->nullable()->comment('Vipps, mastercard, visa, eurocard, etc..');
            $table->string('payment_status')->nullable()->comment('PAID or CANCELED. Can be NULL');
            $table->decimal('amount')->comment('Payed amount in NOK');
            $table->decimal('vat_amount')->comment('How mouch of payed amount is taxed');
            $table->decimal('vat_percentage')->nullable()->comment('Tax percentage of payed amount');
            $table->decimal('credit_amount')->nullable();
            $table->dateTime('credit_date')->nullable();
            $table->string('transaction_type')->nullable()->comment('CREDIT or SALE');
            $table->unsignedBigInteger('ticket_number');
            $table->string('ticket_type')->nullable()->comment('Ticket type in human readable format');
            $table->unsignedInteger('ticket_type_id')->comment('AKA product template ID');
            $table->string('ticket_status')->nullable()->comment('DELIVERED, DELIVERED_NOT_USED, FAILED, …');
            $table->dateTime('valid_from')->comment('Timestamp when ticket is valid from');
            $table->dateTime('valid_to')->comment('Timestamp when ticket is due');
            $table->string('stop_from')->nullable()->comment('Stop name, no ID. Used by express boats, otherwise null');
            $table->string('stop_to')->nullable()->comment('Stop name, no ID. Used by express boats, otherwise null');
            $table->string('zone_from')->nullable()->comment('Zone name, no ID. May be empty (null)');
            $table->string('zone_to')->nullable()->comment('Zone name, no ID. May be empty (null)');
            $table->unsignedInteger('zones')->comment('Number of zones in ticket');
            $table->unsignedSmallInteger('zones_all')->nullable()->comment('Boolean. true or false');
            $table->string('distribution_type')->nullable()->comment('Examples: CUSTOMER_CENTER, MOBILETICKET, PREPURCHASE');
            $table->string('cs_ordered_by')->nullable()->comment('Who executed the order, usually by customer service');
            $table->text('cs_comment')->nullable()->comment('Comment related to order placed by customer service');
            $table->string('cs_invoice_ref')->nullable();
            $table->string('owner')->nullable()->comment('Company ID');
        });

        Schema::create('ruter_passengers', function(Blueprint $table)
        {
            $table->id()->comment('Unique record ID in ragnarok');
            $table->char('transaction_pax_id', 64)->comment('Transaction ID in UUID format');
            $table->char('transaction_id', 64)->index()->comment('References `ruter_transactions`');
            $table->unsignedInteger('product_id')->comment('Product ID');
            $table->unsignedInteger('profile_id')->comment('Product profile ID');
            $table->string('profile')->nullable()->comment('Product profile name');
            $table->decimal('amount')->nullable()->comment('Price in NOK');
            $table->decimal('vat_amount')->nullable()->comment('How mouch of payed amount is taxed');
            $table->decimal('vat_percentage')->nullable()->comment('Tax percentage of payed amount');
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
        Schema::dropIfExists('ruter_passengers');
        Schema::dropIfExists('ruter_transactions');
    }
};
