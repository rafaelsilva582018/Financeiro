<?php

namespace App\Services;

use App\Models\CreditCard;
use App\Models\Entry;
use Carbon\Carbon;

class CreditCardInvoiceService
{
    /**
     * Retorna a fatura de um cartão em um mês
     */
    public function getInvoice(
        int $userId,
        int $creditCardId,
        Carbon $month
    ): array {
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd   = $month->copy()->endOfMonth();

        $card = CreditCard::where('id', $creditCardId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $entries = Entry::where('user_id', $userId)
            ->where('credit_card_id', $card->id)
            ->whereBetween('reference_date', [$monthStart, $monthEnd])
            ->get();

        $invoiceTotal = $entries->sum('value');
        $openUsed = Entry::where('user_id', $userId)
            ->where('credit_card_id', $card->id)
            ->where('status', 'pending')
            ->sum('value');

        return [
            'card'  => $card,
            'month' => $month->format('Y-m'),
            'limit' => (float) $card->limit,
            'used'  => (float) $invoiceTotal,
            'available' => (float) ($card->limit - $invoiceTotal),
            'open_used' => (float) $openUsed,
            'open_available' => (float) ($card->limit - $openUsed),
            'entries' => $entries,
        ];
    }
}
