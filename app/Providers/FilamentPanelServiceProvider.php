<?php

namespace App\Providers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\BasePage;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class FilamentPanelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Action::configureUsing(function (Action $action): void {
            $action
                ->modalCloseButton(false)
                ->iconSize(IconSize::Small);
        });

        ViewAction::configureUsing(function (ViewAction $action): void {
            $action
                ->iconButton();
        });

        EditAction::configureUsing(function (EditAction $action): void {
            $action
                ->iconButton();
        });

        DeleteAction::configureUsing(function (DeleteAction $action): void {
            $action
                ->iconButton();
        });

        ForceDeleteAction::configureUsing(function (ForceDeleteAction $action): void {
            $action
                ->iconButton();
        });

        RestoreAction::configureUsing(function (RestoreAction $action): void {
            $action
                ->iconButton();
        });

        DetachAction::configureUsing(function (DetachAction $action): void {
            $action
                ->iconButton();
        });

        DissociateAction::configureUsing(function (DissociateAction $action): void {
            $action
                ->iconButton();
        });

        Column::configureUsing(function (Column $column): void {
            $column
                ->alignCenter();
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->deferLoading()
                ->striped()
                ->persistSearchInSession()
                ->persistColumnSearchesInSession()
                ->emptyStateActions([CreateAction::make()]);
        });

        Summarizer::configureUsing(function (Summarizer $summarizer): void {
            $summarizer
                ->label('');
        });

        Field::configureUsing(function (Field $field): void {
            if ($field instanceof TextInput || $field instanceof Textarea) {
                $field
                    ->dehydrated(fn (mixed $state) => filled($state))
                    ->dehydrateStateUsing(fn (mixed $state) => Str::squish($state));
            }

            if ($field instanceof TextInput) {
                $field
                    ->maxLength(255);
            }

            if ($field instanceof Textarea) {
                $field
                    ->maxLength(65535);
            }

            $field
                ->live(
                    debounce: 300,
                    condition: fn (Field $component): bool => ! ($component instanceof TextInput && $component->getType() === 'password'),
                )
                ->afterStateUpdated(function (BasePage $livewire, Field $component): void {
                    if ($component instanceof TextInput && $component->getType() === 'password') {
                        return;
                    }

                    $livewire->validateOnly($component->getStatePath());
                });
        });
    }
}
