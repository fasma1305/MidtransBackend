<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ShopeepayController extends Controller
{
    public function shopeePay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'total' => 'required|int',
            'order_id' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'invalid', 'data' => $validator->errors()]);
        }

        $order = DB::table('orders')
            ->where('id', $request->order_id)
            ->first();
        if (!$order) {
            return response()->json(['message' => 'order not found', 'data' => [
                'order_id' => ['order not in database']
            ]], 422);
        }

        try {
            DB::beginTransaction();
            $serverKey = config('midtrans.key');

            $orderId = Str::uuid()->toString();
            $grossAmount = $order->price * $request->total;

            $response = Http::withBasicAuth($serverKey, '')
                ->post('https://api.sandbox.midtrans.com/v2/charge', [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => $grossAmount,
                    ],
                    'payment_type' => 'shopeepay',
                    'shopeepay' => [
                        'enable_callback' => true,                // optional
                        'callback_url' => 'someapps://callback'   // optional
                    ]
                ]);


            DB::table('transactions')->insert([
                'id' => $orderId,
                'order_code' => Str::random(6),
                'name' => $request->name,
                'email' => $request->email,
                'order_id' => $order->id,
                'total_order' => $request->total,
                'total_amount' => $grossAmount,
                'status' => 'Paid',
                'created_at' => now()
            ]);

            DB::table('orders')->where('id', $order->id)->update([
                'stock' => $order->stock - $request->total
            ]);

            DB::commit();
            return response()->json($response->json());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
