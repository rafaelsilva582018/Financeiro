<?php

namespace App\Livewire\CreditCards;

use App\Models\CreditCard;
use Livewire\Component;

class CreditCardIndex extends Component
{
    protected $listeners = ['credit-card-saved' => '$refresh'];

    public function delete(int $id): void
    {
        CreditCard::where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();
    }

    public function render()
    {
        return view('livewire.credit-cards.credit-card-index', [
            'cards' => CreditCard::where('user_id', auth()->id())
                ->orderBy('name')
                ->get(),
        ]);
    }
}