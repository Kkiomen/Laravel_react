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
        Schema::table('generate_gpt_collections', function (Blueprint $table) {
            $table->boolean('add_last_message')->nullable();
            $table->text('webhook')->nullable();
            $table->text('webhook_type')->nullable();
            $table->integer('lp_generate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('generate_gpt_collections', function (Blueprint $table) {
            //
        });
    }
};
