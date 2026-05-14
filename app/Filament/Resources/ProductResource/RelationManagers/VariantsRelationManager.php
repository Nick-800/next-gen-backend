<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('sku')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Forms\Components\TextInput::make('price')
                ->numeric()
                ->prefix('$')
                ->required(),

            Forms\Components\TextInput::make('stock_quantity')
                ->numeric()
                ->required()
                ->default(0),

            Forms\Components\TextInput::make('low_stock_threshold')
                ->numeric()
                ->required()
                ->default(5)
                ->helperText('"Only X left!" badge triggers below this threshold.'),

            Forms\Components\KeyValue::make('attributes_json')
                ->label('Technical Specifications')
                ->keyLabel('Attribute (e.g. RAM)')
                ->valueLabel('Value (e.g. 16GB)')
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('images_json')
                ->label('Variant Images')
                ->multiple()
                ->image()
                ->directory('variants')
                ->columnSpanFull(),

            Forms\Components\Toggle::make('is_default')
                ->label('Default Variant'),

            Forms\Components\Toggle::make('is_active')
                ->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->searchable(),
                Tables\Columns\TextColumn::make('price')->money('USD'),
                Tables\Columns\TextColumn::make('stock_quantity')->label('Stock')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->isOutOfStock() => 'danger',
                        $record->isLowStock()   => 'warning',
                        default                 => 'success',
                    }),
                Tables\Columns\IconColumn::make('is_default')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
