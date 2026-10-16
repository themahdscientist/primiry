<?php

use App\Filament\Admin\Resources\Guardians\Tables\GuardiansTable;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

test('password fields are not live while other text inputs remain live', function () {
    $password = TextInput::make('password')->password();
    $name = TextInput::make('name');

    expect($password->isLive())->toBeFalse()
        ->and($name->isLive())->toBeTrue();
});

test('guardian avatar table column reads images from the public disk', function () {
    $table = GuardiansTable::configure(
        Table::make(Mockery::mock(HasTable::class)),
    );

    expect($table->getColumn('user.avatar')->getDiskName())->toBe('public');
});
