<?php

namespace App\Filament\Pages\Auth;

use App\Jobs\FetchLocation;
use App\Jobs\FetchPaystackCountries;
use App\Models\Country;
use App\Models\Role;
use App\Models\State;
use Filament\Actions\Action;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Register extends BaseRegister
{
    protected Width|string|null $maxContentWidth = Width::FourExtraLarge;

    public ?array $paystack_countries;

    public ?string $ipLocation;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->paystack_countries = $this->getPaystackSupportedCountries();

        $this->ipLocation = $this->getIpLocation();

        $this->callHook('beforeFill');

        $this->form->fill();

        $this->callHook('afterFill');
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Personal')
                        ->description('Provide details about yourself.')
                        ->icon(Heroicon::OutlinedUser)
                        ->completedIcon(Heroicon::User)
                        ->columns()
                        ->schema([
                            Section::make('Account')
                                ->schema([
                                    $this->getEmailFormComponent(),
                                    $this->getPasswordFormComponent(),
                                    $this->getPasswordConfirmationFormComponent(),
                                ]),
                            Section::make('Contact')
                                ->schema([
                                    $this->getNameFormComponent(),
                                    Select::make('setting.billable_country_code')
                                        ->label('Country')
                                        ->options(fn () => $this->paystack_countries)
                                        ->default(fn () => $this->ipLocation)
                                        ->searchable()
                                        ->required(),
                                    PhoneInput::make('phone')
                                        ->label('Phone number')
                                        ->prefixIcon('heroicon-s-phone')
                                        ->defaultCountry(fn () => $this->ipLocation)
                                        ->autoPlaceholder('aggressive')
                                        ->strictMode()
                                        ->required(),
                                ]),
                        ]),
                    Step::make('School')
                        ->description('Provide details about your school.')
                        ->icon(Heroicon::OutlinedBuildingLibrary)
                        ->completedIcon(Heroicon::BuildingLibrary)
                        ->columns()
                        ->schema([
                            Section::make('Profile')
                                ->schema([
                                    TextInput::make('setting.sch_name')
                                        ->label('Name')
                                        ->required(),
                                    TextInput::make('setting.sch_aliases')
                                        ->label('Aliases')
                                        ->hint('Separated by commas')
                                        ->dehydrateStateUsing(fn (mixed $state): array => array_map('trim', explode(',', $state))),
                                    TextInput::make('setting.sch_pmb')
                                        ->label('PMB / P.O Box')
                                        ->required(),
                                ]),
                            Section::make('Location')
                                ->schema([
                                    TextInput::make('setting.sch_address')
                                        ->label('Address')
                                        ->hint('Street, City')
                                        ->required(),
                                    Select::make('setting.sch_country')
                                        ->label('Country')
                                        ->options(fn () => static::getCountries())
                                        ->default(fn () => $this->ipLocation)
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->afterStateUpdated(function (Set $set) {
                                            $set('setting.sch_state', null);
                                        }),
                                    Select::make('setting.sch_state')
                                        ->label('Division')
                                        ->hint('State / Province')
                                        ->options(fn (Get $get) => filled($get('setting.sch_country')) ? static::getStates($get('setting.sch_country')) : [])
                                        ->searchable()
                                        ->preload()
                                        ->disabled(fn (Get $get): bool => ! $get('setting.sch_country'))
                                        ->required(),
                                ]),
                        ]),
                ])
                    ->startOnStep(1)
                    ->persistStepInQueryString()
                    ->previousAction(fn (Action $action) => $action->icon('heroicon-s-chevron-left'))
                    ->nextAction(fn (Action $action) => $action->icon('heroicon-s-chevron-right'))
                    ->submitAction($this->getRegisterFormAction()->icon('heroicon-s-rocket-launch')->iconPosition('after'))
                    ->skippable(),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration(Arr::except($data, 'setting'));

        $user->setting()->create($data['setting']);

        $user->role()->associate(Role::query()->find(Role::ADMIN))->save();

        return $user;
    }

    protected static function getCountries(): Collection
    {
        return Country::query()->pluck('name', 'iso2');
    }

    protected static function getStates($iso2): Collection
    {
        return State::query()->where('country_iso2', 'like', '%'.$iso2.'%')->pluck('name', 'state_code');
    }

    protected function getPaystackSupportedCountries(): array
    {
        $paystack_countries = Cache::get('paystack-countries');

        if ($paystack_countries) {
            return $paystack_countries;
        }

        FetchPaystackCountries::dispatch();

        return ['NG' => 'Nigeria'];
    }

    protected function getIpLocation(): string
    {
        $id = Session::id();
        $country = Cache::get("ip-location-{$id}");

        if ($country) {
            return $country;
        }

        FetchLocation::dispatch($id);

        return 'NG';
    }

    #[On('echo:updates,ApiFetched')]
    public function apiFetched($event)
    {
        $this->paystack_countries = $event['type'] === 'paystack_countries' ? $event['data'] : $this->paystack_countries;
        $this->ipLocation = $event['type'] === 'ip-location' ? $event['data'] : $this->ipLocation;

        if ($event['type'] === 'ip-location' && isset($this->paystack_countries[$this->ipLocation])) {
            return Notification::make('ip')
                ->icon('heroicon-s-map-pin')
                ->title(new Markdown('**Primiry&trade;**'))
                ->body(new Markdown('*You are interacting from **'.$this->paystack_countries[$this->ipLocation].'***'))
                ->persistent()
                ->success()
                ->send();
        } else {
            return Notification::make('ip')
                ->title('Our services are currently not offered at your current IP location.')
                ->warning()
                ->send();
        }
    }
}
