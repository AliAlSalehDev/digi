<?php

namespace App\Imports;

use App\Models\Ingredient;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class IngredientImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $row) {
            if ($i == 0) {
                continue;
            }
            Ingredient::create([
                'name' => $row[0] ?? null,
                'unit' => $row[1] ?? null,
                'calories' => $row[2] ?? 0,
                'protein' => $row[3] ?? null,
                'carbs' => $row[4] ?? null,
                'fats_per_100g' => $row[5] ?? null,
                'price' => $row[6] ?? null,
            ]);
        }
    }
}
