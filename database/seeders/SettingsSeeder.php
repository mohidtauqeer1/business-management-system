<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── General ──────────────────────────────────────────────────────
            ['key' => 'business_name',     'value' => 'My Business',           'type' => 'text',     'group' => 'general', 'label' => 'Business Name',      'description' => 'Shown on invoices, dashboard, and reports'],
            ['key' => 'business_tagline',  'value' => 'Quality You Can Trust',  'type' => 'text',     'group' => 'general', 'label' => 'Tagline / Slogan',    'description' => 'Subtitle shown under business name'],
            ['key' => 'business_email',    'value' => 'info@mybusiness.com',    'type' => 'email',    'group' => 'general', 'label' => 'Business Email',      'description' => null],
            ['key' => 'business_phone',    'value' => '03001234567',            'type' => 'text',     'group' => 'general', 'label' => 'Phone Number',        'description' => null],
            ['key' => 'business_address',  'value' => '123 Main Street, City', 'type' => 'textarea', 'group' => 'general', 'label' => 'Business Address',    'description' => 'Printed on invoices and receipts'],
            ['key' => 'currency_symbol',   'value' => 'Rs.',                    'type' => 'text',     'group' => 'general', 'label' => 'Currency Symbol',     'description' => 'e.g. Rs., $, €, £'],
            ['key' => 'currency_code',     'value' => 'PKR',                    'type' => 'text',     'group' => 'general', 'label' => 'Currency Code',       'description' => 'e.g. PKR, USD, EUR'],

            // ── Invoice ───────────────────────────────────────────────────────
            ['key' => 'invoice_prefix_sales',     'value' => 'INV',   'type' => 'text', 'group' => 'invoice', 'label' => 'Sales Invoice Prefix',    'description' => 'e.g. INV → INV-2026-00001'],
            ['key' => 'invoice_prefix_purchases', 'value' => 'PUR',   'type' => 'text', 'group' => 'invoice', 'label' => 'Purchase Invoice Prefix',  'description' => 'e.g. PUR → PUR-2026-00001'],
            ['key' => 'invoice_footer',           'value' => 'Thank you for your business! All disputes must be raised within 7 days.', 'type' => 'textarea', 'group' => 'invoice', 'label' => 'Invoice Footer Text', 'description' => 'Printed at the bottom of every invoice'],
            ['key' => 'invoice_show_logo',        'value' => '1',     'type' => 'boolean', 'group' => 'invoice', 'label' => 'Show Logo on Invoice', 'description' => null],

            // ── Finance ──────────────────────────────────────────────────────
            ['key' => 'default_tax_rate',        'value' => '0',      'type' => 'number', 'group' => 'finance', 'label' => 'Default Tax Rate (%)',        'description' => 'Pre-filled on new sales. 0 = no default tax'],
            ['key' => 'low_stock_threshold',     'value' => '10',     'type' => 'number', 'group' => 'finance', 'label' => 'Global Low Stock Threshold', 'description' => 'Used when a product has no specific reorder level set'],
            ['key' => 'customer_credit_limit',   'value' => '50000',  'type' => 'number', 'group' => 'finance', 'label' => 'Default Customer Credit Limit (Rs.)', 'description' => 'Sale blocked if customer outstanding exceeds this'],
            ['key' => 'enable_credit_limit',     'value' => '0',      'type' => 'boolean', 'group' => 'finance', 'label' => 'Enable Credit Limit Enforcement', 'description' => 'Block sales when customer exceeds credit limit'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
