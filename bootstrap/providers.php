<?php

use App\Providers\AppServiceProvider;
use App\Providers\DbGrammarServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\FilamentPanelServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    FortifyServiceProvider::class,
    DbGrammarServiceProvider::class,
    FilamentPanelServiceProvider::class,
];
