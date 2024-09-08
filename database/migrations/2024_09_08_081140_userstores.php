<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('userstores', function (Blueprint $table) {
            $table->id(); 
            $table->text('userid')->nullable();
            $table->text('shopify_domain')->nullable();
            $table->text('api_key')->nullable();
            $table->text('name')->nullable();
            $table->text('storedata')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
