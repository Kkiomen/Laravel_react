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
        Schema::create('generate_gpt_collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('generate_gpt_request_id');
            $table->string('id_external')->nullable();
            $table->text('prompt')->nullable();
            $table->text('system')->nullable();
            $table->text('generated_content')->nullable();
            $table->integer('sort')->nullable();
            $table->integer('status_generate')->default(0);
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
        Schema::dropIfExists('generate_gpt_collections');
    }
};
