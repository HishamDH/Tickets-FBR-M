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
            // معلومات الموقع والتواصل
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('city', 100)->nullable()->after('longitude');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            
            // معلومات السعة والخدمات
            $table->integer('capacity')->nullable()->after('postal_code');
            $table->json('services')->nullable()->after('capacity');
            $table->text('amenities')->nullable()->after('services');
            
            // معلومات المدير
            $table->string('manager_name', 255)->nullable()->after('amenities');
            $table->string('manager_phone', 20)->nullable()->after('manager_name');
            $table->string('manager_email', 255)->nullable()->after('manager_phone');
            
            // ساعات العمل
            $table->json('opening_hours')->nullable()->after('manager_email');
            
            // إعدادات الحجز
            $table->boolean('accepts_online_booking')->default(true)->after('opening_hours');
            $table->integer('advance_booking_days')->default(30)->after('accepts_online_booking');
            $table->time('booking_cutoff_time')->nullable()->after('advance_booking_days');
            
            // الإعدادات المالية
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('booking_cutoff_time');
            $table->decimal('cancellation_fee', 8, 2)->default(0)->after('commission_rate');
            
            // إعدادات الإشعارات
            $table->boolean('email_notifications')->default(true)->after('cancellation_fee');
            $table->boolean('sms_notifications')->default(true)->after('email_notifications');
            
            // التقييم والتصنيف
            $table->decimal('rating', 3, 2)->default(0)->after('sms_notifications');
            $table->integer('review_count')->default(0)->after('rating');
            
            // الصور والوسائط
            $table->json('images')->nullable()->after('review_count');
            $table->string('cover_image')->nullable()->after('images');
            
            // الحالة والإعدادات
            $table->boolean('is_verified')->default(false)->after('cover_image');
            $table->boolean('is_featured')->default(false)->after('is_verified');
            $table->timestamp('last_activity_at')->nullable()->after('is_featured');
            
            // فهارس للبحث السريع
            $table->index(['city', 'is_active']);
            $table->index(['is_active', 'is_verified']);
            $table->index(['rating', 'review_count']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // إزالة الفهارس أولاً
            $table->dropIndex(['city', 'is_active']);
            $table->dropIndex(['is_active', 'is_verified']);
            $table->dropIndex(['rating', 'review_count']);
            $table->dropIndex(['user_id', 'is_active']);
            
            // إزالة الأعمدة
            $table->dropColumn([
                'latitude',
                'longitude', 
                'city',
                'state',
                'postal_code',
                'capacity',
                'services',
                'amenities',
                'manager_name',
                'manager_phone',
                'manager_email',
                'opening_hours',
                'accepts_online_booking',
                'advance_booking_days',
                'booking_cutoff_time',
                'commission_rate',
                'cancellation_fee',
                'email_notifications',
                'sms_notifications',
                'rating',
                'review_count',
                'images',
                'cover_image',
                'is_verified',
                'is_featured',
                'last_activity_at'
            ]);
        });
    }
};
