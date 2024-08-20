<?php

namespace Tests\Feature;

use App\Models\Hmo;
use Database\Seeders\HmoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchingOrderTest extends TestCase
{
    use RefreshDatabase;

    protected string $seeder = HmoSeeder::class;

    public array $orderData = [
        'provider_name' => 'Savannah Hospital',
        'encounter_date' => '2024-07-29 10:00:00',
        'items' => [
            [
                'name' => 'HPLC Test',
                'unit_price' => 23.56,
                'quantity' => 2,
            ],
            [
                'name' => 'HIV Test',
                'unit_price' => 15.55,
                'quantity' => 2,
            ]
        ]
    ];

    public function test_batch_order_returns_errors_without_data(): void
    {
        $response = $this->post(route('batch_order'), []);

        $response->assertSessionHasErrors([
            'provider_name',
            'hmo_code',
            'encounter_date',
            'items'
        ]);
    }

    public function test_batch_order_returns_errors_with_invalid_data()
    {
        $response = $this->post(route('batch_order'), [
            'name' => 'Invalid Order',
            'hmo_code' => 'invalid_code',
            'encounter_date' => 'invalid_date',
            'items' => [
                [
                    'name' => 'Invalid Item',
                    'unit_price' => -1,
                    'quantity' => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors([
            'hmo_code',
            'encounter_date',
            'items.*.unit_price',
            'items.*.quantity'
        ]);
    }

    public function test_batch_order_successful_with_encounter_date_batching_strategy()
    {
        $this->seed();

        $response = $this->post(route('batch_order'), [
            ...$this->orderData,
            'hmo_code' => 'HMO-B',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('batches', [
            'name' => $this->orderData['provider_name'] ." Jul 2024",
            'hmo_id' => $this->getHmoID('HMO-B'),
        ]);
    }

    public function test_batch_order_successful_with_set_day_batching_strategy()
    {
        $this->seed();

        $response = $this->post(route('batch_order'), [
            ...$this->orderData,
            'hmo_code' => 'HMO-A'
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('batches', [
            'name' => $this->orderData['provider_name'] ." Aug 2024",
            'hmo_id' => $this->getHmoID('HMO-A'),
        ]);
    }

    public function test_batch_order_successful_without_batching_strategy()
    {
        $this->seed();

        $response = $this->post(route('batch_order'), [
            ...$this->orderData,
            'hmo_code' => 'HMO-D'
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('batches', [
            'name' => $this->orderData['provider_name'] ." Jul 2024",
            'hmo_id' => $this->getHmoID('HMO-D'),
        ]);
    }

    public function getHmoID( $hmoCode ) : int|null
    {
        return Hmo::where('code', $hmoCode)->first()->id;
    }
}
