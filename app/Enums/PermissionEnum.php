<?php

namespace App\Enums;

class PermissionEnum
{
    // مجموعة صلاحيات المستخدمين
    const USER_PERMISSIONS = [
        'users.view' => 'عرض المستخدمين',
        'users.create' => 'إنشاء المستخدمين',
        'users.edit' => 'تعديل المستخدمين',
        'users.delete' => 'حذف المستخدمين',
        'users.approve' => 'الموافقة على المستخدمين',
        'users.suspend' => 'تعليق المستخدمين',
    ];

    // مجموعة صلاحيات التجار
    const MERCHANT_PERMISSIONS = [
        'merchants.view' => 'عرض التجار',
        'merchants.create' => 'إنشاء التجار',
        'merchants.edit' => 'تعديل التجار',
        'merchants.delete' => 'حذف التجار',
        'merchants.approve' => 'الموافقة على التجار',
        'merchants.verify' => 'التحقق من التجار',
        'merchants.suspend' => 'تعليق التجار',
        'merchants.financial' => 'عرض البيانات المالية للتجار',
    ];

    // مجموعة صلاحيات الخدمات والعروض
    const SERVICE_PERMISSIONS = [
        'services.view' => 'عرض الخدمات',
        'services.create' => 'إنشاء الخدمات',
        'services.edit' => 'تعديل الخدمات',
        'services.delete' => 'حذف الخدمات',
        'services.approve' => 'الموافقة على الخدمات',
        'services.publish' => 'نشر الخدمات',
        'services.unpublish' => 'إلغاء نشر الخدمات',
        'services.moderate' => 'إدارة محتوى الخدمات',
    ];

    // مجموعة صلاحيات الحجوزات
    const BOOKING_PERMISSIONS = [
        'bookings.view' => 'عرض الحجوزات',
        'bookings.create' => 'إنشاء الحجوزات',
        'bookings.edit' => 'تعديل الحجوزات',
        'bookings.cancel' => 'إلغاء الحجوزات',
        'bookings.confirm' => 'تأكيد الحجوزات',
        'bookings.refund' => 'استرداد الحجوزات',
        'bookings.check_in' => 'تسجيل الوصول',
    ];

    // مجموعة صلاحيات المدفوعات
    const PAYMENT_PERMISSIONS = [
        'payments.view' => 'عرض المدفوعات',
        'payments.process' => 'معالجة المدفوعات',
        'payments.refund' => 'استرداد المدفوعات',
        'payments.withdraw' => 'سحب الأرباح',
        'payments.approve_withdraw' => 'الموافقة على طلبات السحب',
        'payments.gateways' => 'إدارة بوابات الدفع',
    ];

    // مجموعة صلاحيات التقييمات
    const REVIEW_PERMISSIONS = [
        'reviews.view' => 'عرض التقييمات',
        'reviews.create' => 'إنشاء التقييمات',
        'reviews.edit' => 'تعديل التقييمات',
        'reviews.delete' => 'حذف التقييمات',
        'reviews.approve' => 'الموافقة على التقييمات',
        'reviews.reject' => 'رفض التقييمات',
        'reviews.moderate' => 'إدارة التقييمات',
    ];

    // مجموعة صلاحيات التقارير والإحصائيات
    const ANALYTICS_PERMISSIONS = [
        'analytics.view' => 'عرض التحليلات',
        'analytics.export' => 'تصدير التقارير',
        'analytics.advanced' => 'التحليلات المتقدمة',
        'analytics.financial' => 'التحليلات المالية',
    ];

    // مجموعة صلاحيات الإشعارات
    const NOTIFICATION_PERMISSIONS = [
        'notifications.view' => 'عرض الإشعارات',
        'notifications.send' => 'إرسال الإشعارات',
        'notifications.broadcast' => 'إرسال إشعارات جماعية',
        'notifications.manage' => 'إدارة الإشعارات',
    ];

    // مجموعة صلاحيات المحتوى
    const CONTENT_PERMISSIONS = [
        'content.view' => 'عرض المحتوى',
        'content.create' => 'إنشاء المحتوى',
        'content.edit' => 'تعديل المحتوى',
        'content.delete' => 'حذف المحتوى',
        'content.publish' => 'نشر المحتوى',
    ];

