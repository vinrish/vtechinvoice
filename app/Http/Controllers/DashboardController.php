<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

final class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        // Top-level totals
        $totals = [
            'clients' => Client::count(),
            'invoices' => Invoice::count(),
            'revenue' => (float) (Invoice::sum('total') ?? 0),
            'outstanding' => (float) (Invoice::whereIn('status', ['unpaid', 'overdue', 'pending'])->sum('total') ?? 0),
        ];

        // Invoices by status (count and total)
        $statusSummary = Invoice::query()
            ->selectRaw('COALESCE(NULLIF(status, ""), "unknown") as status')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total) as total')
            ->groupBy('status')
            ->get()
            ->map(fn ($row): array => [
                'status' => (string) $row->status,
                'count' => (int) $row->count,
                'total' => (float) $row->total,
            ]);

        // Revenue by month for last 6 months (including current)
        $months = [];
        $start = Carbon::now()->startOfMonth()->subMonths(5);
        $end = Carbon::now()->endOfMonth();

        $invoices = Invoice::query()
            ->whereBetween('issued_at', [$start, $end])
            ->get(['issued_at', 'total']);

        // Initialize months map
        for ($i = 0; $i < 6; $i++) {
            $m = (clone $start)->addMonths($i);
            $key = $m->format('Y-m');
            $months[$key] = 0.0;
        }

        foreach ($invoices as $inv) {
            if ($inv->issued_at) {
                $key = Carbon::parse($inv->issued_at)->format('Y-m');
                if (array_key_exists($key, $months)) {
                    $months[$key] += (float) $inv->total;
                }
            }
        }

        $revenueByMonth = [];
        foreach ($months as $ym => $total) {
            $revenueByMonth[] = [
                'month' => $ym,
                'total' => round($total, 2),
            ];
        }

        return Inertia::render('Dashboard', [
            'totals' => $totals,
            'statusSummary' => $statusSummary,
            'revenueByMonth' => $revenueByMonth,
        ])->toResponse(request());
    }
}
