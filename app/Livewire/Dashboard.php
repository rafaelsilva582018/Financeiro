<?php

namespace App\Livewire;

use App\Models\CreditCard;
use App\Models\Entry;
use App\Services\CreditCardInvoiceService;
use App\Services\MonthlySummaryService;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public string $month;

    public array $summary = [];

    public array $monthlyChart = [];

    public array $yearlyChart = [];

    public array $categoryChart = [];

    public array $cards = [];

    public array $health = [];

    public string $selectedMonthLabel = '';

    public $entries = [];

    protected $listeners = [
        'transaction-created' => 'loadDashboard',
    ];

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
        $this->loadDashboard();
    }

    public function updatedMonth(): void
    {
        $this->loadDashboard();
    }

    public function loadDashboard(): void
    {
        [$year, $month] = explode('-', $this->month);
        $reference = Carbon::create((int) $year, (int) $month, 1);
        $start = $reference->copy()->startOfMonth();
        $end = $reference->copy()->endOfMonth();

        $this->selectedMonthLabel = $reference->format('m/Y');

        $this->summary = app(MonthlySummaryService::class)
            ->getSummary(auth()->id(), $reference);

        $income = (float) ($this->summary['income'] ?? 0);
        $expenses = (float) ($this->summary['expenses'] ?? 0);
        $finalBalance = (float) ($this->summary['final_balance'] ?? 0);
        $totalMovement = $income + $expenses;

        $pendingTotal = (float) Entry::where('user_id', auth()->id())
            ->whereBetween('reference_date', [$start, $end])
            ->where('status', 'pending')
            ->sum('value');

        $paidCount = Entry::where('user_id', auth()->id())
            ->whereBetween('reference_date', [$start, $end])
            ->where('status', 'paid')
            ->count();

        $pendingCount = Entry::where('user_id', auth()->id())
            ->whereBetween('reference_date', [$start, $end])
            ->where('status', 'pending')
            ->count();

        $this->health = [
            'pending_total' => $pendingTotal,
            'paid_count' => $paidCount,
            'pending_count' => $pendingCount,
            'payment_progress' => ($paidCount + $pendingCount) > 0
                ? round(($paidCount / ($paidCount + $pendingCount)) * 100)
                : 0,
            'expense_ratio' => $income > 0 ? round(($expenses / $income) * 100) : 0,
            'movement_total' => $totalMovement,
        ];

        $this->monthlyChart = [
            'income' => $income,
            'expenses' => $expenses,
            'balance' => $finalBalance,
        ];

        $this->yearlyChart = $this->buildYearlyChart((int) $year);
        $this->categoryChart = $this->buildCategoryChart((int) $year, (int) $month);
        $this->cards = $this->buildCards($reference);
        $this->entries = $this->buildLatestEntries($start, $end);

        $this->dispatch('dashboard:charts-updated', [
            'monthly' => $this->monthlyChart,
            'yearly' => $this->yearlyChart,
            'categories' => $this->categoryChart,
        ]);
    }

    public function toggleEntryStatus(int $entryId): void
    {
        $entry = Entry::where('user_id', auth()->id())
            ->findOrFail($entryId);

        $entry->update([
            'status' => $entry->status === 'paid' ? 'pending' : 'paid',
        ]);

        $this->loadDashboard();
    }

    private function buildYearlyChart(int $year): array
    {
        $income = [];
        $expenses = [];

        for ($month = 1; $month <= 12; $month++) {
            $income[] = (float) Entry::where('user_id', auth()->id())
                ->whereYear('reference_date', $year)
                ->whereMonth('reference_date', $month)
                ->where('status', 'paid')
                ->whereHas('transaction', fn ($query) => $query->where('type', 'income'))
                ->sum('value');

            $expenses[] = (float) Entry::where('user_id', auth()->id())
                ->whereYear('reference_date', $year)
                ->whereMonth('reference_date', $month)
                ->where('status', 'paid')
                ->whereHas('transaction', fn ($query) => $query->where('type', 'expense'))
                ->sum('value');
        }

        return [
            'labels' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            'income' => $income,
            'expenses' => $expenses,
        ];
    }

    private function buildCategoryChart(int $year, int $month): array
    {
        $entries = Entry::with('transaction.category')
            ->where('user_id', auth()->id())
            ->whereYear('reference_date', $year)
            ->whereMonth('reference_date', $month)
            ->where('status', 'paid')
            ->whereHas('transaction', fn ($query) => $query->where('type', 'expense'))
            ->get();

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

    private function buildCards(Carbon $reference): array
    {
        return CreditCard::where('user_id', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(fn ($card) => app(CreditCardInvoiceService::class)
                ->getInvoice(auth()->id(), $card->id, $reference))
            ->toArray();
    }

    private function buildLatestEntries(Carbon $start, Carbon $end)
    {
        return Entry::with(['transaction.category', 'account', 'creditCard'])
            ->where('user_id', auth()->id())
            ->whereBetween('reference_date', [$start, $end])
            ->orderByRaw("status = 'pending' desc")
            ->orderByDesc('reference_date')
            ->limit(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
