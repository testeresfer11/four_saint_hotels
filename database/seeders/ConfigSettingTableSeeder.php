<?php

namespace Database\Seeders;

use App\Models\ConfigSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        ConfigSetting::updateOrCreate([
            'type' => 'smtp',
            'key' => 'from_email'],
            ['value' => 'testsingh28@gmail.com'
        ]);
        ConfigSetting::updateOrCreate([
            'type' => 'smtp',
            'key' => 'host'],
            ['value' => 'smtp.gmail.com'
        ]);
        ConfigSetting::updateOrCreate([
            'type' => 'smtp',
            'key' => 'port'],
            ['value' => '587'
        ]);
        ConfigSetting::updateOrCreate([
            'type' => 'smtp',
            'key' => 'username'],
            ['value' => 'testsingh28@gmail.com'
        ]);
        ConfigSetting::updateOrCreate([
            'type' => 'smtp',
            'key' => 'from_name'],
            ['value' => 'Four Saints Hotel'
        ]);
        ConfigSetting::updateOrCreate([
            'type' => 'smtp',
            'key' => 'password'],
            ['value' => ''
        ]);
        ConfigSetting::updateOrCreate([
            'type' => 'smtp',
            'key' => 'encryption'],
            ['value' => 'tls'
        ]);
			
	
        // stripe
        ConfigSetting::updateOrCreate([
            'type' => 'stripe',
            'key' => 'STRIPE_KEY'],
            ['value' => ''
        ]); 
        ConfigSetting::updateOrCreate([
            'type' => 'stripe',
            'key' => 'STRIPE_SECRET'],
            ['value' => ''
        ]); 



        // Config Setting
        ConfigSetting::updateOrCreate(['type' => 'config','key' => 'CARD_LIMIT'],['value' => '10']);
        ConfigSetting::updateOrCreate(['type' => 'config','key' => 'QUESTION_LIMIT'],['value' => '10']);
        ConfigSetting::updateOrCreate(['type' => 'config','key' => 'PRICE_CATEGORIZED'],['value' => '4']);
        ConfigSetting::updateOrCreate(['type' => 'config','key' => 'PRICE_PERSONALIZED'],['value' => '7']);
        ConfigSetting::updateOrCreate(['type' => 'config','key' => 'BOARD_EXPIRY'],['value' => '7']);

    }
}
