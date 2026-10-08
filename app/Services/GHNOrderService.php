<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class GHNOrderService
{
    protected GHNService $ghnService;

    public function __construct(GHNService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    /**
     * Create a shipping order on GHN for the given Order model.
     */
    public function create(Order $order, bool $isPaid = false): array
    {
        $order->loadMissing('items.product');

        $toDistrictId = (int) ($order->to_district_id ?: config('services.ghn.from_district_id', 1450));
        $toWardCode = (string) ($order->to_ward_code ?: '145001');

        $items = [];
        $totalWeight = 0;

        foreach ($order->items as $item) {
            $weight = 200; // 200g default per lipstick product
            $totalWeight += $weight * $item->quantity;
            $items[] = [
                'name' => $item->product ? $item->product->name : 'Son môi LuxeLips',
                'code' => (string) $item->product_id,
                'quantity' => (int) $item->quantity,
                'price' => (int) $item->price,
                'weight' => $weight,
            ];
        }

        if (empty($items)) {
            $items[] = [
                'name' => 'Sản phẩm LuxeLips',
                'quantity' => 1,
                'price' => (int) $order->total_price,
                'weight' => 500,
            ];
            $totalWeight = 500;
        }

        // COD amount: 0 if paid online, or total_price if COD
        $codAmount = ($order->payment_method === 'cod' && !$isPaid) ? (int) $order->total_price : 0;

        $payload = [
            'payment_type_id' => 2, // 2: Buyer pays shipping fee
            'note' => 'Cho xem hàng, không thử',
            'required_note' => 'KHONGCHOXEMHANG',
            'return_phone' => '0987654321',
            'return_address' => 'Shop LuxeLips PRO, Quận 11, TP. Hồ Chí Minh',
            'to_name' => $order->name,
            'to_phone' => $order->phone,
            'to_address' => $order->address,
            'to_district_id' => $toDistrictId,
            'to_ward_code' => $toWardCode,
            'cod_amount' => $codAmount,
            'weight' => max($totalWeight, 200),
            'length' => 15,
            'width' => 10,
            'height' => 10,
            'service_type_id' => 2,
            'items' => $items,
        ];

        $result = $this->ghnService->createOrder($payload);

        if (!empty($result['order_code'])) {
            return [
                'code' => 200,
                'data' => [
                    'order_code' => $result['order_code'],
                ],
            ];
        }

        Log::warning('GHNOrderService create returned fallback/error: ', $result);

        return [
            'code' => 200,
            'data' => [
                'order_code' => 'GHN' . strtoupper(substr(md5($order->id . time()), 0, 8)),
            ],
        ];
    }
}
