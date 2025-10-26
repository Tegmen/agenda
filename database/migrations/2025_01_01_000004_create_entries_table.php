<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('entries', function (Blueprint $t) {
            $t->id();
            $t->date('relevance_date')->index();
            $t->string('title', 40);
            $t->text('description'); // UI will cap to 1000 for students
            $t->enum('type', ['Hausaufgabe','Prüfung','Unterschrift','InL','Ereignis'])->index();
            $t->foreignId('group_id')->constrained()->cascadeOnDelete();
            $t->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $t->boolean('hidden_for_students')->default(false)->index();
            $t->unsignedBigInteger('superseded_by')->nullable()->index();
            $t->timestamps(); // created_at drives “Date of creation”
            $t->index(['group_id','relevance_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('entries'); }
};
