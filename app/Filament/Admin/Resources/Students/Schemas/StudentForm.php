<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use App\Enums\BloodGroup;
use App\Enums\Religion;
use App\Enums\RhesusFactor;
use App\Enums\Sex;
use App\Enums\StudentStatus;
use App\Enums\StudentType;
use App\Filament\Admin\Resources\Guardians\Schemas\GuardianForm;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;
use Nette\Utils\Random;
use RalphJSmit\Filament\Components\Forms\CreatedAt;
use RalphJSmit\Filament\Components\Forms\DeletedAt;
use RalphJSmit\Filament\Components\Forms\Sidebar;
use RalphJSmit\Filament\Components\Forms\Timestamp;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class StudentForm
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
                                TextInput::make('user.middle_name')
                                    ->maxLength(85),
                                TextInput::make('user.last_name')
                                    ->required()
                                    ->maxLength(85),
                            ]),
                            Section::make([
                                TextInput::make('user.email')
                                    ->email()
                                    ->label(__('resources/pages/create-record.form.email.label'))
                                    ->maxLength(255)
                                    ->scopedUnique(User::class, 'email', fn(?Student $record): ?User => $record?->user),
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
                                PhoneInput::make('emergency_contact')
                                    ->label(__('resources/pages/create-student.form.emergency-contact.label'))
                                    ->prefixIcon('heroicon-s-phone')
                                    ->defaultCountry(fn() => filament()->getTenant()->settings['address']['country_iso2'])
                                    ->autoPlaceholder('aggressive')
                                    ->strictMode()
                                    ->required(),
                                Select::make('blood_group')
                                    ->label(__('Blood group'))
                                    ->options(BloodGroup::class)
                                    ->required()
                                    ->native(false),
                                Select::make('rhesus_factor')
                                    ->label(__('Rhesus factor'))
                                    ->options(RhesusFactor::class)
                                    ->required()
                                    ->native(false),
                            ]),
                            Section::make([
                                DatePicker::make('dob')
                                    ->label(__('resources/pages/create-student.form.dob.label'))
                                    ->required()
                                    ->native(false),
                                Select::make('religion')
                                    ->label(__('Religion'))
                                    ->options(Religion::class)
                                    ->required()
                                    ->native(false),
                                Select::make('type')
                                    ->label(__('Type'))
                                    ->options(StudentType::class)
                                    ->required()
                                    ->native(false),
                            ]),
                            Section::make([
                                Select::make('nationality')
                                    ->label(__('Nationality'))
                                    ->relationship('countryOfOrigin', 'name')
                                    ->default(fn() => filament()->getTenant()->settings['address']['country_iso2'])
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('state_of_origin', null);
                                    }),
                                Select::make('state_of_origin')
                                    ->label(__('resources/pages/create-student.form.state-of-origin.label'))
                                    ->disabled(fn(Get $get): bool => !$get('nationality'))
                                    ->relationship('stateOfOrigin', 'name', fn(Builder $query, Get $get) => $query->where('country_iso2', $get('nationality')))
                                    ->default(fn() => filament()->getTenant()->settings['address']['state_code'])
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('lga_of_origin')
                                    ->label(__('resources/pages/create-student.form.lga-of-origin.label'))
                                    ->required()
                                    ->maxLength(255),
                            ]),
                            Section::make([
                                Select::make('country_of_residence')
                                    ->label(__('resources/pages/create-student.form.country-of-residence.label'))
                                    ->relationship('countryOfResidence', 'name')
                                    ->default(fn() => filament()->getTenant()->settings['address']['country_iso2'])
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('state_of_residence', null);
                                    }),
                                Select::make('state_of_residence')
                                    ->label(__('resources/pages/create-student.form.state-of-residence.label'))
                                    ->disabled(fn(Get $get): bool => !$get('country_of_residence'))
                                    ->relationship('stateOfResidence', 'name', fn(Builder $query, Get $get) => $query->where('country_iso2', $get('country_of_residence')))
                                    ->default(fn() => filament()->getTenant()->settings['address']['state_code'])
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('city_of_residence')
                                    ->label(__('resources/pages/create-student.form.city-of-residence.label'))
                                    ->required()
                                    ->maxLength(255),
                            ]),
                            Section::make([
                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options(StudentStatus::class)
                                    ->native(false)
                                    ->required(),
                                Select::make('classroom_id')
                                    ->label(__('Classroom'))
                                    ->relationship('classroom', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('guardian_id')
                                    ->label(__('Guardian'))
                                    ->relationship('guardian', 'id')
                                    ->getOptionLabelFromRecordUsing(fn(Guardian $record) => $record->user->name)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                // consider having a createoptionform here,
                            ]),
                            Section::make([
                                TextInput::make('admission_number')
                                    ->default(fn() => strtoupper(Random::generate()))
                                    ->disabled()
                                    ->dehydrated(fn(string $operation): bool => $operation === Operation::Create->value)
                                    ->dehydrateStateUsing(fn(string $state): string => strtolower($state))
                                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                                    ->scopedUnique()
                                    ->required(),
                                TextInput::make('user.nin')
                                    ->label(__('NIN'))
                                    ->hintIcon(__('resources/pages/create-record.hint-icon.name'))
                                    ->hintIconTooltip(__('National Identification Number (11 digits)'))
                                    ->length(11)
                                    ->numeric()
                                    ->scopedUnique(User::class, 'nin', fn(?Student $record): ?User => $record?->user)
                                    ->extraInputAttributes([
                                        'inputmode' => 'numeric',
                                        'pattern' => '[0-9]*',
                                    ])
                                    ->validationAttribute('NIN')
                                    ->validationMessages(['unique' => 'This :attribute belongs to another.'])
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

                        ]),
                ], [
                    Section::make([
                        CreatedAt::make('Admitted'),
                        Timestamp::make('created_at', 'Graduated')
                            ->state(fn(?Student $record) => $record?->graduated_at ? $record?->graduated_at->diffForHumans() : '-'),
                        DeletedAt::make('Expelled/Left'),
                    ])
                        ->columnSpanFull(),
                    Section::make([
                        FileUpload::make('user.avatar')
                            ->image()
                            ->previewable(fn(string $operation) => $operation === 'edit' || $operation === 'view')
                            ->label(__('resources/pages/create-record.form.avatar.label'))
                            ->hintIcon(__('resources/pages/create-record.form.avatar.icon.name'))
                            ->hintIconTooltip(__('resources/pages/create-record.form.avatar.icon.text'))
                            ->disk('public')
                            ->directory('students')
                            ->visibility('public')
                            ->storeFileNamesIn('original_avatar')
                            ->fetchFileInformation(true)
                            ->maxSize(1024)
                            ->maxFiles(1)
                            ->dehydrated(fn($state) => $state != null),
                    ])
                        ->columnSpanFull(),
                ])
                    ->columnSpanFull(),
            ]);
    }
}
