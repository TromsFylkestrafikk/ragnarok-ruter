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
        Schema::table('ruter_transactions', function (Blueprint $table) {
            $table->char('id')->comment('Hashed version of Transaction ID.')->change();
            $table->text('id_real')->comment('Transaction ID in UUID-ish format with appended string.')->after('id');
            $table->unsignedInteger('ticket_type_id')->nullable()->comment('AKA product template ID')->change();
        });
        Schema::table('ruter_passengers', function (Blueprint $table) {
            $table->text('product_id')->comment('Product ID')->change();
            $table->text('profile_id')->comment('Product ID')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruter_transactions', function (Blueprint $table) {
            $table->dropColumn('id_real');
        });
    }
};
