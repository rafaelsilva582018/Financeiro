<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class CategoryForm extends Component
{
    public ?Category $category = null;

    public bool $modal = false;

    public string $name = '';

    public string $type = 'expense';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ];
    }


    protected function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da categoria.',
            'type.required' => 'Selecione o tipo da categoria.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'nome',
            'type' => 'tipo',
        ];
    }
    public function mount(?Category $category = null, bool $modal = false): void
    {
        $this->modal = $modal;

        if ($category && $category->exists) {
            $this->category = $category;
            $this->name = $category->name;
            $this->type = $category->type;
        }
    }

    public function save()
    {
        $this->validate();

        Category::updateOrCreate(
            ['id' => $this->category?->id],
            [
                'user_id' => auth()->id(),
                'name' => $this->name,
                'type' => $this->type,
            ]
        );

        if ($this->modal) {
            $this->reset(['category', 'name']);
            $this->dispatch('category-created');
            $this->dispatch('close-dashboard-modal');
            $this->dispatch('close-resource-modal');

            return null;
        }

        return redirect()->route('categories.index');
    }

    public function render()
    {
        return view('livewire.categories.category-form');
    }
}