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
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('subdomain')->unique()->nullable()->after('business_name');
            $table->string('logo_path')->nullable()->after('subdomain');
            $table->json('brand_colors')->nullable()->after('logo_path');
            $table->string('primary_color', 7)->default('#3B82F6')->after('brand_colors');
            $table->string('secondary_color', 7)->default('#1E40AF')->after('primary_color');
            $table->string('accent_color', 7)->default('#EF4444')->after('secondary_color');
            $table->text('custom_css')->nullable()->after('accent_color');
            $table->string('custom_domain')->nullable()->after('custom_css');
            $table->boolean('subdomain_enabled')->default(true)->after('custom_domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn([
                'subdomain',
                'logo_path', 
                'brand_colors',
                'primary_color',
                'secondary_color', 
                'accent_color',
                'custom_css',
                'custom_domain',
                'subdomain_enabled'
            ]);
        });
    }
};
