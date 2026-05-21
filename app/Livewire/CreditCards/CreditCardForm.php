<?php

namespace App\Livewire\CreditCards;

use App\Models\CreditCard;
use Livewire\Component;

class CreditCardForm extends Component
{
    public ?CreditCard $creditCard = null;

    public bool $modal = false;

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

    protected function messages(): array
    {
        return [
            'name.required' => 'Informe o nome do cartão.',
            'limit.required' => 'Informe o limite do cartão.',
            'closing_day.required' => 'Informe o dia de fechamento.',
            'closing_day.between' => 'O fechamento deve ser entre os dias 1 e 28.',
            'due_day.required' => 'Informe o dia de vencimento.',
            'due_day.between' => 'O vencimento deve ser entre os dias 1 e 28.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'nome',
            'limit' => 'limite',
            'closing_day' => 'dia de fechamento',
            'due_day' => 'dia de vencimento',
        ];
    }

    public function mount(?CreditCard $creditCard = null, bool $modal = false): void
    {
        $this->modal = $modal;

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
            ['id' => $this->creditCard?->id],
            [
                'user_id' => auth()->id(),
                'name' => $this->name,
                'limit' => $this->limit,
                'closing_day' => $this->closing_day,
                'due_day' => $this->due_day,
            ]
        );

        if ($this->modal) {
            $this->reset(['creditCard', 'name', 'limit']);
            $this->closing_day = 1;
            $this->due_day = 1;
            $this->modal = true;
            $this->dispatch('credit-card-saved');
            $this->dispatch('close-resource-modal');

            return null;
        }

        return redirect()->route('credit-cards.index');
    }

    public function render()
    {
        return view('livewire.credit-cards.credit-card-form');
    }
}