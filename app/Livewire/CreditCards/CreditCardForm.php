<?php

namespace App\Livewire\CreditCards;

use Livewire\Component;
use App\Models\CreditCard;

class CreditCardForm extends Component
{
    public ?CreditCard $creditCard = null;

    public string $name = '';
    public float $limit = 0;
    public int $closing_day = 1;
    public int $due_day = 1;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'limit' => 'required|numeric|min:0',
            'closing_day' => 'required|integer|between:1,28',
            'due_day' => 'required|integer|between:1,28',
        ];
    }

    public function mount(CreditCard $creditCard = null): void
    {
        if ($creditCard && $creditCard->exists) {
            $this->creditCard = $creditCard;
            $this->name = $creditCard->name;
            $this->limit = (float) $creditCard->limit;
            $this->closing_day = $creditCard->closing_day;
            $this->due_day = $creditCard->due_day;
        }
    }

    public function save()
    {
        $this->validate();

        CreditCard::updateOrCreate(
            [
                'id' => $this->creditCard?->id,
            ],
            [
                'user_id' => auth()->id(),
                'name' => $this->name,
                'limit' => $this->limit,
                'closing_day' => $this->closing_day,
                'due_day' => $this->due_day,
            ]
        );

        return redirect()->route('credit-cards.index');
    }

    public function render()
    {
        return view('livewire.credit-cards.credit-card-form');
    }
}
