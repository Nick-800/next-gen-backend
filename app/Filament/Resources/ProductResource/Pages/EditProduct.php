<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Actions\Products\GenerateVariantMatrixAction;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    /**
     * After saving the product, check if the variant matrix was configured
     * and run GenerateVariantMatrixAction.
     */
    protected function afterSave(): void
    {
        $matrix = $this->data['attribute_matrix'] ?? [];

        if (empty($matrix)) {
            return;
        }

        $attributeMap = collect($matrix)
            ->mapWithKeys(fn ($row) => [$row['attribute'] => $row['values']])
            ->toArray();

        $count = app(GenerateVariantMatrixAction::class)->execute(
            product:   $this->record,
            attributeMatrix: $attributeMap,
            basePrice: (float) $this->record->base_price,
        );

        if ($count > 0) {
            Notification::make()
                ->title("Generated {$count} new variant(s) successfully.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('No new variants generated — all combinations already exist.')
                ->warning()
                ->send();
        }
    }
}
