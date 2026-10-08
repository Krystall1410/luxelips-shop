<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoService
{
    public function createPayment(Order $order, PaymentTransaction $transaction): array
    {
        $endpoint = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $partnerCode = config('services.momo.partner_code', 'MOMOBKUN20180529');
        $accessKey = config('services.momo.access_key', 'klm05TvNBzhg7h7j');
        $secretKey = config('services.momo.secret_key', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
        
        $orderInfo = 'Thanh toan don hang #' . $order->id;
        $amount = (string) ((int) $order->total_price);
        $orderId = $order->id . '_' . $transaction->id . '_' . time();
        $redirectUrl = config('services.momo.redirect_url') ?: route('user.payment.momo.callback');
        $ipnUrl = config('services.momo.ipn_url') ?: route('payment.momo.ipn');
        $extraData = (string) $order->id;
        $requestId = (string) time();
        $requestType = 'payWithCC';

        $rawHash = 'accessKey=' . $accessKey .
            '&amount=' . $amount .
            '&extraData=' . $extraData .
            '&ipnUrl=' . $ipnUrl .
            '&orderId=' . $orderId .
            '&orderInfo=' . $orderInfo .
            '&partnerCode=' . $partnerCode .
            '&redirectUrl=' . $redirectUrl .
            '&requestId=' . $requestId .
            '&requestType=' . $requestType;

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'LuxeLips Cosmetics',
            'storeId' => 'LuxeLipsStore',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        $transaction->update([
            'gateway_order_id' => $orderId,
            'request_payload' => $data,
        ]);

        try {
            $verifySsl = filter_var(config('services.momo.verify_ssl', false), FILTER_VALIDATE_BOOLEAN);
            $response = Http::timeout(10)
                ->withOptions(['verify' => $verifySsl])
                ->post($endpoint, $data);

            $result = $response->json() ?? [];

            $transaction->update([
                'response_payload' => $result,
                'result_code' => isset($result['resultCode']) ? (int) $result['resultCode'] : null,
                'message' => $result['message'] ?? null,
                'status' => isset($result['payUrl']) ? 'initiated' : 'failed',
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('MoMo createPayment Exception: ' . $e->getMessage());

            $transaction->update([
                'message' => 'Lỗi kết nối MoMo: ' . $e->getMessage(),
                'status' => 'failed',
            ]);

            return [
                'resultCode' => 99,
                'message' => 'Không thể kết nối máy chủ MoMo.',
            ];
        }
    }

    public function isSuccessful(array $payload): bool
    {
        return (string) ($payload['resultCode'] ?? '') === '0';
    }

    public function markPaid(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id' => $payload['transId'] ?? null,
            'result_code' => (int) ($payload['resultCode'] ?? 0),
            'message' => $payload['message'] ?? null,
            'response_payload' => $payload,
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);
    }

    public function markFailed(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id' => $payload['transId'] ?? null,
            'result_code' => isset($payload['resultCode']) ? (int) $payload['resultCode'] : null,
            'message' => $payload['message'] ?? null,
            'response_payload' => $payload,
            'status' => 'failed',
        ]);
    }

    public function isValidSuccessfulResponse(array $payload): bool
    {
        return $this->isValidResponse($payload) && $this->isSuccessful($payload);
    }

    public function isValidResponse(array $payload): bool
    {
        if (!isset($payload['signature'])) {
            return false;
        }

        $accessKey = config('services.momo.access_key', 'klm05TvNBzhg7h7j');
        $secretKey = config('services.momo.secret_key', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');

        $rawHash = 'accessKey=' . $accessKey .
            '&amount=' . ($payload['amount'] ?? '') .
            '&extraData=' . ($payload['extraData'] ?? '') .
            '&message=' . ($payload['message'] ?? '') .
            '&orderId=' . ($payload['orderId'] ?? '') .
            '&orderInfo=' . ($payload['orderInfo'] ?? '') .
            '&orderType=' . ($payload['orderType'] ?? '') .
            '&partnerCode=' . ($payload['partnerCode'] ?? '') .
            '&payType=' . ($payload['payType'] ?? '') .
            '&requestId=' . ($payload['requestId'] ?? '') .
            '&responseTime=' . ($payload['responseTime'] ?? '') .
            '&resultCode=' . ($payload['resultCode'] ?? '') .
            '&transId=' . ($payload['transId'] ?? '');

        return hash_equals(
            hash_hmac('sha256', $rawHash, $secretKey),
            (string) $payload['signature']
        );
    }

    public function orderId(array $payload): ?int
    {
        $orderId = $payload['extraData'] ?? null;
        return is_numeric($orderId) ? (int) $orderId : null;
    }
}
