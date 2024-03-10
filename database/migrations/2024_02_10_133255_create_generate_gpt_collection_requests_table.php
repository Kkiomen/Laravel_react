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
        Schema::create('generate_gpt_collection_requests', function (Blueprint $table) {
            $table->id();
            $table->string("id_external")->nullable();
            $table->string("model")->nullable();
            $table->float("temperature")->nullable();
            $table->string("type")->nullable();
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
        Schema::dropIfExists('generate_gpt_collection_requests');
    }
};
