<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Order', \App\Models\Order::count()),
            Stat::make('Order Terverifikasi', \App\Models\Order::where('is_valid', true)->count()),
            Stat::make('Pemasukan', function () {
                return 'Rp ' . number_format(\App\Models\Order::where('is_valid', true)->join('packages', 'orders.package_id', '=', 'packages.id')->sum('packages.price'), 2);
            }),
        ];
    }
}
