<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            // General
            ['key' => 'store_name',       'value' => 'Afghan POS',          'type' => 'string',  'group' => 'general'],
            ['key' => 'store_address',    'value' => 'Kabul, Afghanistan',   'type' => 'string',  'group' => 'general'],
            ['key' => 'store_phone',      'value' => '',                     'type' => 'string',  'group' => 'general'],
            ['key' => 'store_email',      'value' => '',                     'type' => 'string',  'group' => 'general'],
            ['key' => 'currency',         'value' => 'AFN',                  'type' => 'string',  'group' => 'general'],
            ['key' => 'currency_symbol',  'value' => 'Af',                   'type' => 'string',  'group' => 'general'],
            ['key' => 'timezone',         'value' => 'Asia/Kabul',           'type' => 'string',  'group' => 'general'],
            // Calendar
            ['key' => 'default_calendar', 'value' => 'hijri',                'type' => 'string',  'group' => 'calendar'],
            ['key' => 'default_language', 'value' => 'en',                   'type' => 'string',  'group' => 'calendar'],
            ['key' => 'date_format',      'value' => 'd M Y',                'type' => 'string',  'group' => 'calendar'],
            // Security
            ['key' => 'auto_logout',      'value' => '30',                   'type' => 'integer', 'group' => 'security'],
            ['key' => 'require_pin',      'value' => '1',                    'type' => 'boolean', 'group' => 'security'],
            ['key' => 'audit_log',        'value' => '1',                    'type' => 'boolean', 'group' => 'security'],
            // Hardware
            ['key' => 'printer_type',     'value' => 'thermal',              'type' => 'string',  'group' => 'hardware'],
            ['key' => 'printer_port',     'value' => 'USB001',               'type' => 'string',  'group' => 'hardware'],
            ['key' => 'drawer_enabled',   'value' => '1',                    'type' => 'boolean', 'group' => 'hardware'],
            ['key' => 'scanner_enabled',  'value' => '1',                    'type' => 'boolean', 'group' => 'hardware'],
            ['key' => 'receipt_footer',   'value' => 'شکریه — Thank you',    'type' => 'string',  'group' => 'hardware'],
        ];

        foreach ($defaults as $s) {
            \App\Models\Setting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}