    // مجموعة صلاحيات الإعدادات
    const SETTINGS_PERMISSIONS = [
        'settings.view' => 'عرض الإعدادات',
        'settings.edit' => 'تعديل الإعدادات',
        'settings.system' => 'إعدادات النظام',
        'settings.security' => 'إعدادات الأمان',
    ];

    // مجموعة صلاحيات الشركاء
    const PARTNER_PERMISSIONS = [
        'partners.view' => 'عرض الشركاء',
        'partners.create' => 'إنشاء الشركاء',
        'partners.edit' => 'تعديل الشركاء',
        'partners.delete' => 'حذف الشركاء',
        'partners.approve' => 'الموافقة على الشركاء',
        'partners.commissions' => 'إدارة عمولات الشركاء',
    ];

    // مجموعة صلاحيات POS
    const POS_PERMISSIONS = [
        'pos.access' => 'الوصول لنظام POS',
        'pos.sell' => 'البيع عبر POS',
        'pos.refund' => 'الاسترداد عبر POS',
        'pos.reports' => 'تقارير POS',
        'pos.settings' => 'إعدادات POS',
    ];

    /**
     * الحصول على جميع الصلاحيات
     */
    public static function getAllPermissions(): array
    {
        return array_merge(
            self::USER_PERMISSIONS,
            self::MERCHANT_PERMISSIONS,
            self::SERVICE_PERMISSIONS,
            self::BOOKING_PERMISSIONS,
            self::PAYMENT_PERMISSIONS,
            self::REVIEW_PERMISSIONS,
            self::ANALYTICS_PERMISSIONS,
            self::NOTIFICATION_PERMISSIONS,
            self::CONTENT_PERMISSIONS,
            self::SETTINGS_PERMISSIONS,
            self::PARTNER_PERMISSIONS,
            self::POS_PERMISSIONS
        );
    }

    /**
     * الحصول على المجموعات المنظمة
     */
    public static function getGroupedPermissions(): array
    {
        return [
            'المستخدمين' => self::USER_PERMISSIONS,
            'التجار' => self::MERCHANT_PERMISSIONS,
            'الخدمات والعروض' => self::SERVICE_PERMISSIONS,
            'الحجوزات' => self::BOOKING_PERMISSIONS,
            'المدفوعات' => self::PAYMENT_PERMISSIONS,
            'التقييمات' => self::REVIEW_PERMISSIONS,
            'التحليلات والتقارير' => self::ANALYTICS_PERMISSIONS,
            'الإشعارات' => self::NOTIFICATION_PERMISSIONS,
            'المحتوى' => self::CONTENT_PERMISSIONS,
            'الإعدادات' => self::SETTINGS_PERMISSIONS,
            'الشركاء' => self::PARTNER_PERMISSIONS,
            'نظام POS' => self::POS_PERMISSIONS,
        ];
    }

    /**
     * صلاحيات الأدوار الافتراضية
     */
    public static function getRolePermissions(): array
    {
        return [
            'admin' => array_keys(self::getAllPermissions()), // جميع الصلاحيات
            'merchant' => [
                // صلاحيات التاجر
                'services.view', 'services.create', 'services.edit', 'services.delete',
                'bookings.view', 'bookings.confirm', 'bookings.cancel', 'bookings.check_in',
                'payments.view', 'payments.withdraw',
                'reviews.view', 'reviews.moderate',
                'analytics.view', 'analytics.export',
                'notifications.view',
                'pos.access', 'pos.sell', 'pos.refund', 'pos.reports',
            ],
            'customer' => [
                // صلاحيات العميل
                'services.view',
                'bookings.view', 'bookings.create', 'bookings.cancel',
                'payments.view',
                'reviews.view', 'reviews.create', 'reviews.edit',
                'notifications.view',
            ],
            'partner' => [
                // صلاحيات الشريك
                'merchants.view',
                'analytics.view',
                'notifications.view',
                'partners.view',
            ],
        ];
    }
}
