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
        Schema::table('users', function (Blueprint $table) {
            $table->string('prefixname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->string('suffixname')->nullable();
            $table->string('username')->nullable();
            
            $table->text('photo')->nullable();

            $table->string('type')->default('user')->nullable();
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('prefixname');
            $table->dropColumn('middlename');
            $table->dropColumn('lastname');
            $table->dropColumn('suffixname');
            $table->dropColumn('username');
            
            $table->dropColumn('photo');

            $table->dropIndex(['type']);

        });
    }
};
