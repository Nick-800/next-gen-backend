<?php

namespace App\Filament\Resources;

use App\Enums\CouponType;
use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-ticket';
    protected static string | \UnitEnum | null $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('code')
                ->required()
                ->unique(ignoreRecord: true)
                ->alphaNum()
                ->maxLength(50),

            Forms\Components\Select::make('type')
                ->options(collect(CouponType::cases())->mapWithKeys(
                    fn ($c) => [$c->value => $c->label()]
                ))
                ->required()
                ->live(),

            Forms\Components\TextInput::make('value')
                ->numeric()
                ->required()
                ->suffix(fn ($get) => $get('type') === CouponType::Percentage->value ? '%' : '$'),

            Forms\Components\TextInput::make('minimum_order_amount')
                ->numeric()
                ->prefix('$')
                ->default(0),

            Forms\Components\TextInput::make('usage_limit')
                ->numeric()
                ->nullable()
                ->helperText('Leave blank for unlimited usage.'),

            Forms\Components\DateTimePicker::make('expires_at')
                ->nullable(),

            Forms\Components\Toggle::make('is_active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->copyable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->formatStateUsing(fn ($state) => $state instanceof CouponType ? $state->label() : $state),
                Tables\Columns\TextColumn::make('value'),
                Tables\Columns\TextColumn::make('times_used')->label('Used'),
                Tables\Columns\TextColumn::make('usage_limit')->label('Limit')->placeholder('∞'),
                Tables\Columns\TextColumn::make('expires_at')->dateTime()->placeholder('Never'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit'   => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
