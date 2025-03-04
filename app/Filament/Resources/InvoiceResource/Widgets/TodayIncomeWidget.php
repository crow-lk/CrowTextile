<?php

namespace App\Filamemt\Resources\InvoiceResource\Widgets\TodayIncomeWidget;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Invoice;
use Flowframe\Trend\Trend;

class TodayIncomeWidget extends BaseWidget
{
    protected static string $view = 'filament-widgets::stats-overview-widget';

    protected function getColumns(): int
    {
        return 2; // Adjust the number of columns as needed
    }

    protected function getStats(): array
    {
        // Query for today's invoices
        $invoiceQuery = Invoice::query();

        $invoiceTypeQuery = (clone $invoiceQuery)->where('is_invoice', true);

        // Trend data for invoices in the last 30 days
        $trendc = Trend::query((clone $invoiceTypeQuery))
            ->interval('day')
            ->dateColumn('created_at')
            ->between(now()->subDays(30), now())
            ->count();

        // Total income for today
        $todayIncome = (clone $invoiceTypeQuery)->whereDate('created_at', Carbon::today())->sum('amount'); // Replace 'total_amount' with your income column name

        return [
            // Number of invoices today
            Stat::make('Invoices Today', (clone $invoiceTypeQuery)->whereDate('created_at', Carbon::today())->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->value((clone $invoiceQuery)->whereDate('created_at', Carbon::today())->where('is_invoice', true)->count())
                ->chart($trendc->pluck('aggregate')->toArray())
                ->color('primary')
                ->icon('heroicon-o-clipboard-document-check'),

            // Total income today
            Stat::make('Total Income Today', $todayIncome)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->value($todayIncome)
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),
        ];
    }
}
