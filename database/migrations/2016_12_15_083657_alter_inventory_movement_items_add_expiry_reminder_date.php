<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInventoryMovementItemsAddExpiryReminderDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_movement_items', function (Blueprint $table) {
            $table->dateTime('expiry_reminder_date')
                ->nullable();

            $table->index(['expiry_reminder_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_movement_items', function (Blueprint $table) {
            $table->dropColumn(['expiry_reminder_date']);
        });
    }
}
