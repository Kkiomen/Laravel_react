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
        Schema::create('history_generate_gpt_collections', function (Blueprint $table) {
            $table->id();
            $table->integer('collection_request_id')->nullable();
            $table->string('model')->nullable();
            $table->string('id_external')->nullable();
            $table->float('temperature')->nullable();
            $table->string('type')->nullable();
            $table->string('sort')->nullable();
            $table->string('webhook')->nullable();
            $table->string('webhook_type')->nullable();
            $table->text('prompt')->nullable();
            $table->text('system')->nullable();
            $table->text('generated_content')->nullable();
            $table->boolean('add_last_message')->nullable();
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
        Schema::dropIfExists('history_generate_gpt_collections');
    }
};
