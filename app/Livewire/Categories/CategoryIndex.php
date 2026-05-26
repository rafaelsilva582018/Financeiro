<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class CategoryIndex extends Component
{
    protected $listeners = ['category-created' => '$refresh'];

    public function delete(int $id): void
    {
        Category::where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();
    }

    public function render()
    {
        return view('livewire.categories.category-index', [
            'categories' => Category::where('user_id', auth()->id())
                ->orderBy('type')
                ->orderBy('name')
                ->get(),
        ]);
    }
}