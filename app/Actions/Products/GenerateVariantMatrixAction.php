<?php

namespace App\Actions\Products;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateVariantMatrixAction
{
    /**
     * Generate all variant permutations from an attribute matrix and bulk-insert them.
     *
     * @param  Product  $product
     * @param  array<string, array<string>>  $attributeMatrix  e.g. ['RAM' => ['8GB','16GB'], 'Color' => ['Black','Silver']]
     * @param  float  $basePrice
     * @return int  Number of variants created
     */
    public function execute(Product $product, array $attributeMatrix, float $basePrice): int
    {
        $combinations = $this->cartesianProduct($attributeMatrix);
        $created = 0;

        DB::transaction(function () use ($product, $combinations, $basePrice, &$created) {
            foreach ($combinations as $combination) {
                $sku = $this->buildSku($product, $combination);

                // Skip if SKU already exists
                if (Variant::where('sku', $sku)->exists()) {
                    continue;
                }

                Variant::create([
                    'product_id'      => $product->id,
                    'sku'             => $sku,
                    'price'           => $basePrice,
                    'stock_quantity'  => 0,
                    'attributes_json' => $combination,
                    'is_default'      => false,
                    'is_active'       => true,
                ]);

                $created++;
            }
        });

        return $created;
    }

    /**
     * Compute the Cartesian product of attribute arrays.
     *
     * @param  array<string, array<string>>  $matrix
     * @return array<array<string, string>>
     */
    private function cartesianProduct(array $matrix): array
    {
        $result = [[]];

        foreach ($matrix as $attribute => $values) {
            $newResult = [];
            foreach ($result as $existing) {
                foreach ($values as $value) {
                    $newResult[] = array_merge($existing, [$attribute => $value]);
                }
            }
            $result = $newResult;
        }

        return $result;
    }

    private function buildSku(Product $product, array $combination): string
    {
        $suffix = collect($combination)
            ->map(fn ($v) => Str::upper(Str::substr($v, 0, 4)))
            ->implode('-');

        return Str::upper(Str::slug($product->name, '-')) . '-' . $suffix;
    }
}
