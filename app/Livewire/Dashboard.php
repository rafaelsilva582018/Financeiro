<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Transaction;
use App\Models\CreditCard;
use App\Services\MonthlySummaryService;
use App\Services\CreditCardInvoiceService;

class Dashboard extends Component
{
    public string $month;

    public array $summary = [];
    public $entries = [];
    public $cards = [];

    protected $listeners = [
        'transaction-created' => '$refresh',
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

    private function loadDashboard(): void
    {
        [$year, $month] = explode('-', $this->month);
        $reference = Carbon::create($year, $month, 1);

        /* ===============================
         * 📅 DADOS ANUAIS
         * =============================== */
        $yearlyIncome = [];
        $yearlyExpenses = [];

        for ($m = 1; $m <= 12; $m++) {
            $yearlyIncome[] = Entry::where('user_id', auth()->id())
                ->whereYear('reference_date', $year)
                ->whereMonth('reference_date', $m)
                ->where('status', 'paid')
                ->whereHas('transaction', fn ($q) => $q->where('type', 'income'))
                ->sum('value');

            $yearlyExpenses[] = Entry::where('user_id', auth()->id())
                ->whereYear('reference_date', $year)
                ->whereMonth('reference_date', $m)
                ->where('status', 'paid')
                ->whereHas('transaction', fn ($q) => $q->where('type', 'expense'))
                ->sum('value');
        }

        /* ===============================
         * 📊 RESUMO MENSAL
         * =============================== */
        $this->summary = app(MonthlySummaryService::class)
            ->getSummary(auth()->id(), $reference);

        /* ===============================
         * 📈 RECEITAS / DESPESAS
         * =============================== */
        $income = Entry::where('user_id', auth()->id())
            ->whereYear('reference_date', $year)
            ->whereMonth('reference_date', $month)
            ->where('status', 'paid')
            ->whereHas('transaction', fn ($q) => $q->where('type', 'income'))
            ->sum('value');

        $expenses = Entry::where('user_id', auth()->id())
            ->whereYear('reference_date', $year)
            ->whereMonth('reference_date', $month)
            ->where('status', 'paid')
            ->whereHas('transaction', fn ($q) => $q->where('type', 'expense'))
            ->sum('value');

        /* ===============================
         * 🥧 DESPESAS POR CATEGORIA
         * =============================== */
        $expensesByCategory = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereHas('entries', fn ($q) =>
                $q->whereYear('reference_date', $year)
                  ->whereMonth('reference_date', $month)
                  ->where('status', 'paid')
            )
            ->with('category')
            ->get()
            ->groupBy(fn ($t) => $t->category->name ?? 'Sem categoria')
            ->map(fn ($items) => $items->sum('total_value'))
            ->filter();

        /* ===============================
         * 📤 DISPATCH GRÁFICOS
         * =============================== */
        $this->dispatch('charts:update', [
            'income' => $income,
            'expenses' => $expenses,
            'categories' => [
                'labels' => $expensesByCategory->keys()->values()->toArray(),
                'values' => $expensesByCategory->values()->values()->toArray(),
            ],
        ]);

        $this->dispatch('charts:yearly', [
            'income' => $yearlyIncome,
            'expenses' => $yearlyExpenses,
        ]);

        /* ===============================
         * 💳 CARTÕES
         * =============================== */
        $this->cards = CreditCard::where('user_id', auth()->id())
            ->get()
            ->map(fn ($card) =>
                app(CreditCardInvoiceService::class)
                    ->getInvoice(auth()->id(), $card->id, $reference)
            )
            ->toArray();

        /* ===============================
         * 🧾 ÚLTIMOS LANÇAMENTOS
         * =============================== */
        $this->entries = Entry::with('transaction')
            ->where('user_id', auth()->id())
            ->whereYear('reference_date', $year)
            ->whereMonth('reference_date', $month)
            ->orderByDesc('reference_date')
            ->limit(5)
            ->get();
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

    public function render()
    {
        return view('livewire.dashboard');
    }
}
