<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => Setting::get('site_name', __('app.ensemble_calendar')),
            'imprint_company' => Setting::get('imprint_company'),
            'imprint_address' => Setting::get('imprint_address'),
            'imprint_contact' => Setting::get('imprint_contact'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('app.general_settings'))
                    ->schema([
                        TextInput::make('site_name')
                            ->label(__('app.site_name'))
                            ->helperText(__('app.site_name_helper'))
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make(__('app.imprint'))
                    ->schema([
                        TextInput::make('imprint_company')
                            ->label(__('app.imprint_company'))
                            ->maxLength(255),
                        Textarea::make('imprint_address')
                            ->label(__('app.imprint_address'))
                            ->rows(3),
                        Textarea::make('imprint_contact')
                            ->label(__('app.imprint_contact'))
                            ->rows(3),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->success()
            ->title(__('app.settings_saved'))
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return __('app.settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_system');
    }

    public function getTitle(): string
    {
        return __('app.settings');
    }
}
