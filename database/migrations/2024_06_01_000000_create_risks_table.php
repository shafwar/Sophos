<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // 'low' atau 'medium'
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('risks');
    }
}; 