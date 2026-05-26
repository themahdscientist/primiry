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
        $user = auth()->user();

        if (session()->has('greeted')) {
            return Action::make('greetings');
        }

        Notification::make()
            ->title("Welcome back, {$user->name}!")
            ->body('We hope you have a great day using our application.')
            ->success()
            ->persistent()
            ->send();

        session()->put('greeted', true);

        return Action::make('greetings');
    }
}
