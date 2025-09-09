<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Post::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete(); // remove comments if post is removed

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete(); // remove comments if author is removed

            $table->text('body');

            $table->timestamps();
            $table->softDeletes();

            // Helpful index for listing comments chronologically
            $table->index(['post_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
