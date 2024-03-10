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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("client_id")->unsigned();
            $table->string('invoice_symbol');
            $table->string('invoice_place');
            $table->date('invoice_date');
            $table->date('payment_date');
            $table->string('delivery_address');
            $table->string('delivery_city');
            $table->string('delivery_postal_code');
            $table->string('delivery_country');
            $table->string('delivery_phone');
            $table->string('delivery_email');
            $table->string('delivery_first_name');
            $table->string('delivery_last_name');
            $table->integer('positions_count');
            $table->decimal('positions_price_net_total');
            $table->decimal('positions_price_gross_total');
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
        Schema::dropIfExists('orders');
    }
};
