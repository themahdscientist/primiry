<?php

namespace App\Providers;

use App\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class DbGrammarServiceProvider extends ServiceProvider
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
        $connection = DB::connection();
        $connection->setQueryGrammar(new MySqlGrammar($connection));

    }
}
