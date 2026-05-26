<?php

namespace App\Database\Query\Grammars;

use Illuminate\Database\Query\Grammars\MySqlGrammar as BaseMySqlGrammar;

class MySqlGrammar extends BaseMySqlGrammar
{
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.u';
    }
}
