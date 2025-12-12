<?php

namespace App\Filament\Resources\Rehearsals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RehearsalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make(__('app.rehearsal_details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('app.title'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('location_name')
                            ->label(__('app.location'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('location_address')
                            ->label(__('app.address'))
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Hidden::make('timezone')
                            ->default(config('app.timezone')),
                        Toggle::make('is_published')
                            ->label(__('app.published'))
                            ->default(true)
                            ->helperText(__('app.published_hint')),
                    ]),
                Section::make(__('app.schedule'))
                    ->schema([
                        Repeater::make('days')
                            ->relationship('days')
                            ->label(__('app.rehearsal_days'))
                            ->minItems(1)
                            ->columns(4)
                            ->addActionLabel(__('app.add_rehearsal_day'))
                            ->schema([
                                DatePicker::make('rehearsal_date')
                                    ->label(__('app.date'))
                                    ->required()
                                    ->native(false),
                                TimePicker::make('starts_at')
                                    ->label(__('app.start_time'))
                                    ->required()
                                    ->seconds(false),
                                TimePicker::make('ends_at')
                                    ->label(__('app.end_time'))
                                    ->required()
                                    ->seconds(false)
                                    ->rule('after:starts_at'),
                                Textarea::make('notes')
                                    ->label(__('app.notes'))
                                    ->rows(2)
                                    ->columnSpanFull()
                                    ->helperText(__('app.day_notes_hint')),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('app.materials'))
                    ->schema([
                        FileUpload::make('plan_path')
                            ->label(__('app.rehearsal_plan_pdf'))
                            ->disk('private')
                            ->directory('rehearsal-plans')
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->helperText(__('app.plan_hint')),
                        Textarea::make('notes')
                            ->label(__('app.notes'))
                            ->rows(4)
                            ->placeholder(__('app.notes_placeholder'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

}
