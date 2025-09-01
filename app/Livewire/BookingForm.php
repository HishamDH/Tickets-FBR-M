<?php

namespace App\Livewire;

use Livewire\Component;

class BookingForm extends Component
{
    public $service_id;

    public $step = 1;

    public $date;

    public $quantity = 1;

    public $name;

    public $email;

    public $phone;

    public function mount($service_id)
    {
        $this->service_id = $service_id;
    }

    public function render()
    {
        return view('livewire.booking-form');
    }

    public function nextStep()
    {
        // Add validation logic here for each step if needed
        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit()
    {
        // Add final validation
        // Process payment
        // Save booking to database

        $this->step = 'success'; // Show a success message
    }
}
