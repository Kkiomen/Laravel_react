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
        Schema::create('order_positions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("order_id")->unsigned();
            $table->bigInteger("product_id")->unsigned();
            $table->integer("quantity");
            $table->decimal("price_net");
            $table->decimal("price_gross");
            $table->decimal("tax");
            $table->decimal("price_net_total");
            $table->decimal("price_gross_total");
            $table->decimal("tax_value_total");
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
        Schema::dropIfExists('order_positions');
    }
};
