<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Country;
use App\Models\School;
use App\Models\State;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class RegisterSchool extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register school';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('School details')
                    ->description('Set up your school profile and unique school code.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('School name')
                                    ->required()
                                    ->maxLength(255)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('code', Str::slug($state ?? ''))),
                                TextInput::make('code')
                                    ->label('School code')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(School::class, 'code'),
                            ]),
                    ]),
                Section::make('Contact information')
                    ->description('Optional contact details saved in the school settings.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('settings.contact.email')
                                    ->label('Contact email')
                                    ->email()
                                    ->maxLength(255),
                                PhoneInput::make('settings.contact.phone')
                                    ->label('Contact phone')
                                    ->prefixIcon('heroicon-s-phone')
                                    ->autoPlaceholder('aggressive')
                                    ->strictMode(),
                                TextInput::make('settings.contact.website')
                                    ->label('Website')
                                    ->url()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Section::make('Address')
                    ->description('Optional location details for your school.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('settings.address.line1')
                                    ->label('Street address')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                TextInput::make('settings.address.city')
                                    ->label('City')
                                    ->maxLength(255),
                                Select::make('settings.address.country_iso2')
                                    ->label('Country')
                                    ->options(fn (): array => Country::query()
                                        ->orderBy('name')
                                        ->pluck('name', 'iso2')
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('settings.address.state_code', null)),
                                Select::make('settings.address.state_code')
                                    ->label('State / Province')
                                    ->options(fn (Get $get): array => filled($countryIso2 = $get('settings.address.country_iso2'))
                                        ? State::query()
                                            ->where('country_iso2', $countryIso2)
                                            ->orderBy('name')
                                            ->pluck('name', 'state_code')
                                            ->all()
                                        : [])
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (Get $get): bool => blank($get('settings.address.country_iso2'))),
                            ]),
                    ]),
                Section::make('Academic defaults')
                    ->description('Optional defaults to help configure your school later.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('settings.academic.default_session')
                                    ->label('Default academic session')
                                    ->options(function (): array {
                                        $year = now()->year;

                                        return [
                                            "{$year}/".($year + 1) => "{$year}/".($year + 1),
                                            ($year + 1).'/'.($year + 2) => ($year + 1).'/'.($year + 2),
                                        ];
                                    })
                                    ->native(false),
                                Select::make('settings.academic.default_semester')
                                    ->label('Default semester')
                                    ->options([
                                        'First semester' => 'First semester',
                                        'Second semester' => 'Second semester',
                                        'Third semester' => 'Third semester',
                                    ])
                                    ->native(false),
                            ]),
                    ]),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $school = School::create([
            ...$data,
            'status' => 'active',
        ]);

        $school->users()->attach(Filament::auth()->id());

        return $school;
    }

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User && $user->isAdmin();
    }
}
