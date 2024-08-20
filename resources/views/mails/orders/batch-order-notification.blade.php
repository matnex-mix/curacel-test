<x-mail::message>
# Hello, {{ $order->hmo->name }}

You have a new order with the following details:

<p><b>Provider:</b> {{ $order->provider_name }}</p>
<p><b>Batch:</b> {{ $order->batch->name }}</p>
<p><b>Encounter Date:</b> {{ $order->encounter_date->format('D m F, Y') }}</p>
<br/>

# Items
<x-mail::table>
|Item Name|Unit Price|Quantity|Sub Total|
|-------------|:-----------:|:------------:|:------------:|
{{ $order->items->map(fn ($it) => "|$it->name|". number_format($it->unit_price, 2) ."|$it->quantity|". number_format($it->sub_total, 2) ."|")->join("\n") }}
|||<b>Total</b>|{{ number_format($order->total, 2) }}|
</x-mail::table>

<p>From {{ config('app.name') }}</p>
</x-mail::message>
