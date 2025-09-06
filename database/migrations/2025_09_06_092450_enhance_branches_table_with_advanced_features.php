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
        Schema::table('branches', function (Blueprint $table) {
            // إضافة الحقول المفقودة
            if (!Schema::hasColumn('branches', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('location');
            }
            if (!Schema::hasColumn('branches', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('branches', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0)->after('city');
            }
        });
        
        // إضافة الفهارس في خطوة منفصلة
        Schema::table('branches', function (Blueprint $table) {
            if (!$this->indexExists('branches', 'branches_is_active_index')) {
                $table->index('is_active');
            }
            if (!$this->indexExists('branches', 'branches_city_index')) {
                $table->index('city');
            }
            if (!$this->indexExists('branches', 'branches_rating_index')) {
                $table->index('rating');
            }
            if (!$this->indexExists('branches', 'branches_user_id_is_active_index')) {
                $table->index(['user_id', 'is_active']);
            }
        });
    }

    private function indexExists($table, $name): bool
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes($table);
        return isset($indexes[$name]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // إزالة الفهارس أولاً إذا كانت موجودة
            if ($this->indexExists('branches', 'branches_is_active_index')) {
                $table->dropIndex(['branches_is_active_index']);
            }
            if ($this->indexExists('branches', 'branches_city_index')) {
                $table->dropIndex(['branches_city_index']);
            }
            if ($this->indexExists('branches', 'branches_rating_index')) {
                $table->dropIndex(['branches_rating_index']);
            }
            if ($this->indexExists('branches', 'branches_user_id_is_active_index')) {
                $table->dropIndex(['branches_user_id_is_active_index']);
            }
        });
        
        Schema::table('branches', function (Blueprint $table) {
            // إزالة الأعمدة المضافة
            if (Schema::hasColumn('branches', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('branches', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('branches', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }
};
