<?php

namespace App\Http\Livewire;

use Livewire\Component;

class OfferingWizard extends Component
{
    public $hasChairs = false;

    public $chairsCount = null;

    public function render()
    {
        return view('livewire.offering-wizard');
    }
}
