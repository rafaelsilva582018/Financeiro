<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use App\Models\Category;

class CategoryForm extends Component
{
    public ?Category $category = null;

    public string $name = '';
    public string $type = 'expense';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ];
    }

    public function mount(Category $category = null): void
    {
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
            [
                'id' => $this->category?->id,
            ],
            [
                'user_id' => auth()->id(),
                'name'    => $this->name,
                'type'    => $this->type,
            ]
        );

        return redirect()->route('categories.index');
    }

    public function render()
    {
        return view('livewire.categories.category-form');
    }
}
