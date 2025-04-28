<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_associated', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('associated_product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
            
            // Avoid duplications
            $table->unique(['product_id', 'associated_product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_associated');
    }
};