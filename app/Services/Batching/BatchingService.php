<?php

namespace App\Services\Batching;

use App\Mail\BatchOrderNotification;
use App\Models\Hmo;
use App\Models\Order;
use App\Services\Batching\Strategies\DaySent;
use App\Services\Batching\Strategies\EncounterMonth;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class BatchingService
{
    use AsAction;

    private string $defaultStrategy = 'encounter_month';

    private array $strategies = [
        'encounter_month' => EncounterMonth::class,
        'day_sent' => DaySent::class,
    ];

    public function rules()
    {
        return [
            'provider_name' => 'required|string',
            'hmo_code' => 'required|string|exists:hmos,code',
            'encounter_date' => 'required|date',
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function handle(Order $order)
    {
        // we need the hmo to get the batching strategy and notification email
        $theHMO = $order->hmo;
        if( empty($theHMO) ){
            throw new Exception("BadOrder: No HMO is set for ORDER #$order->id");
        }

        if( $theHMO->batching_strategy && !empty($this->strategies[$theHMO->batching_strategy]) ){
            $strategy = $this->strategies[$theHMO->batching_strategy];
        } else {
            $strategy = $this->strategies[$this->defaultStrategy];
        }

        // get the correct batch using the right strategy
        $batch = app($strategy)
            ->getBatch($order);

        // update the batch of the order
        $order->update([
            'batch_id' => $batch->id
        ]);

        if( !empty($theHMO->notification_email) ) {
            // send notification to hmo
            Mail::to($theHMO->notification_email)
                ->send(new BatchOrderNotification($order));
        }
    }

    public function asController(ActionRequest $request)
    {
        try {
            $data = $request->validated();

            // Add the hmo_details
            $data['hmo_id'] = Hmo::where('code', $data['hmo_code'])
                ->value('id');

            // Calculate totals
            $total = 0;

            foreach ($data['items'] as &$item) {
                $item['sub_total'] = $item['quantity'] * $item['unit_price'];
                $total += $item['sub_total'];
            }

            $data['total'] = $total;

            // create order
            $order = Order::create($data);
            // populate the order items
            $order->items()->createMany($data['items']);

            // Tell the batching service to batch the order
            $this->handle($order);

            session()->flash('alert', [
                'type' => 'success',
                'message' => 'Order submitted successfully'
            ]);

            return redirect()->back();
        }
        catch (\Throwable $t){
            // Control measure
            Log::error($t);

            session()->flash('alert', [
                'type' => 'error',
                'message' => $t instanceof BatchingError ? $t->getMessage() : 'A server error occurred. Please try again after some time.'
            ]);

            return redirect()->back()->withErrors([
                'error' => 'error'
            ]);
        }
    }
}
