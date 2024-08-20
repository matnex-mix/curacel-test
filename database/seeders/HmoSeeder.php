<?php

namespace Database\Seeders;

use App\Services\Batching\Strategies\DaySent;
use App\Services\Batching\Strategies\EncounterMonth;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HmoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hmos')->insert([
            ['name'=>'HMO A', 'code'=> 'HMO-A', 'notification_email' => 'hmoa@example.com', 'batching_strategy' => DaySent::$name],
            ['name'=>'HMO B', 'code'=> 'HMO-B', 'notification_email' => 'hmob@example.com', 'batching_strategy' => EncounterMonth::$name],
            ['name'=>'HMO C', 'code'=> 'HMO-C', 'notification_email' => 'hmoc@example.com', 'batching_strategy' => EncounterMonth::$name],
            ['name'=>'HMO D', 'code'=> 'HMO-D', 'notification_email' => 'hmod@example.com', 'batching_strategy' => null],
        ]);
    }
}
