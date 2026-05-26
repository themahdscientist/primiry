<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use App\Enums\BloodGroup;
use App\Enums\Religion;
use App\Enums\RhesusFactor;
use App\Enums\Sex;
use App\Enums\StudentStatus;
use App\Enums\StudentType;
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
                                TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(85)
                                    ->autofocus(),
                                TextInput::make('middle_name')
                                    ->required()
                                    ->maxLength(85),
                                TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(85),
                            ]),
                            Section::make([
                                TextInput::make('email')
                                    ->email()
                                    ->label(__('resources/pages/create-record.form.email.label'))
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
                                PhoneInput::make('student.emergency_contact')
                                    ->label(__('resources/pages/create-student.form.emergency-contact.label'))
                                    ->prefixIcon('heroicon-s-phone')
                                    ->defaultCountry(fn () => filament()->auth()->user()->getDefaultCountry())
                                    ->autoPlaceholder('aggressive')
                                    ->strictMode()
                                    ->required(),
                                Select::make('student.blood_group')
                                    ->label(__('Blood group'))
                                    ->options(BloodGroup::class)
                                    ->required()
                                    ->native(false),
                                Select::make('student.rhesus_factor')
                                    ->label(__('Rhesus factor'))
                                    ->options(RhesusFactor::class)
                                    ->required()
                                    ->native(false),
                            ]),
                            Section::make([
                                DatePicker::make('student.dob')
                                    ->label(__('resources/pages/create-student.form.dob.label'))
                                    ->required()
                                    ->native(false),
                                Select::make('student.religion')
                                    ->label(__('Religion'))
                                    ->options(Religion::class)
                                    ->required()
                                    ->native(false),
                                Select::make('student.type')
                                    ->label(__('Type'))
                                    ->options(StudentType::class)
                                    ->required()
                                    ->native(false),
                            ]),
                            Section::make([
                                Select::make('student.nationality')
                                    ->label(__('Nationality'))
                                    ->relationship('student.nationalityy', 'name')
                                    ->default(fn () => filament()->auth()->user()->getDefaultCountry())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('student.state_of_origin', null);
                                    }),
                                Select::make('student.state_of_origin')
                                    ->label(__('resources/pages/create-student.form.state-of-origin.label'))
                                    ->disabled(fn (Get $get): bool => ! $get('student.nationality'))
                                    ->relationship('student.stateOfOrigin', 'name', fn (Builder $query, Get $get) => $query->where('country_iso2', $get('student.nationality')))
                                    ->default(fn () => filament()->auth()->user()->getDefaultState())
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('student.lga_of_origin')
                                    ->label(__('resources/pages/create-student.form.lga-of-origin.label'))
                                    ->required()
                                    ->maxLength(255),
                            ]),
                            Section::make([
                                Select::make('student.country_of_residence')
                                    ->label(__('resources/pages/create-student.form.country-of-residence.label'))
                                    ->relationship('student.countryOfResidence', 'name')
                                    ->default(fn () => filament()->auth()->user()->getDefaultCountry())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('student.state_of_residence', null);
                                    }),
                                Select::make('student.state_of_residence')
                                    ->label(__('resources/pages/create-student.form.state-of-residence.label'))
                                    ->disabled(fn (Get $get): bool => ! $get('student.country_of_residence'))
                                    ->relationship('student.stateOfResidence', 'name', fn (Builder $query, Get $get) => $query->where('country_iso2', $get('student.country_of_residence')))
                                    ->default(fn () => filament()->auth()->user()->getDefaultState())
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('student.city_of_residence')
                                    ->label(__('resources/pages/create-student.form.city-of-residence.label'))
                                    ->required()
                                    ->maxLength(255),
                            ]),
                            Section::make([
                                Select::make('student.status')
                                    ->label(__('Status'))
                                    ->options(StudentStatus::class)
                                    ->native(false)
                                    ->required(),
                                Select::make('student.classroom_id')
                                    ->label(__('Classroom'))
                                    ->relationship('student.classroom', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('student.guardian_id')
                                    ->label(__('Guardian'))
                                    ->relationship('student.guardian', 'id', function (Builder $query) {
                                        return $query
                                            ->whereHas('user', fn (Builder $q) => $q->where('role_id', Role::GUARDIAN))
                                            ->with('user');
                                    })
                                    ->getOptionLabelFromRecordUsing(fn (Guardian $record) => $record->user->name)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                            Section::make([
                                TextInput::make('student.admission_number')
                                    ->default(fn () => strtoupper(Random::generate()))
                                    ->disabled()
                                    ->dehydrated(fn (string $operation): bool => $operation === 'create')
                                    ->dehydrateStateUsing(fn (string $state): string => strtolower($state))
                                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                                    ->unique(Student::class, 'admission_number', modifyRuleUsing: fn (Unique $rule, ?User $record) => $rule->ignore($record?->id, 'user_id'))
                                    ->required(),
                                TextInput::make('nin')
                                    ->label(__('NIN'))
                                    ->hintIcon(__('resources/pages/create-record.hint-icon.name'))
                                    ->hintIconTooltip(__('National Identification Number (11 digits)'))
                                    ->length(11)
                                    ->numeric()
                                    ->unique(User::class, 'nin')
                                    ->extraInputAttributes([
                                        'inputmode' => 'numeric',
                                        'pattern' => '[0-9]*',
                                    ])
                                    ->validationAttribute('NIN')
                                    ->validationMessages(['unique' => 'This :attribute belongs to another.'])
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

                        ]),
                ], [
                    Section::make([
                        CreatedAt::make('Admitted'),
                        Timestamp::make('created_at', 'Graduated')
                            ->state(fn (?User $record) => $record?->student->graduated_at ? $record?->student->graduated_at->diffForHumans() : '-'),
                        DeletedAt::make('Expelled/Left'),
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
                            ->directory('students')
                            ->visibility('public')
                            ->storeFileNamesIn('original_avatar')
                            ->fetchFileInformation(true)
                            ->maxSize(1024)
                            ->maxFiles(1)
                            ->dehydrated(fn ($state) => $state != null),
                    ])
                        ->columnSpanFull(),
                ])
                    ->columnSpanFull(),
            ]);
    }
}
