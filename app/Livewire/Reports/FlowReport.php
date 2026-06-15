<?php

namespace App\Livewire\Reports;

use App\Models\Entry;
use Carbon\Carbon;
use Livewire\Component;

class FlowReport extends Component
{
    public string $type = 'expense';

    public string $month;

    public string $title = '';

    public string $subtitle = '';

    public string $selectedMonthLabel = '';

    public array $summary = [];

    public array $comparison = [];

    public array $categoryChart = [];

    public array $monthlyChart = [];

    public $entries;

    public function mount(): void
    {
        abort_unless(in_array($this->type, ['income', 'expense'], true), 404);

        $this->month = request('month', now()->format('Y-m'));

        $this->loadReport();
    }

    public function updatedMonth(): void
    {
        $this->loadReport();
    }

    public function loadReport(): void
    {
        [$year, $month] = explode('-', $this->month);
        $reference = Carbon::create((int) $year, (int) $month, 1);
        $previous = $reference->copy()->subMonth();

        $this->selectedMonthLabel = $reference->translatedFormat('F \d\e Y');
        $this->title = $this->type === 'income' ? 'Relatório de receitas' : 'Relatório de despesas';
        $this->subtitle = $this->type === 'income'
            ? 'Veja entradas do mês, categorias que mais geram receita e comparação com meses anteriores.'
            : 'Veja gastos do mês, categorias que mais pesam e comparação com meses anteriores.';

        $currentEntries = $this->queryForMonth($reference)
            ->with(['transaction.category', 'account', 'creditCard'])
            ->orderByDesc('reference_date')
            ->get();

        $previousTotal = (float) $this->queryForMonth($previous)->sum('value');
        $currentTotal = (float) $currentEntries->sum('value');
        $paidTotal = (float) $currentEntries->where('status', 'paid')->sum('value');
        $pendingTotal = (float) $currentEntries->where('status', 'pending')->sum('value');
        $variation = $previousTotal > 0 ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1) : null;

        $this->summary = [
            'total' => $currentTotal,
            'paid' => $paidTotal,
            'pending' => $pendingTotal,
            'count' => $currentEntries->count(),
            'average' => $currentEntries->count() ? $currentTotal / $currentEntries->count() : 0,
        ];

        $this->comparison = [
            'current' => $currentTotal,
            'previous' => $previousTotal,
            'variation' => $variation,
            'previous_label' => $previous->translatedFormat('m/Y'),
        ];

        $this->categoryChart = $this->buildCategoryChart($currentEntries);
        $this->monthlyChart = $this->buildMonthlyChart($reference);
        $this->entries = $currentEntries;

        $this->dispatch('flow-report:charts-updated', [
            'categories' => $this->categoryChart,
            'monthly' => $this->monthlyChart,
            'comparison' => $this->comparison,
            'type' => $this->type,
        ]);
    }

    private function queryForMonth(Carbon $reference)
    {
        return Entry::where('user_id', auth()->id())
            ->whereHas('transaction', fn ($query) => $query->where('type', $this->type))
            ->whereYear('reference_date', $reference->year)
            ->whereMonth('reference_date', $reference->month);
    }

    private function buildCategoryChart($entries): array
    {
        $grouped = $entries
            ->groupBy(fn (Entry $entry) => $entry->transaction?->category?->name ?? 'Sem categoria')
            ->map(fn ($items) => round((float) $items->sum('value'), 2))
            ->filter(fn (float $value) => $value > 0)
            ->sortDesc();

        return [
            'labels' => $grouped->keys()->values()->toArray(),
            'values' => $grouped->values()->values()->toArray(),
        ];
    }

    private function buildMonthlyChart(Carbon $reference): array
    {
        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $reference->copy()->subMonths($i);
            $labels[] = ucfirst($month->translatedFormat('M'));
            $values[] = (float) $this->queryForMonth($month)->sum('value');
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function render()
    {
        return view('livewire.reports.flow-report');
    }
}
