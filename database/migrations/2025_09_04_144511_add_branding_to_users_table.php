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
            // Subdomain for merchant stores
            $table->string('subdomain')->nullable()->unique()->after('email');
            
            // Branding and customization
            $table->json('branding')->nullable()->after('subdomain');
            $table->string('logo_url')->nullable()->after('branding');
            $table->string('custom_domain')->nullable()->after('logo_url');
            $table->boolean('custom_domain_verified')->default(false)->after('custom_domain');
            
            // Store settings
            $table->text('store_description')->nullable()->after('custom_domain_verified');
            $table->json('social_links')->nullable()->after('store_description');
            $table->json('business_hours')->nullable()->after('social_links');
            $table->boolean('store_active')->default(true)->after('business_hours');
            
            // SEO settings
            $table->string('meta_title')->nullable()->after('store_active');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            
            // Indexes for performance
            $table->index('subdomain');
            $table->index('custom_domain');
            $table->index(['user_type', 'store_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['subdomain']);
            $table->dropIndex(['custom_domain']);
            $table->dropIndex(['user_type', 'store_active']);
            
            $table->dropColumn([
                'subdomain',
                'branding',
                'logo_url',
                'custom_domain',
                'custom_domain_verified',
                'store_description',
                'social_links',
                'business_hours',
                'store_active',
                'meta_title',
                'meta_description',
                'meta_keywords',
            ]);
        });
    }
};
