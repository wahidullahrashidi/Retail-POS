<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $size = Attribute::updateOrCreate(
            ['name' => 'Size'],
            ['name_ps' => 'سایز', 'name_dr' => 'سایز', 'data_type' => 'string']
        );

        $this->upsertAttributeValue($size->id, 'S', 'S', 'S', null, 1);
        $this->upsertAttributeValue($size->id, 'M', 'M', 'M', null, 2);
        $this->upsertAttributeValue($size->id, 'L', 'L', 'L', null, 3);
        $this->upsertAttributeValue($size->id, 'XL', 'XL', 'XL', null, 4);

        $color = Attribute::updateOrCreate(
            ['name' => 'Color'],
            ['name_ps' => 'رنګ', 'name_dr' => 'رنگ', 'data_type' => 'color']
        );

        $this->upsertAttributeValue($color->id, 'Red', 'سور', 'سرخ', '#FF0000', 1);
        $this->upsertAttributeValue($color->id, 'Blue', 'آبي', 'آبی', '#0000FF', 2);
        $this->upsertAttributeValue($color->id, 'Black', 'تور', 'سیاه', '#000000', 3);
        $this->upsertAttributeValue($color->id, 'White', 'سپین', 'سفید', '#FFFFFF', 4);

        $weight = Attribute::updateOrCreate(
            ['name' => 'Weight'],
            ['name_ps' => 'وزن', 'name_dr' => 'وزن', 'data_type' => 'number']
        );

        $this->upsertAttributeValue($weight->id, '1kg', '۱ کیلو', '۱ کیلو', null, 1);
        $this->upsertAttributeValue($weight->id, '5kg', '۵ کیلو', '۵ کیلو', null, 2);
        $this->upsertAttributeValue($weight->id, '10kg', '۱۰ کیلو', '۱۰ کیلو', null, 3);
    }

    private function upsertAttributeValue(
        int $attributeId,
        string $value,
        string $valuePs,
        string $valueDr,
        ?string $colorCode,
        int $sortOrder
    ): void {
        AttributeValue::updateOrCreate(
            [
                'attribute_id' => $attributeId,
                'value' => $value,
            ],
            [
                'value_ps' => $valuePs,
                'value_dr' => $valueDr,
                'color_code' => $colorCode,
                'sort_order' => $sortOrder,
            ]
        );
    }
}
