<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('group_user', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('group_id')->constrained()->cascadeOnDelete();
            $t->boolean('is_primary')->default(false);
            $t->timestamps();
            $t->unique(['user_id','group_id']);
            $t->index(['group_id','user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('group_user'); }
};
