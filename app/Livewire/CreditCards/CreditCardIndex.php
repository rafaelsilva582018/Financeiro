<?php

namespace App\Livewire\CreditCards;

use Livewire\Component;
use App\Models\CreditCard;

class CreditCardIndex extends Component
{
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
