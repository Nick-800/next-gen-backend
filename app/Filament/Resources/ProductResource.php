<?php

namespace App\Filament\Resources;

use App\Actions\Products\GenerateVariantMatrixAction;
use App\Enums\ProductStatus;
use App\Enums\ProductType;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\VariantsRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';
    protected static string | \UnitEnum | null $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Product Details')->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Pricing & Classification')->schema([
                Forms\Components\Select::make('type')
                    ->options(collect(ProductType::cases())->mapWithKeys(
                        fn ($case) => [$case->value => $case->label()]
                    ))
                    ->required()
                    ->default(ProductType::Physical->value),

                Forms\Components\TextInput::make('base_price')
                    ->numeric()
                    ->prefix('$')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options(collect(ProductStatus::cases())->mapWithKeys(
                        fn ($case) => [$case->value => $case->label()]
                    ))
                    ->required()
                    ->default(ProductStatus::Draft->value),

                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured Product')
                    ->default(false),
            ])->columns(2),

            // ─── Variant Matrix Generator ─────────────────────────────────────
            Forms\Components\Section::make('Variant Matrix Generator')
                ->description('Define attribute groups to auto-generate all variant SKU combinations.')
                ->schema([
                    Forms\Components\Repeater::make('attribute_matrix')
                        ->label('Attribute Groups')
                        ->schema([
                            Forms\Components\TextInput::make('attribute')
                                ->label('Attribute Name')
                                ->placeholder('e.g. RAM, Color, Storage')
                                ->required(),
                            Forms\Components\TagsInput::make('values')
                                ->label('Values')
                                ->placeholder('e.g. 8GB, 16GB, 32GB')
                                ->required(),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add Attribute Group')
                        ->defaultItems(0),
                ])
                ->collapsible()
                ->collapsed()
                ->visibleOn('edit'), // Only show on edit (product must exist first)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->formatStateUsing(fn ($state) => $state instanceof ProductType ? $state->label() : $state),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn ($state) => $state instanceof ProductStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof ProductStatus ? $state->color() : 'gray'),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('variants_count')->counts('variants')->label('Variants'),
                Tables\Columns\TextColumn::make('base_price')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(
                    collect(ProductStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])
                ),
                Tables\Filters\SelectFilter::make('type')->options(
                    collect(ProductType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])
                ),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->headerActions([
                // Variant Matrix Generator — fires GenerateVariantMatrixAction
                Tables\Actions\Action::make('generate_variants')
                    ->label('Generate Variants')
                    ->icon('heroicon-o-squares-2x2')
                    ->form([
                        Forms\Components\Repeater::make('attribute_matrix')
                            ->label('Attribute Groups')
                            ->schema([
                                Forms\Components\TextInput::make('attribute')->required(),
                                Forms\Components\TagsInput::make('values')->required(),
                            ])
                            ->columns(2)
                            ->minItems(1),
                        Forms\Components\TextInput::make('base_price')
                            ->label('Base Price for Generated Variants')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        // headerActions fire on the list page; this action is per-record on the edit page
                    })
                    ->visible(false), // Used on edit page only via the form section
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getRelationManagers(): array
    {
        return [VariantsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
