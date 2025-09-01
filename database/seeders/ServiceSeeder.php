<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Luxury Wedding Hall',
                'description' => 'Elegant wedding hall with modern amenities, perfect for your special day. Features include premium sound system, professional lighting, and dedicated bridal suite.',
                'location' => 'Riyadh',
                'price' => 15000.00,
                'price_type' => 'fixed',
                'category' => 'Venues',
                'image' => 'wedding-hall.jpg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Gourmet Catering',
                'description' => 'Professional catering service offering traditional Saudi cuisine and international dishes. Fresh ingredients, expert chefs, and elegant presentation.',
                'location' => 'Jeddah',
                'price' => 500.00,
                'price_type' => 'per_person',
                'category' => 'Catering',
                'image' => 'catering.jpg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Professional Photoshoot',
                'description' => 'Capture your precious moments with our professional photography service. Includes pre-shoot consultation, 4-hour session, and edited high-resolution photos.',
                'location' => 'Dammam',
                'price' => 2500.00,
                'price_type' => 'fixed',
                'category' => 'Photography',
                'image' => 'photography.jpg',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Live Music Band',
                'description' => 'Energize your event with our talented live music band. Traditional and contemporary music, professional sound equipment included.',
                'location' => 'Riyadh',
                'price' => 3000.00,
                'price_type' => 'fixed',
                'category' => 'Entertainment',
                'image' => 'music-band.jpg',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Event Planning Package',
                'description' => 'Complete event planning service from concept to execution. Includes venue coordination, vendor management, and day-of coordination.',
                'location' => 'Jeddah',
                'price' => 8000.00,
                'price_type' => 'fixed',
                'category' => 'Planning',
                'image' => 'event-planning.jpg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Floral Arrangements',
                'description' => 'Beautiful floral decorations for any occasion. Fresh flowers, creative designs, and setup included.',
                'location' => 'Riyadh',
                'price' => 1200.00,
                'price_type' => 'fixed',
                'category' => 'Decoration',
                'image' => 'flowers.jpg',
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }
}
