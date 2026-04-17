<?php

namespace App\Http\Controllers;

use App\Models\CheckoutOrder;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Redirect ke Midtrans Snap untuk pembayaran
     */
    public function checkout($order_id)
    {
        $order = CheckoutOrder::where('public_order_id', $order_id)->firstOrFail();

        // Jika payment URL sudah ada, redirect langsung (biar tidak double create)
        if ($order->payment_redirect_url) {
            return redirect($order->payment_redirect_url);
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->public_order_id,
                    'gross_amount' => (int) $order->amount_idr,
                ],
                'customer_details' => [
                    // Handle cases where customer is null
                    'first_name' => explode(' ', $order->customer->name ?? 'Customer')[0],
                    'email' => $order->customer_email,
                ],
                'item_details' => [
                    [
                        'id' => $order->box_slug,
                        'price' => (int) $order->amount_idr,
                        'quantity' => 1,
                        'name' => mb_substr($order->box_title, 0, 50),
                    ]
                ],
                'callbacks' => [
                    'finish' => route('home'), // Langsung kembali ke home page sesuai request
                ],
            ];

            // Buat transaksi Midtrans
            $transaction = Snap::createTransaction($params);

            // Simpan snap token dan payment url di kolom yang sudah ada
            $order->update([
                'midtrans_transaction_id' => $transaction->token,
                'payment_redirect_url' => $transaction->redirect_url,
                'payment_status' => 'PENDING',
            ]);

            // Redirect ke halaman pembayaran Midtrans Snap
            return redirect($transaction->redirect_url);
        } catch (\Exception $e) {
            \Log::error('Midtrans checkout error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Callback Webhook dari Midtrans
     */
    public function webhook(Request $request)
    {
        try {
            $notification = new Notification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $order_id = $notification->order_id;
            $fraud = $notification->fraud_status;

            \Log::info('Midtrans webhook received', $request->all());

            $order = CheckoutOrder::where('public_order_id', $order_id)->first();
            if (!$order) {
                \Log::warning('Order not found for order_id: ' . $order_id);
                return response()->json(['error' => 'Order not found'], 404);
            }

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $order->update(['payment_status' => 'CHALLENGE']);
                    } else {
                        $order->update(['payment_status' => 'PAID']);
                    }
                }
            } else if ($transaction == 'settlement') {
                $order->update(['payment_status' => 'PAID']);
            } else if ($transaction == 'pending') {
                $order->update(['payment_status' => 'PENDING']);
            } else if ($transaction == 'deny') {
                $order->update(['payment_status' => 'DENIED']);
            } else if ($transaction == 'expire') {
                $order->update(['payment_status' => 'EXPIRED']);
            } else if ($transaction == 'cancel') {
                $order->update(['payment_status' => 'CANCELED']);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Webhook error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Midtrans Success page (redirect from Snap based on dashboard settings)
     */
    public function success($order_id)
    {
        $order = CheckoutOrder::where('public_order_id', $order_id)->firstOrFail();
        
        // Asumsi success callback
        if (!in_array($order->payment_status, ['PAID', 'PENDING'])) {
           $order->update(['payment_status' => 'PAID']); 
        }

        return view('payment.success', ['order' => $order]);
    }

    /**
     * Midtrans Failed/Error page (redirect from Snap)
     */
    public function failed($order_id)
    {
        $order = CheckoutOrder::where('public_order_id', $order_id)->firstOrFail();

        return view('payment.failed', ['order' => $order]);
    }

    /**
     * API API status polling if needed
     */
    public function checkStatus($order_id)
    {
        $order = CheckoutOrder::where('public_order_id', $order_id)->firstOrFail();

        return response()->json([
            'status' => $order->payment_status,
            'order' => $order,
        ]);
    }
}
