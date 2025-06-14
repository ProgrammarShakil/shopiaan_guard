<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('domain')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('status')->default('active'); // active, suspended, expired
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_domains')->default(1);
            $table->integer('used_domains')->default(0);
            $table->json('features')->nullable();
            $table->string('license_type')->default('regular'); // regular, extended, lifetime
            $table->boolean('is_trial')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('licenses');
    }
}; 