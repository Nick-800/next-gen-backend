<?php

namespace App\Filament\Resources\ReviewResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Details')->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required(),
                    Select::make('variant_id')
                        ->relationship('variant', 'sku')
                        ->searchable()
                        ->required(),
                    Select::make('order_id')
                        ->relationship('order', 'id')
                        ->searchable()
                        ->nullable(),
                    Select::make('rating')
                        ->options([
                            1 => '1 Star',
                            2 => '2 Stars',
                            3 => '3 Stars',
                            4 => '4 Stars',
                            5 => '5 Stars',
                        ])
                        ->required(),
                    TextInput::make('title')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('body')
                        ->columnSpanFull(),
                ])->columns(2),
                
                Section::make('Status & Moderation')->schema([
                    Toggle::make('is_verified_purchase')
                        ->required(),
                    Toggle::make('is_approved')
                        ->label('Approved'),
                ])->columns(2),
            ]);
    }
}
