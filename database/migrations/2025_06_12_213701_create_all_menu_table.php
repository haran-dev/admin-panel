<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllMenuTable extends Migration
{
    public function up()
    {
        Schema::create('all_menu', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('link')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('all_menu');
    }
}
