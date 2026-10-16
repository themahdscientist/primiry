<?php

namespace App\Filament\Pages\Auth;

use App\Enums\Sex;
use App\Models\Role;
use Filament\Actions\Action;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use SensitiveParameter;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Register extends BaseRegister
{
    protected Width|string|null $maxContentWidth = Width::ExtraLarge;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        $this->getNameFormComponent()
                            ->columnSpanFull(),
                        Select::make('sex')
                            ->options(Sex::class)
                            ->required()
                            ->native(false),
                        PhoneInput::make('phone')
                            ->label('Phone number')
                            ->prefixIcon('heroicon-s-phone')
                            ->autoPlaceholder('aggressive')
                            ->strictMode()
                            ->required(),
                        $this->getEmailFormComponent()
                            ->columnSpanFull(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
            ]);
    }

    protected function handleRegistration(#[SensitiveParameter] array $data): Model
    {
        $user = $this->getUserModel()::create($data)->role()->associate(Role::ADMIN);
        $user->save();

        return $user;
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label(__('filament-panels::auth/pages/register.form.actions.register.label'))
            ->submit('register')
            ->icon('heroicon-s-rocket-launch')
            ->iconPosition('after');
    }
}
