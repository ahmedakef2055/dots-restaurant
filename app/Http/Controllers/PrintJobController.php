<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Printer;
use App\Models\PrintJob;
use App\Services\PrintService;
use Illuminate\Http\JsonResponse;

class PrintJobController extends Controller
{
    public function next(PrintService $printService): JsonResponse
    {
        $job = PrintJob::where('status', 'pending')
            ->whereIn('printer_type', ['cashier', 'bar', 'shift_close'])
            ->whereIn('payload_type', ['json', 'base64', 'html'])
            ->oldest()
            ->first();

        if (! $job) {
            return response()->json(['job' => null]);
        }

        $job->update(['status' => 'printing']);

        try {
            // pre-built HTML or ESC/POS base64 — return directly
            if (in_array($job->payload_type, ['base64', 'html'])) {
                return response()->json([
                    'job' => [
                        'id'          => $job->id,
                        'data'        => $job->payload,
                        'render_type' => $job->payload_type, // 'html' or 'base64'(raw)
                        'printer_name' => Printer::windowsNameFor($job->printer_type),
                    ],
                ]);
            }

            $payload  = json_decode($job->payload, true);
            $orderSerial = $payload['order_serial'] ?? null;

            $order = Order::with([
                'items', 'user:id,name',
                'restaurantTable:id,name',
                'customer:id,first_name',
            ])->where('order_serial', $orderSerial)->first();

            if (! $order) {
                $job->update(['status' => 'failed', 'error' => 'Order not found']);
                return response()->json(['job' => null]);
            }

            if ($job->printer_type === 'bar') {
                $label       = $payload['label'] ?? 'NEW ORDER';
                $kitchenBatch = $payload['kitchen_batch'] ?? null;
                $printItems  = $kitchenBatch
                    ? $order->items->filter(fn ($i) => (int) $i->kitchen_batch === (int) $kitchenBatch)->values()->all()
                    : $order->items->all();

                $base64 = $printService->buildPreparationTicketBase64($order, $label, $printItems);
            } else {
                $base64 = $printService->buildOrderReceiptBase64($order);
            }

            return response()->json([
                'job' => [
                    'id'           => $job->id,
                    'data'         => $base64,
                    'printer_name' => Printer::windowsNameFor($job->printer_type),
                ],
            ]);

        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return response()->json(['job' => null]);
        }
    }

    public function status(PrintJob $printJob): JsonResponse
    {
        return response()->json(['status' => $printJob->status]);
    }

    public function done(PrintJob $printJob): JsonResponse
    {
        $printJob->update(['status' => 'done']);
        return response()->json(['ok' => true]);
    }

    public function failed(PrintJob $printJob): JsonResponse
    {
        $printJob->update(['status' => 'failed']);
        return response()->json(['ok' => true]);
    }
}
