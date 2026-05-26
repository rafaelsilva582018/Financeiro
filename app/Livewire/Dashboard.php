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

    public string $selectedMonthName = '';

    public int $selectedMonthYear = 0;

    public string $previousMonth = '';

    public string $nextMonth = '';

    public $entries = [];

    protected $listeners = [
        'transaction-created' => 'loadDashboard',
    ];

    public function mount(): void
    {
        $requestedMonth = request('month');

        $this->month = is_string($requestedMonth) && preg_match('/^\d{4}-\d{2}$/', $requestedMonth)
            ? $requestedMonth
            : now()->format('Y-m');

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

        $monthNames = [
            1 => 'Jan',
            2 => 'Fev',
            3 => 'Mar',
            4 => 'Abr',
            5 => 'Mai',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ago',
            9 => 'Set',
            10 => 'Out',
            11 => 'Nov',
            12 => 'Dez',
        ];

        $this->selectedMonthName = $monthNames[$reference->month];
        $this->selectedMonthYear = $reference->year;
        $this->selectedMonthLabel = "{$this->selectedMonthName}/{$this->selectedMonthYear}";
        $this->previousMonth = $reference->copy()->subMonth()->format('Y-m');
        $this->nextMonth = $reference->copy()->addMonth()->format('Y-m');

        $this->summary = app(MonthlySummaryService::class)
            ->getSummary(auth()->id(), $reference);

        $income = (float) ($this->summary['income'] ?? 0);
        $expenses = (float) ($this->summary['expenses'] ?? 0);
        $finalBalance = (float) ($this->summary['final_balance'] ?? 0);
        $totalMovement = $income + $expenses;

        $pendingTotal = (float) $this->visibleEntriesQuery($start, $end)
            ->where('status', 'pending')
            ->sum('value');

        $paidCount = $this->visibleEntriesQuery($start, $end)
            ->where('status', 'paid')
            ->count();

        $pendingCount = $this->visibleEntriesQuery($start, $end)
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
        return $this->visibleEntriesQuery($start, $end)
            ->with(['transaction.category', 'account', 'creditCard'])
            ->orderByRaw("status = 'pending' desc")
            ->orderByDesc('reference_date')
            ->limit(8)
            ->get();
    }

    private function visibleEntriesQuery(Carbon $start, Carbon $end)
    {
        return Entry::where('user_id', auth()->id())
            ->whereHas('transaction')
            ->where(function ($query) use ($start, $end) {
                $query
                    ->whereBetween('reference_date', [$start, $end])
                    ->orWhere(function ($purchaseQuery) use ($start, $end) {
                        $purchaseQuery
                            ->where('entries.id', function ($firstEntryQuery) {
                                $firstEntryQuery
                                    ->select('first_entry.id')
                                    ->from('entries as first_entry')
                                    ->whereColumn('first_entry.transaction_id', 'entries.transaction_id')
                                    ->orderBy('first_entry.reference_date')
                                    ->orderBy('first_entry.id')
                                    ->limit(1);
                            })
                            ->whereHas('transaction', function ($transactionQuery) use ($start, $end) {
                                $transactionQuery->whereBetween('start_date', [$start, $end]);
                            });
                    });
            });
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
