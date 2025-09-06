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
            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'password_history')) {
                $table->json('password_history')->nullable()->after('password_changed_at');
            }
            if (!Schema::hasColumn('users', 'force_password_reset')) {
                $table->boolean('force_password_reset')->default(false)->after('password_history');
            }
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->integer('failed_login_attempts')->default(0)->after('force_password_reset');
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'last_login_user_agent')) {
                $table->string('last_login_user_agent')->nullable()->after('last_login_ip');
            }
        });
        
        // إضافة فهارس بعد إضافة الأعمدة
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'password_changed_at') && !$this->indexExists('users', 'users_password_changed_at_index')) {
                $table->index('password_changed_at');
            }
            if (Schema::hasColumn('users', 'force_password_reset') && !$this->indexExists('users', 'users_force_password_reset_index')) {
                $table->index('force_password_reset');
            }
            if (Schema::hasColumn('users', 'failed_login_attempts') && !$this->indexExists('users', 'users_failed_login_attempts_index')) {
                $table->index('failed_login_attempts');
            }
            if (Schema::hasColumn('users', 'locked_until') && !$this->indexExists('users', 'users_locked_until_index')) {
                $table->index('locked_until');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إزالة الفهارس أولاً
            if ($this->indexExists('users', 'users_password_changed_at_index')) {
                $table->dropIndex(['password_changed_at']);
            }
            if ($this->indexExists('users', 'users_force_password_reset_index')) {
                $table->dropIndex(['force_password_reset']);
            }
            if ($this->indexExists('users', 'users_failed_login_attempts_index')) {
                $table->dropIndex(['failed_login_attempts']);
            }
            if ($this->indexExists('users', 'users_locked_until_index')) {
                $table->dropIndex(['locked_until']);
            }
        });
        
        Schema::table('users', function (Blueprint $table) {
            // إزالة الأعمدة الموجودة فقط
            $columnsToRemove = [];
            
            if (Schema::hasColumn('users', 'password_changed_at')) {
                $columnsToRemove[] = 'password_changed_at';
            }
            if (Schema::hasColumn('users', 'password_history')) {
                $columnsToRemove[] = 'password_history';
            }
            if (Schema::hasColumn('users', 'force_password_reset')) {
                $columnsToRemove[] = 'force_password_reset';
            }
            if (Schema::hasColumn('users', 'failed_login_attempts')) {
                $columnsToRemove[] = 'failed_login_attempts';
            }
            if (Schema::hasColumn('users', 'locked_until')) {
                $columnsToRemove[] = 'locked_until';
            }
            if (Schema::hasColumn('users', 'last_login_ip')) {
                $columnsToRemove[] = 'last_login_ip';
            }
            if (Schema::hasColumn('users', 'last_login_user_agent')) {
                $columnsToRemove[] = 'last_login_user_agent';
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $name): bool
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes($table);
        return isset($indexes[$name]);
    }
};
