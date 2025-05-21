<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('risk_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_id')->constrained('risks')->onDelete('cascade');
            $table->string('name');
            $table->string('description');
            $table->string('recommendation')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('risk_details');
    }
}; 