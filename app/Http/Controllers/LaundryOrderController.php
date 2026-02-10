<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\LaundryOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LaundryOrderController extends Controller
{
    public function create()
    {
        $customers = Customer::query()->orderBy('full_name')->get();

        return view('orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'drop_off_date' => ['required', 'date'],
            'expected_pickup_date' => ['nullable', 'date', 'after_or_equal:drop_off_date'],
            'special_instructions' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_name' => ['required', 'string', 'max:255'],
            'items.*.garment_description' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $orderNumber = 'LMS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));

        $order = LaundryOrder::create([
            'customer_id' => $validated['customer_id'],
            'order_number' => $orderNumber,
            'drop_off_date' => $validated['drop_off_date'],
            'expected_pickup_date' => $validated['expected_pickup_date'] ?? null,
            'status' => 'received',
            'special_instructions' => $validated['special_instructions'] ?? null,
        ]);

        $total = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $total += $lineTotal;

            LaundryOrderItem::create([
                'laundry_order_id' => $order->id,
                'service_name' => $item['service_name'],
                'garment_description' => $item['garment_description'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $lineTotal,
            ]);
        }

        $order->update(['total_amount' => $total]);

        LaundryOrderStatus::create([
            'laundry_order_id' => $order->id,
            'status' => 'received',
            'notes' => 'Order created.',
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->route('orders.show', $order)->with('status', 'Laundry order created.');
    }

    public function show(LaundryOrder $order)
    {
        $order->load(['customer', 'items', 'statuses']);

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, LaundryOrder $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:received,washing,ironing,ready,picked_up,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $order->update(['status' => $validated['status']]);

        LaundryOrderStatus::create([
            'laundry_order_id' => $order->id,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->route('orders.show', $order)->with('status', 'Order status updated.');
    }
}
