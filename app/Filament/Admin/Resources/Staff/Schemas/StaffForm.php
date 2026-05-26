<?php

namespace App\Filament\Admin\Resources\Staff\Schemas;

use App\Enums\EmploymentType;
use App\Enums\MaritalStatus;
use App\Enums\Sex;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;
use Nette\Utils\Random;
use RalphJSmit\Filament\Components\Forms\CreatedAt;
use RalphJSmit\Filament\Components\Forms\DeletedAt;
use RalphJSmit\Filament\Components\Forms\Sidebar;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class StaffForm
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
                                TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(85)
                                    ->autofocus(),
                                TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(85),
                                TextInput::make('nin')
                                    ->label(__('NIN'))
                                    ->hintIcon(__('resources/pages/create-record.hint-icon.name'))
                                    ->hintIconTooltip(__('National Identification Number (11 digits)'))
                                    ->length(11)
                                    ->numeric()
                                    // ->unique(User::class, 'nin', modifyRuleUsing: fn(Unique $rule) => $rule->where('role_id', Role::STAFF)) this scope's the search to only staff [unnecessary]
                                    ->unique(User::class, 'nin')
                                    ->extraInputAttributes([
                                        'inputmode' => 'numeric',
                                        'pattern' => '[0-9]*',
                                    ])
                                    ->validationAttribute('NIN')
                                    ->validationMessages(['unique' => 'This :attribute belongs to another.'])
                                    ->required()
                                    ->afterStateUpdated(
                                        fn ($state, $get) => null// verify nin
                                    )
                                    ->suffixAction(
                                        Action::make('verify')
                                            ->icon('heroicon-s-identification')
                                            ->iconButton()
                                            ->tooltip('Verify NIN')
                                            ->action(
                                                fn ($state, $get) => static::verifyNIN($state, $get)
                                            )
                                    ),
                            ]),
                            Section::make([
                                TextInput::make('email')
                                    ->email()
                                    ->label(__('resources/pages/create-record.form.email.label'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(User::class, 'email'),
                                PhoneInput::make('phone')
                                    ->label(__('resources/pages/create-record.form.phone.label'))
                                    ->prefixIcon('heroicon-s-phone')
                                    ->defaultCountry(fn () => filament()->auth()->user()->getDefaultCountry())
                                    ->autoPlaceholder('aggressive')
                                    ->strictMode()
                                    ->required(),
                                Select::make('sex')
                                    ->options(Sex::class)
                                    ->required()
                                    ->native(false),
                            ]),
                            Section::make([
                                TextInput::make('staff.code')
                                    ->default(fn () => strtoupper(Random::generate()))
                                    ->disabled()
                                    ->dehydrated(fn (string $operation): bool => $operation === 'create')
                                    ->dehydrateStateUsing(fn (string $state): string => strtolower($state))
                                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                                    ->unique(Staff::class, 'code', modifyRuleUsing: fn (Unique $rule, ?User $record) => $rule->ignore($record?->id, 'user_id'))
                                    ->required(),
                                Select::make('staff.marital_status')
                                    ->options(MaritalStatus::class)
                                    ->required()
                                    ->native(false),
                                PhoneInput::make('staff.emergency_contact')
                                    ->label(__('resources/pages/create-staff.form.emergency-contact.label'))
                                    ->prefixIcon('heroicon-s-phone')
                                    ->defaultCountry(fn () => filament()->auth()->user()->getDefaultCountry())
                                    ->autoPlaceholder('aggressive')
                                    ->strictMode()
                                    ->required(),
                            ]),
                            Section::make()
                                ->schema([
                                    TextInput::make('staff.bank_account_details.account_name')
                                        ->required(),
                                    TextInput::make('staff.bank_account_details.account_number')
                                        ->numeric()
                                        ->label(__('resources/pages/create-staff.form.account-number.label'))
                                        ->hintIcon(__('resources/pages/create-staff.form.account-number.icon.name'))
                                        ->hintIconTooltip(__('resources/pages/create-staff.form.account-number.icon.text'))
                                        ->length(10)
                                        // ->unique(Staff::class, 'bank_account_details.account_number')
                                        ->extraInputAttributes([
                                            'inputmode' => 'numeric',
                                            'pattern' => '[0-9]*',
                                        ])
                                        ->required(),
                                    Select::make('staff.bank_account_details.bank_name')
                                        ->options([
                                            'first_bank' => 'FirstBank of Nigeria',
                                        ])
                                        ->native(false)
                                        ->required(),
                                ]),
                            Section::make()
                                ->schema([
                                    TextInput::make('staff.salary')
                                        ->numeric()
                                        ->formatStateUsing(fn (mixed $state) => $state) // typecasting (decimal is having problem with displaying more than 255 characters when the "." character is trailing the number)
                                        ->required(),
                                    Select::make('staff.employment_type')
                                        ->options(EmploymentType::class)
                                        ->default(EmploymentType::FullTime)
                                        ->required()
                                        ->native(false),
                                ])
                                ->columnSpanFull(),
                        ]),
                ], [
                    Section::make([
                        CreatedAt::make('Hired'),
                        DeletedAt::make('Fired'),
                    ])
                        ->columnSpanFull(),
                    Section::make([
                        FileUpload::make('avatar')
                            ->image()
                            ->previewable(fn (string $operation) => $operation === 'edit' || $operation === 'view')
                            ->label(__('resources/pages/create-record.form.avatar.label'))
                            ->hintIcon(__('resources/pages/create-record.form.avatar.icon.name'))
                            ->hintIconTooltip(__('resources/pages/create-record.form.avatar.icon.text'))
                            ->disk('public')
                            ->directory('staff')
                            ->visibility('public')
                            ->storeFileNamesIn('original_avatar')
                            ->fetchFileInformation(true)
                            ->maxSize(1024)
                            ->maxFiles(1)
                            ->dehydrated(fn ($state) => $state != null),
                        FileUpload::make('staff.qualifications')
                            ->previewable(false)
                            ->label(__('resources/pages/create-staff.form.qualifications.label'))
                            ->hintIcon(__('resources/pages/create-staff.form.qualifications.icon.name'))
                            ->hintIconTooltip(__('resources/pages/create-staff.form.qualifications.icon.text'))
                            ->disk('local')
                            ->directory('staff')
                            ->visibility('private')
                            ->fetchFileInformation(true)
                            ->maxSize(1024)
                            ->maxFiles(2)
                            ->maxParallelUploads(2)
                            ->multiple()
                            ->dehydrated(fn ($state) => $state != null),
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
