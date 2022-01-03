<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Estateagencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Estateagencies', function (Blueprint $table) {
            $table->id();
            $table->char('status',1)->default('N')->nullable(false);
            $table->char('active_estateagency_site',1)->default('N')->nullable(false);
            $table->unsignedBigInteger('affiliate');
            $table->string('name')->nullable(false);
            $table->string('slug')->nullable(false);
            $table->char('zip_code',8)->nullable(false);
            $table->string('street')->nullable(false);
            $table->char('number', 9)->nullable(false);
            $table->string('district')->nullable(false);
            $table->string('complement')->nullable();
            $table->unsignedBigInteger('city');
            $table->char('fone1',11)->nullable(false);
            $table->char('fone2',11)->nullable();
            $table->char('fone1_estateagency_site',11)->nullable();
            $table->char('fone2_estateagency_site',11)->nullable();
            $table->string('email')->nullable(false);
            $table->text('description')->nullable(false);
            $table->string('url_site')->nullable();
            $table->string('path_image_capa')->nullable();
            $table->string('path_image_logo')->nullable();
            $table->timestamps();
            $table->foreign('affiliate')->references('id')->on('Affiliates');
            $table->foreign('city')->references('id')->on('Cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Estateagencies');
    }
}
