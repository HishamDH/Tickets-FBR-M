<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RolesAndPermissionsSeeder;

class SetupPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup roles and permissions for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 بدء إعداد الأدوار والصلاحيات...');
        
        try {
            // تشغيل seeder الصلاحيات
            $seeder = new RolesAndPermissionsSeeder();
            $seeder->setCommand($this);
            $seeder->run();
            
            $this->newLine();
            $this->info('✅ تم إعداد نظام الصلاحيات بنجاح!');
            $this->info('📊 يمكنك الآن استخدام الصلاحيات في التطبيق');
            
        } catch (\Exception $e) {
            $this->error('❌ خطأ في إعداد الصلاحيات: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
