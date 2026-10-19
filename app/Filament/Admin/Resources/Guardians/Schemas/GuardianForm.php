<?php

namespace App\Filament\Admin\Resources\Guardians\Schemas;

use App\Enums\MaritalStatus;
use App\Enums\Sex;
use App\Models\Guardian;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Validation\Rules\Unique;
use Nette\Utils\Random;
use RalphJSmit\Filament\Components\Forms\CreatedAt;
use RalphJSmit\Filament\Components\Forms\DeletedAt;
use RalphJSmit\Filament\Components\Forms\Sidebar;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class GuardianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Sidebar::make([
                    Section::make()
                        ->columns()
                        ->columnSpanFull()
                        ->schema([
                            Section::make([
                                TextInput::make('user.first_name')
                                    ->required()
                                    ->maxLength(85)
                                    ->autofocus(),
                                TextInput::make('user.last_name')
                                    ->required()
                                    ->maxLength(85),
                                TextInput::make('user.nin')
                                    ->label(__('NIN'))
                                    ->hintIcon(__('resources/pages/create-record.hint-icon.name'))
                                    ->hintIconTooltip(__('National Identification Number (11 digits)'))
                                    ->length(11)
                                    ->numeric()
                                    ->scopedUnique(User::class, 'nin', fn(?Guardian $record): ?User => $record?->user)
                                    ->extraInputAttributes([
                                        'inputmode' => 'numeric',
                                        'pattern' => '[0-9]*',
                                    ])
                                    ->validationAttribute('NIN')
                                    ->validationMessages(['unique' => 'This :attribute belongs to another.'])
                                    ->required()
                                    ->afterStateUpdated(
                                        fn($state, $get) => null// verify nin
                                    )
                                    ->suffixAction(
                                        Action::make('verify')
                                            ->icon('heroicon-s-identification')
                                            ->iconButton()
                                            ->tooltip('Verify NIN')
                                            ->action(
                                                fn($state, $get) => static::verifyNIN($state, $get)
                                            )
                                    ),
                            ]),
                            Section::make([
                                TextInput::make('user.email')
                                    ->email()
                                    ->label(__('resources/pages/create-record.form.email.label'))
                                    ->required()
                                    ->maxLength(255)
                                    ->scopedUnique(User::class, 'email', fn(?Guardian $record): ?User => $record?->user),
                                PhoneInput::make('user.phone')
                                    ->label(__('resources/pages/create-record.form.phone.label'))
                                    ->prefixIcon('heroicon-s-phone')
                                    ->defaultCountry(fn() => filament()->getTenant()->settings['address']['country_iso2'])
                                    ->autoPlaceholder('aggressive')
                                    ->strictMode()
                                    ->required(),
                                Select::make('user.sex')
                                    ->options(Sex::class)
                                    ->required()
                                    ->native(false),
                            ]),
                            Section::make([
                                TextInput::make('code')
                                    ->default(fn() => strtoupper(Random::generate()))
                                    ->disabled()
                                    ->dehydrated(fn(string $operation): bool => $operation === Operation::Create->value)
                                    ->dehydrateStateUsing(fn(string $state): string => strtolower($state))
                                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                                    ->scopedUnique()
                                    ->required(),
                                Select::make('marital_status')
                                    ->options(MaritalStatus::class)
                                    ->required()
                                    ->native(false),
                                TextInput::make('occupation')
                                    ->required(),
                            ])
                                ->columnSpanFull(),
                        ]),
                ], [
                    Section::make([
                        CreatedAt::make('Created'),
                        DeletedAt::make('Deleted'),
                    ])
                        ->columnSpanFull(),
                    Section::make([
                        FileUpload::make('user.avatar')
                            ->image()
                            ->previewable(fn(string $operation): bool => $operation === Operation::Edit->value || $operation === Operation::View->value)
                            ->label(__('resources/pages/create-record.form.avatar.label'))
                            ->hintIcon(__('resources/pages/create-record.form.avatar.icon.name'))
                            ->hintIconTooltip(__('resources/pages/create-record.form.avatar.icon.text'))
                            ->disk('public')
                            ->directory('guardians')
                            ->visibility('public')
                            ->storeFileNamesIn('user.original_avatar')
                            ->fetchFileInformation()
                            ->maxSize(1024)
                            ->maxFiles(1)
                            ->dehydrated(fn($state) => $state != null),
                    ])
                        ->columnSpanFull(),
                ])
                    ->columnSpanFull(),
            ]);
    }

    protected static function verifyNIN(mixed $state, Get $get)
    {
        Notification::make('NIN Verification')
            ->title('NIN Verification is not yet implemented.')
            ->body('The NIN verification feature is currently under development and will be available in a future update.')
            ->warning()
            ->send();

        // verify nin
    }
}
