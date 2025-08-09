<?php

namespace App\Imports;

use App\Models\Meal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class MealImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        // dd($rows);
        foreach ($rows as $i => $row) {
            if ($i == 0) {
                continue;
            }
            Meal::create([
                'name' => $row[0] ?? null,
                'unit' => $row[1] ?? null,
                'calories' => $row[2] ?? 0,
                'protein' => $row[3] ?? null,
                'carbs' => $row[4] ?? null,
                'fats' => $row[5] ?? null,
                'kcal_with_avg_rice_sauce' => $row[6] ?? null,
                'protein_with_avg_rice_sauce' => $row[7] ?? null,
                'carbs_with_avg_rice_sauce' => $row[8] ?? null,
                'fat_with_avg_rice_sauce' => $row[9] ?? null,
                'price' => $row[10] ?? null,
            ]);
        }
    }
}
