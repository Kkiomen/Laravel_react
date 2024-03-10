<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combo_orders', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_address');
            $table->string('company_city');
            $table->string('company_postal_code');
            $table->string('company_country');
            $table->string('company_phone');
            $table->string('company_email');
            $table->string('company_first_name');
            $table->string('company_last_name');
            $table->integer('order_positions_count');
            $table->integer('company_nip');
            $table->decimal('order_positions_price_net_total');
            $table->decimal('order_positions_price_gross_total');
            $table->decimal('order_positions_tax_value_total');
            $table->string('invoice_symbol');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('combo_orders');
    }
};
