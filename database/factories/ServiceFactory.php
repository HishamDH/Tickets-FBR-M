<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Venues' => [
                'names' => [
                    'قاعة الماسة للأفراح',
                    'منتجع الوادي الأخضر',
                    'فندق الريتز كارلتون',
                    'قصر الضيافة الملكي',
                    'مركز الملك عبدالله الثقافي',
                    'جناح الأمير فيصل',
                    'قاعة النخيل الذهبية',
                    'منتجع شاطئ الغروب'
                ],
                'descriptions' => [
                    'قاعة فاخرة للأفراح والمناسبات الخاصة مع أحدث التجهيزات',
                    'منتجع متكامل وسط الطبيعة الخلابة للمناسبات الاستثنائية',
                    'فندق 5 نجوم يوفر أرقى الخدمات والمرافق للمناسبات الراقية',
                    'قصر للاستقبالات الرسمية والمناسبات الكبرى بديكور أصيل'
                ]
            ],
            'Catering' => [
                'names' => [
                    'مطبخ الأصالة للتموين',
                    'بيت الذواقة للضيافة',
                    'مطاعم الغردقة الشامية',
                    'تموين القصر الملكي',
                    'مطبخ الجدة للمأكولات التراثية',
                    'مؤسسة النعيم للضيافة',
                    'مطبخ الشيف المتميز',
                    'تموين الأطايب الشرقية'
                ],
                'descriptions' => [
                    'تموين متكامل للولائم والمناسبات بأطباق تراثية وعالمية',
                    'خدمات ضيافة راقية مع طاقم متخصص وأطباق مميزة',
                    'مأكولات شامية أصيلة للمناسبات والحفلات',
                    'تموين فاخر للمناسبات الكبرى والاستقبالات الرسمية'
                ]
            ],
            'Photography' => [
                'names' => [
                    'استوديو عدسة الذكريات',
                    'مؤسسة الضوء الذهبي',
                    'شركة اللقطة المثالية',
                    'استوديو الفن الراقي',
                    'مصورو الأحلام المحترفون',
                    'شركة البورتريه الذهبي',
                    'استوديو الإبداع البصري',
                    'فريق عدسة المشاعر'
                ],
                'descriptions' => [
                    'تصوير احترافي للمناسبات والأفراح بأحدث المعدات والتقنيات',
                    'خدمات تصوير متكاملة مع مونتاج وإخراج سينمائي',
                    'تصوير فوتوغرافي وفيديو بجودة عالية للذكريات الجميلة',
                    'فريق محترف متخصص في توثيق اللحظات المميزة'
                ]
            ],
            'Entertainment' => [
                'names' => [
                    'فرقة الأنغام الذهبية',
                    'مجموعة الفنون الشعبية',
                    'أوركسترا الحفلات الملكية',
                    'فرقة الطرب الأصيل',
                    'مجموعة الألحان العربية',
                    'فرقة النجوم للغناء',
                    'أوركسترا الموسيقى الكلاسيكية',
                    'فريق المايك الذهبي'
                ],
                'descriptions' => [
                    'فرقة موسيقية محترفة تقدم أجمل الألحان للمناسبات',
                    'عروض فنية تراثية وشعبية مع راقصين محترفين',
                    'أوركسترا كاملة للحفلات الراقية والمناسبات الكبرى',
                    'طرب أصيل وغناء شجي يضفي روحاً خاصة على مناسباتكم'
                ]
            ],
            'Planning' => [
                'names' => [
                    'مكتب الأحلام للتنظيم',
                    'شركة المناسبات المثالية',
                    'مؤسسة الإبداع والتميز',
                    'مكتب الفعاليات الراقية',
                    'شركة اللمسة الذهبية',
                    'مؤسسة التنظيم المحترف',
                    'مكتب الأناقة والجمال',
                    'شركة الإبداع اللامحدود'
                ],
                'descriptions' => [
                    'تنظيم شامل للمناسبات من التخطيط حتى التنفيذ المثالي',
                    'خدمات تنظيم وتنسيق متكاملة للأفراح والمؤتمرات',
                    'فريق إبداعي متخصص في إدارة الفعاليات الكبرى',
                    'تخطيط وتنفيذ احترافي للمناسبات بأدق التفاصيل'
                ]
            ]
        ];

        $selectedCategory = $this->faker->randomElement(array_keys($categories));
        $categoryData = $categories[$selectedCategory];
        
        $name = $this->faker->randomElement($categoryData['names']);
        $description = $this->faker->randomElement($categoryData['descriptions']);
        
        $pricingModels = ['fixed', 'per_person', 'per_table', 'hourly'];
        $pricingModel = $this->faker->randomElement($pricingModels);
        
        // Set base price based on category and pricing model
        $basePrices = [
            'Venues' => ['min' => 5000, 'max' => 50000],
            'Catering' => ['min' => 100, 'max' => 500], // per person usually
            'Photography' => ['min' => 2000, 'max' => 15000],
            'Entertainment' => ['min' => 3000, 'max' => 20000],
            'Planning' => ['min' => 5000, 'max' => 30000],
        ];
        
        $priceRange = $basePrices[$selectedCategory];
        $basePrice = $this->faker->numberBetween($priceRange['min'], $priceRange['max']);
        
        // Adjust price for pricing model
        if ($pricingModel === 'per_person') {
            $basePrice = $this->faker->numberBetween(50, 300);
        } elseif ($pricingModel === 'per_table') {
            $basePrice = $this->faker->numberBetween(500, 2000);
        } elseif ($pricingModel === 'hourly') {
            $basePrice = $this->faker->numberBetween(200, 1500);
        }

        $features = [
            'Venues' => [
                'موقف سيارات مجاني',
                'تكييف مركزي',
                'إضاءة احترافية',
                'نظام صوتي متطور',
                'كاميرات مراقبة',
                'خدمة أمن',
                'مساحات خضراء',
                'تراس خارجي'
            ],
            'Catering' => [
                'بوفيه مفتوح',
                'أطباق تراثية',
                'مأكولات عالمية',
                'حلويات شرقية',
                'مشروبات ساخنة وباردة',
                'طاولات ومقاعد',
                'أدوات تقديم فاخرة',
                'طاقم خدمة مدرب'
            ],
            'Photography' => [
                'تصوير 4K عالي الدقة',
                'طائرة بدون طيار',
                'إضاءة احترافية',
                'مونتاج وإخراج',
                'ألبوم فوتوغرافي',
                'نسخ رقمية',
                'تصحيح ألوان',
                'تسليم سريع'
            ],
            'Entertainment' => [
                'نظام صوتي متطور',
                'إضاءة ملونة',
                'مؤثرات بصرية',
                'فرقة موسيقية',
                'مطربين محترفين',
                'راقصين تراثيين',
                'آلات موسيقية متنوعة',
                'عروض تفاعلية'
            ],
            'Planning' => [
                'تخطيط شامل',
                'إدارة الفعالية',
                'تنسيق الموردين',
                'جدولة زمنية',
                'طاقم تنظيم',
                'خدمة استقبال',
                'تنسيق ديكور',
                'متابعة مستمرة'
            ]
        ];

        $cities = ['الرياض', 'جدة', 'الدمام', 'مكة المكرمة', 'المدينة المنورة', 'الطائف', 'أبها', 'تبوك', 'الخبر', 'القطيف'];

        return [
            'merchant_id' => Merchant::factory(),
            'name' => $name,
            'description' => $description,
            'location' => $this->faker->randomElement($cities),
            'price' => $basePrice,
            'price_type' => $pricingModel,
            'category' => $selectedCategory,
            'service_type' => $this->faker->randomElement(['event', 'exhibition', 'restaurant', 'experience']),
            'pricing_model' => $pricingModel,
            'base_price' => $basePrice,
            'currency' => 'SAR',
            'duration_hours' => $pricingModel === 'hourly' ? $this->faker->numberBetween(2, 12) : $this->faker->optional()->numberBetween(2, 8),
            'capacity' => $selectedCategory === 'Venues' ? $this->faker->numberBetween(50, 1000) : null,
            'features' => json_encode($this->faker->randomElements($features[$selectedCategory], $this->faker->numberBetween(3, 6))),
            'images' => json_encode([]),
            'status' => $this->faker->randomElement(['active', 'inactive', 'draft']),
            'online_booking_enabled' => $this->faker->boolean(80),
            'is_featured' => $this->faker->boolean(20),
            'is_active' => $this->faker->boolean(85),
            'is_available' => $this->faker->boolean(90),
        ];
    }

    /**
     * Indicate that the service is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the service is a venue type.
     */
    public function venue(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Venues',
            'capacity' => $this->faker->numberBetween(100, 1000),
            'pricing_model' => 'fixed',
            'base_price' => $this->faker->numberBetween(10000, 50000),
        ]);
    }

    /**
     * Indicate that the service is catering type.
     */
    public function catering(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Catering',
            'pricing_model' => 'per_person',
            'base_price' => $this->faker->numberBetween(80, 300),
        ]);
    }
}
