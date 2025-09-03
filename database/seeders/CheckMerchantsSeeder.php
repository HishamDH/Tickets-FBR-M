<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;

class CheckMerchantsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Current merchants:');

        $merchants = Merchant::with('user')->get();

        if ($merchants->isEmpty()) {
            $this->command->info('No merchants found');

            return;
        }

        foreach ($merchants as $merchant) {
            $this->command->info(sprintf(
                'ID: %d | Business: %s | Status: %s | User: %s',
                $merchant->id,
                $merchant->business_name,
                $merchant->verification_status,
                $merchant->user->email
            ));
        }

        $this->command->info('Total merchants: '.$merchants->count());
    }
}
