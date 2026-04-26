<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $clothing = Category::updateOrCreate(
            ['code' => 'CLO'],
            ['name' => 'Clothing', 'name_ps' => 'کالي', 'name_dr' => 'لباس', 'sort_order' => 1]
        );

        $electronics = Category::updateOrCreate(
            ['code' => 'ELE'],
            ['name' => 'Electronics', 'name_ps' => 'الکترونيک', 'name_dr' => 'الکترونيک', 'sort_order' => 2]
        );

        $grocery = Category::updateOrCreate(
            ['code' => 'GRO'],
            ['name' => 'Grocery', 'name_ps' => 'خوراکبار', 'name_dr' => 'خواروبار', 'sort_order' => 3]
        );

        Category::updateOrCreate(
            ['code' => 'CLO-MEN'],
            ['parent_id' => $clothing->id, 'name' => 'Men', 'name_ps' => 'نارينه', 'name_dr' => 'مردانه', 'sort_order' => 1]
        );

        Category::updateOrCreate(
            ['code' => 'CLO-WOM'],
            ['parent_id' => $clothing->id, 'name' => 'Women', 'name_ps' => 'ښځينه', 'name_dr' => 'زنانه', 'sort_order' => 2]
        );

        Category::updateOrCreate(
            ['code' => 'CLO-CHI'],
            ['parent_id' => $clothing->id, 'name' => 'Children', 'name_ps' => 'ماشومان', 'name_dr' => 'بچگانه', 'sort_order' => 3]
        );

        Category::updateOrCreate(
            ['code' => 'ELE-MOB'],
            ['parent_id' => $electronics->id, 'name' => 'Mobile Phones', 'name_ps' => 'موبايل', 'name_dr' => 'موبايل', 'sort_order' => 1]
        );

        Category::updateOrCreate(
            ['code' => 'ELE-ACC'],
            ['parent_id' => $electronics->id, 'name' => 'Accessories', 'name_ps' => 'لوازم', 'name_dr' => 'لوازم', 'sort_order' => 2]
        );
    }
}
