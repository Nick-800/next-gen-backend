<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string | \UnitEnum | null $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Order Details')->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(
                        fn ($c) => [$c->value => $c->label()]
                    ))
                    ->required(),

                Forms\Components\Toggle::make('is_quote')->label('Bulk Quote'),

                Forms\Components\Textarea::make('notes'),
            ])->columns(2),

            Forms\Components\Section::make('Financials')->schema([
                Forms\Components\TextInput::make('subtotal')->numeric()->prefix('$')->readOnly(),
                Forms\Components\TextInput::make('discount_amount')->numeric()->prefix('$')->readOnly(),
                Forms\Components\TextInput::make('tax')->numeric()->prefix('$')->readOnly(),
                Forms\Components\TextInput::make('total')->numeric()->prefix('$')->readOnly(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Order #')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Customer')->placeholder('Guest'),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn ($state) => $state instanceof OrderStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof OrderStatus ? $state->color() : 'gray'),
                Tables\Columns\IconColumn::make('is_quote')->boolean()->label('Quote'),
                Tables\Columns\TextColumn::make('total')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(
                    collect(OrderStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])
                ),
                Tables\Filters\TernaryFilter::make('is_quote')->label('Quotes Only'),
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Approve selected draft quotes → Pending
                    Tables\Actions\BulkAction::make('approve_quotes')
                        ->label('Approve Quotes')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $service = app(OrderService::class);
                            foreach ($records as $order) {
                                if ($order->status === OrderStatus::Draft) {
                                    $service->approveQuote($order);
                                }
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
