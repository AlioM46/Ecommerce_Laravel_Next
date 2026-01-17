@php
  $order = $order->loadMissing(['user','address','products','payment']);

  $subtotal = 0;
  foreach ($order->products as $p) {
    $subtotal += ((float)$p->pivot->price) * ((int)$p->pivot->quantity);
  }
@endphp

<p>Hi {{ $order->user->name }},</p>

<p>Your order has been created successfully ✅</p>

<p><b>Order ID:</b> #{{ $order->id }}</p>
<p><b>Status:</b> {{ $order->status?->name ?? $order->status }}</p>
<p><b>Date:</b> {{ optional($order->created_at)->format('Y-m-d H:i') }}</p>

@if($order->address)
  <p style="margin-top:10px;">
    <b>Shipping Address:</b><br>
    {{ $order->address->city ?? '' }} {{ $order->address->street ?? '' }}<br>
    {{ $order->address->phone ?? '' }}
  </p>
@endif

<table style="width:100%; border-collapse: collapse; margin-top:12px;">
  <thead>
    <tr>
      <th style="text-align:left; border-bottom:1px solid #ddd; padding:8px;">Product</th>
      <th style="text-align:left; border-bottom:1px solid #ddd; padding:8px;">Variant</th>
      <th style="text-align:left; border-bottom:1px solid #ddd; padding:8px;">Qty</th>
      <th style="text-align:left; border-bottom:1px solid #ddd; padding:8px;">Price</th>
      <th style="text-align:left; border-bottom:1px solid #ddd; padding:8px;">Line Total</th>
    </tr>
  </thead>

  <tbody>
    @foreach($order->products as $product)
      @php
        $qty = (int) $product->pivot->quantity;
        $price = (float) $product->pivot->price;
        $lineTotal = $qty * $price;

        $variantParts = [];
        if (!empty($product->pivot->size)) $variantParts[] = "Size: {$product->pivot->size}";
        if (!empty($product->pivot->color)) $variantParts[] = "Color: {$product->pivot->color}";
        $variant = count($variantParts) ? implode(' | ', $variantParts) : '-';
      @endphp

      <tr>
        <td style="padding:8px; border-bottom:1px solid #eee;">
          {{ $product->name ?? 'Product' }}
        </td>

        <td style="padding:8px; border-bottom:1px solid #eee;">
          {{ $variant }}
        </td>

        <td style="padding:8px; border-bottom:1px solid #eee;">
          {{ $qty }}
        </td>

        <td style="padding:8px; border-bottom:1px solid #eee;">
          {{ number_format($price, 2) }}
        </td>

        <td style="padding:8px; border-bottom:1px solid #eee;">
          {{ number_format($lineTotal, 2) }}
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

<p style="margin-top:12px;">
  <b>Subtotal:</b> {{ number_format($subtotal, 2) }}
</p>

@if($order->payment)
  <p>
    <b>Payment:</b> {{ $order->payment->status ?? 'Pending' }}
  </p>
@endif

<p>If you didn’t place this order, please contact support.</p>
