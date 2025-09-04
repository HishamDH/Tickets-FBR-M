<?php

namespace Database\Seeders;

use App\Models\VenueLayout;
use App\Models\Seat;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Database\Seeder;

class VenueLayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a merchant user
        $merchant = User::where('user_type', 'merchant')->first();
        
        if (!$merchant) {
            $this->command->error('No merchant found. Please run UsersSeeder first.');
            return;
        }

        // Get an offering from this merchant
        $offering = Offering::where('user_id', $merchant->id)->first();
        
        if (!$offering) {
            $this->command->error('No offering found for merchant. Creating one...');
            
            $offering = Offering::create([
                'user_id' => $merchant->id,
                'title' => 'عرض مسرحي - الليلة الأخيرة',
                'description' => 'عرض مسرحي درامي مثير يحكي قصة مؤثرة عن الحب والفراق',
                'category' => 'entertainment',
                'price' => 150.00,
                'capacity' => 200,
                'duration' => 120,
                'status' => 'active',
                'featured_image' => 'theater-show.jpg',
                'location' => 'مسرح الملك فهد الثقافي، الرياض',
                'terms_and_conditions' => 'يجب الحضور قبل 15 دقيقة من بداية العرض',
                'cancellation_policy' => 'يمكن الإلغاء حتى 24 ساعة قبل العرض',
            ]);
        }

        // Create venue layout
        $venueLayout = VenueLayout::create([
            'merchant_id' => $merchant->id,
            'offering_id' => $offering->id,
            'name' => 'تخطيط المسرح الرئيسي',
            'description' => 'تخطيط المقاعد للمسرح الرئيسي مع مقاعد VIP في المقدمة',
            'rows' => 15,
            'columns' => 20,
            'total_seats' => 0, // Will be calculated
            'is_active' => true,
            'layout_data' => null, // Will be set later
        ]);

        $this->command->info("Created venue layout: {$venueLayout->name}");

        // Create seats
        $seatCount = 0;
        $vipRows = 3; // First 3 rows are VIP
        $regularPrice = $offering->price;
        $vipPrice = $offering->price * 1.5; // VIP is 50% more expensive

        for ($row = 1; $row <= $venueLayout->rows; $row++) {
            for ($col = 1; $col <= $venueLayout->columns; $col++) {
                // Skip some seats to create aisles
                if ($col == 6 || $col == 15) {
                    continue; // Create aisles
                }

                // Skip some back corner seats
                if ($row > 12 && ($col <= 3 || $col >= 18)) {
                    continue;
                }

                $seatCount++;
                $isVip = $row <= $vipRows;
                
                Seat::create([
                    'venue_layout_id' => $venueLayout->id,
                    'seat_number' => sprintf('%02d%02d', $row, $col),
                    'row_number' => $row,
                    'column_number' => $col,
                    'category' => $isVip ? 'vip' : 'standard',
                    'price' => $isVip ? $vipPrice : $regularPrice,
                    'x_position' => $col,
                    'y_position' => $row,
                    'is_available' => true,
                ]);
            }
        }

        // Update total seats count
        $venueLayout->update(['total_seats' => $seatCount]);

        $this->command->info("Created {$seatCount} seats for venue layout");

        // Create layout data
        $seats = $venueLayout->seats;
        $layoutData = [
            'rows' => $venueLayout->rows,
            'columns' => $venueLayout->columns,
            'seats' => $seats->map(function ($seat) {
                return [
                    'number' => $seat->seat_number,
                    'row' => $seat->row_number,
                    'column' => $seat->column_number,
                    'type' => $seat->category === 'vip' ? 'vip' : 'seat',
                ];
            })->values()->toArray(),
            'tables' => [], // No tables in this layout
        ];

        $venueLayout->update(['layout_data' => $layoutData]);

        $this->command->info("✅ Venue layout seeding completed!");
        $this->command->info("📊 Summary:");
        $this->command->info("   - Venue Layout: {$venueLayout->name}");
        $this->command->info("   - Total Seats: {$seatCount}");
        $this->command->info("   - VIP Seats: " . $seats->where('category', 'vip')->count());
        $this->command->info("   - Regular Seats: " . $seats->where('category', 'standard')->count());
        $this->command->info("   - VIP Price: " . number_format($vipPrice, 2) . " SAR");
        $this->command->info("   - Regular Price: " . number_format($regularPrice, 2) . " SAR");
    }
}
