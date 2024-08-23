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
        Schema::table('ruter_transactions', function(Blueprint $table) {
            $table->char('id')->comment('Transaction ID in UUID-ish format with appended string.')->change();
            $table->char('order_id')->change();
            $table->char('payer_app_instance_name')->nullable()->comment('App ID of payer in format XXXX-XXXX-XX')->change();
            $table->char('app_instance_name')->nullable()->comment('App name/identifier in XXXX-XXXX-XX format. Used in customer relations to identify transactions.')->change();
            $table->char('app_instance_id')->nullable()->comment('App instance ID in UUID format')->change();
            $table->unsignedBigInteger('ticket_type_id')->comment('AKA product template ID')->change();
        });

        Schema::table('ruter_passengers', function(Blueprint $table)
        {
            $table->char('transaction_pax_id')->comment('Transaction ID in UUID format')->change();
            $table->char('transaction_id')->comment('References `ruter_transactions`')->change();
            $table->unsignedBigInteger('product_id')->comment('Product ID')->change();
            $table->unsignedBigInteger('profile_id')->comment('Product profile ID')->change();
            $table->unsignedBigInteger('count')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
