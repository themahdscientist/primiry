<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = 'dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::ChartBar;

    public $defaultAction = 'greetings';

    public function greetings(): Action
    {
        $name = filament()->auth()->user()->name;

        Notification::make('greetings')
            ->title('Welcome back '.$name)
            ->success()
            // ->status()
            // ->iconColor()
            // ->color()
            ->icon(Heroicon::User)
            // ? get an icon that's waving.
            ->send();

        return Action::make('greetings');
    }
}
