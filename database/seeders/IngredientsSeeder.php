<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IngredientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        $ingredients = [
            ['id'=>1,'name'=>'RICE','unit'=>null,'calories'=>'130','protein'=>'2.7','carbs'=>'28','fats_per_100g'=>'0.3','price'=>'0.66'],
            ['id'=>2,'name'=>'YELLOW RICE','unit'=>null,'calories'=>'140.4','protein'=>'2.916','carbs'=>'30.24','fats_per_100g'=>'0.32','price'=>'0.957'],
            ['id'=>3,'name'=>'RED RICE','unit'=>null,'calories'=>'144.61','protein'=>'3.003','carbs'=>'31.14','fats_per_100g'=>'0.33','price'=>'0.957'],
            ['id'=>4,'name'=>'GREEN RICE','unit'=>null,'calories'=>'147.5','protein'=>'3.06','carbs'=>'31.77','fats_per_100g'=>'0.34','price'=>'0.957'],
            ['id'=>5,'name'=>'PASTA','unit'=>null,'calories'=>'370','protein'=>'13','carbs'=>'75','fats_per_100g'=>'1.5','price'=>'2.2'],
            ['id'=>6,'name'=>'POTATO','unit'=>null,'calories'=>'175.5','protein'=>'3.96','carbs'=>'27.69','fats_per_100g'=>'5.89','price'=>'0.352'],
            ['id'=>7,'name'=>'SPAGHETTI','unit'=>null,'calories'=>'370','protein'=>'13','carbs'=>'75','fats_per_100g'=>'1.5','price'=>'1.243'],
            ['id'=>8,'name'=>'SWEET POTATO','unit'=>null,'calories'=>'86','protein'=>'1.8','carbs'=>'20','fats_per_100g'=>'0.1','price'=>'0.825'],
            ['id'=>9,'name'=>'GRILLED POTATO','unit'=>null,'calories'=>'110','protein'=>'2.8','carbs'=>'29','fats_per_100g'=>'2.7','price'=>'0.99'],
            ['id'=>10,'name'=>'FISH','unit'=>null,'calories'=>'90','protein'=>'17','carbs'=>'0','fats_per_100g'=>'2','price'=>'1.045'],
            ['id'=>11,'name'=>'SALMON','unit'=>null,'calories'=>'206','protein'=>'20.4','carbs'=>'0','fats_per_100g'=>'13','price'=>'7.15'],
            ['id'=>12,'name'=>'SHRIMP','unit'=>null,'calories'=>'85','protein'=>'20.1','carbs'=>'0.2','fats_per_100g'=>'0.5','price'=>'3.245'],
            ['id'=>13,'name'=>'BEEF','unit'=>null,'calories'=>'230','protein'=>'26','carbs'=>'0','fats_per_100g'=>'15','price'=>'4.0689'],
            ['id'=>14,'name'=>'CHICKEN FILLET','unit'=>null,'calories'=>'165','protein'=>'31','carbs'=>'0','fats_per_100g'=>'3.6','price'=>'1.672'],
            ['id'=>15,'name'=>'EGG','unit'=>null,'calories'=>'148','protein'=>'13','carbs'=>'0.8','fats_per_100g'=>'10','price'=>'1.3288'],
            ['id'=>16,'name'=>'Avocado','unit'=>null,'calories'=>'174.06','protein'=>'2','carbs'=>'8.53','fats_per_100g'=>'14.66','price'=>'6'],
            ['id'=>17,'name'=>'Banana','unit'=>null,'calories'=>'98.69','protein'=>'1.09','carbs'=>'22.84','fats_per_100g'=>'0.33','price'=>'3'],
            ['id'=>18,'name'=>'Blackberry','unit'=>null,'calories'=>'48.41','protein'=>'1.39','carbs'=>'9.61','fats_per_100g'=>'0.49','price'=>'15'],
            ['id'=>19,'name'=>'Cantaloupe','unit'=>null,'calories'=>'37.71','protein'=>'0.84','carbs'=>'8.16','fats_per_100g'=>'0.19','price'=>'6'],
            ['id'=>20,'name'=>'Dragon Fruit','unit'=>null,'calories'=>'50.6','protein'=>'1.1','carbs'=>'11.1','fats_per_100g'=>'0.6','price'=>'23'],
            ['id'=>21,'name'=>'Grapes','unit'=>null,'calories'=>'69.9','protein'=>'0.72','carbs'=>'18.1','fats_per_100g'=>'0.16','price'=>'10'],
            ['id'=>22,'name'=>'Kiwi','unit'=>null,'calories'=>'67.88','protein'=>'1.14','carbs'=>'14.66','fats_per_100g'=>'0.52','price'=>'15'],
            ['id'=>23,'name'=>'Mango','unit'=>null,'calories'=>'66.62','protein'=>'0.82','carbs'=>'14.98','fats_per_100g'=>'0.38','price'=>'20'],
            ['id'=>24,'name'=>'Orange','unit'=>null,'calories'=>'69.9','protein'=>'1.3','carbs'=>'15.5','fats_per_100g'=>'0.3','price'=>'6'],
            ['id'=>25,'name'=>'Papaya','unit'=>null,'calories'=>'47.5','protein'=>'0.47','carbs'=>'10.82','fats_per_100g'=>'0.26','price'=>'6'],
            ['id'=>26,'name'=>'Passion Fruit','unit'=>null,'calories'=>'97.6','protein'=>'2.2','carbs'=>'23.4','fats_per_100g'=>'0.4','price'=>'20'],
            ['id'=>27,'name'=>'Pineapple','unit'=>null,'calories'=>'55.72','protein'=>'0.54','carbs'=>'13.12','fats_per_100g'=>'0.12','price'=>'8'],
            ['id'=>28,'name'=>'Pomegranate','unit'=>null,'calories'=>'92.01','protein'=>'1.67','carbs'=>'18.7','fats_per_100g'=>'1.17','price'=>'10'],
            ['id'=>29,'name'=>'Strawberry','unit'=>null,'calories'=>'36.1','protein'=>'0.67','carbs'=>'7.68','fats_per_100g'=>'0.3','price'=>'15'],
            ['id'=>30,'name'=>'Watermelon','unit'=>null,'calories'=>'33.99','protein'=>'0.61','carbs'=>'7.55','fats_per_100g'=>'0.15','price'=>'4'],
            ['id'=>31,'name'=>'QUINOA SALAD','unit'=>null,'calories'=>'400','protein'=>'18','carbs'=>'45','fats_per_100g'=>'20','price'=>'13.244'],
            ['id'=>32,'name'=>'FATUOUS SALAD','unit'=>null,'calories'=>'350','protein'=>'10','carbs'=>'40','fats_per_100g'=>'18','price'=>'11.748'],
            ['id'=>33,'name'=>'GREEK SALAD','unit'=>null,'calories'=>'380','protein'=>'15','carbs'=>'35','fats_per_100g'=>'22','price'=>'12.287'],
            ['id'=>34,'name'=>'ITALIAN SALAD','unit'=>null,'calories'=>'600','protein'=>'12','carbs'=>'50','fats_per_100g'=>'40','price'=>'15.631'],
            ['id'=>35,'name'=>'TUNA SALAD','unit'=>null,'calories'=>'600','protein'=>'50','carbs'=>'45','fats_per_100g'=>'30','price'=>'22.528'],
            ['id'=>36,'name'=>'CHICKEN SALAD','unit'=>null,'calories'=>'550','protein'=>'45','carbs'=>'40','fats_per_100g'=>'25','price'=>'15.433'],
        ];

        foreach ($ingredients as &$ingredient) {
            $ingredient['created_at'] = $now;
            $ingredient['updated_at'] = $now;
        }

        DB::table('ingredients')->insert($ingredients);
    }
}
