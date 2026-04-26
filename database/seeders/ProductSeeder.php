<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();
        $clothing = Category::where('code', 'CLO')->firstOrFail();
        $grocery = Category::where('code', 'GRO')->firstOrFail();

        $size = Attribute::where('name', 'Size')->firstOrFail();
        $color = Attribute::where('name', 'Color')->firstOrFail();

        $sizeM = AttributeValue::where('attribute_id', $size->id)->where('value', 'M')->firstOrFail();
        $sizeL = AttributeValue::where('attribute_id', $size->id)->where('value', 'L')->firstOrFail();
        $colorRed = AttributeValue::where('attribute_id', $color->id)->where('value', 'Red')->firstOrFail();
        $colorBlue = AttributeValue::where('attribute_id', $color->id)->where('value', 'Blue')->firstOrFail();
        $colorBlack = AttributeValue::where('attribute_id', $color->id)->where('value', 'Black')->firstOrFail();

        $shirt = Product::updateOrCreate(
            ['name' => 'Cotton Shirt'],
            [
                'category_id' => $clothing->id,
                'name_ps' => 'کوټن پیراهن',
                'name_dr' => 'پیراهن نخی',
                'description' => 'High quality cotton shirt',
                'has_variants' => true,
                'unit' => 'piece',
                'is_active' => true,
                'expiry_tracking' => false,
                'created_by' => $admin->id,
            ]
        );

        ProductVariant::updateOrCreate(
            ['sku' => 'SHIRT-M-RED'],
            [
                'product_id' => $shirt->id,
                'barcode' => '100000000001',
                'attribute_value_1' => $sizeM->id,
                'attribute_value_2' => $colorRed->id,
                'price' => 500,
                'cost_price' => 300,
                'stock_quantity' => 15,
                'is_active' => true,
            ]
        );

        ProductVariant::updateOrCreate(
            ['sku' => 'SHIRT-M-BLUE'],
            [
                'product_id' => $shirt->id,
                'barcode' => '100000000002',
                'attribute_value_1' => $sizeM->id,
                'attribute_value_2' => $colorBlue->id,
                'price' => 500,
                'cost_price' => 300,
                'stock_quantity' => 10,
                'is_active' => true,
            ]
        );

        ProductVariant::updateOrCreate(
            ['sku' => 'SHIRT-L-BLACK'],
            [
                'product_id' => $shirt->id,
                'barcode' => '100000000003',
                'attribute_value_1' => $sizeL->id,
                'attribute_value_2' => $colorBlack->id,
                'price' => 550,
                'cost_price' => 320,
                'stock_quantity' => 8,
                'is_active' => true,
            ]
        );

        $rice = Product::updateOrCreate(
            ['name' => 'Basmati Rice'],
            [
                'category_id' => $grocery->id,
                'name_ps' => 'باسمتی وریجه',
                'name_dr' => 'برنج باسمتی',
                'description' => 'Premium quality basmati rice',
                'has_variants' => false,
                'unit' => '10kg bag',
                'is_active' => true,
                'expiry_tracking' => true,
                'created_by' => $admin->id,
            ]
        );

        ProductVariant::updateOrCreate(
            ['sku' => 'RICE-10KG'],
            [
                'product_id' => $rice->id,
                'barcode' => '200000000001',
                'price' => 2500,
                'cost_price' => 1800,
                'stock_quantity' => 20,
                'expiry_date' => '2026-12-31',
                'is_active' => true,
            ]
        );
    }
}
