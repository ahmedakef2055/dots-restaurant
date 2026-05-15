<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateUser;
use App\Http\Middleware\EnsureUserHasPermission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StationDisplayTest extends TestCase
{
    /** @var int[] */
    private array $createdProductIds = [];

    /** @var int[] */
    private array $createdOrderIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            AuthenticateUser::class,
            EnsureUserHasPermission::class,
        ]);
    }

    protected function tearDown(): void
    {
        $orderIds = array_values(array_unique($this->createdOrderIds));
        $productIds = array_values(array_unique($this->createdProductIds));

        if (! empty($orderIds)) {
            Order::query()->whereIn('id', $orderIds)->delete();
        }

        if (! empty($productIds)) {
            Product::query()->whereIn('id', $productIds)->delete();
        }

        parent::tearDown();
    }

    public function test_kitchen_data_shows_only_kitchen_items(): void
    {
        $kitchenProduct = $this->createProduct('Kitchen Sandwich', 30, 'kitchen');
        $barProduct = $this->createProduct('Bar Mojito', 20, 'bar');

        $order = $this->createPendingOrder();

        $this->createOrderItem($order, $kitchenProduct, 1, 1, 'pending', 'kitchen');
        $this->createOrderItem($order, $barProduct, 1, 1, 'pending', 'bar');

        $response = $this->getJson(route('kds.data'));

        $response->assertOk();

        $pendingTickets = $response->json('ordersByStage.pending');

        $this->assertCount(1, $pendingTickets);
        $this->assertCount(1, $pendingTickets[0]['items']);
        $this->assertSame('Kitchen Sandwich', $pendingTickets[0]['items'][0]['product_name']);
    }

    public function test_bar_data_shows_only_bar_items(): void
    {
        $kitchenProduct = $this->createProduct('Kitchen Pasta', 40, 'kitchen');
        $barProduct = $this->createProduct('Bar Latte', 25, 'bar');

        $order = $this->createPendingOrder();

        $this->createOrderItem($order, $kitchenProduct, 1, 1, 'pending', 'kitchen');
        $this->createOrderItem($order, $barProduct, 2, 1, 'pending', 'bar');

        $response = $this->getJson(route('bar.data'));

        $response->assertOk();

        $pendingTickets = $response->json('ordersByStage.pending');

        $this->assertCount(1, $pendingTickets);
        $this->assertCount(1, $pendingTickets[0]['items']);
        $this->assertSame('Bar Latte', $pendingTickets[0]['items'][0]['product_name']);
    }

    public function test_station_transition_updates_only_matching_station_items(): void
    {
        $kitchenProduct = $this->createProduct('Kitchen Burger', 55, 'kitchen');
        $barProduct = $this->createProduct('Bar Soda', 12, 'bar');

        $order = $this->createPendingOrder();

        $kitchenItem = $this->createOrderItem($order, $kitchenProduct, 1, 1, 'pending', 'kitchen');
        $barItem = $this->createOrderItem($order, $barProduct, 1, 1, 'pending', 'bar');

        $this->patchJson(route('kds.orders.transition', $order), [
            'action' => 'start',
            'kitchen_batch' => 1,
        ])->assertOk();

        $kitchenItem->refresh();
        $barItem->refresh();

        $this->assertSame('preparing', $kitchenItem->kitchen_status);
        $this->assertSame('pending', $barItem->kitchen_status);

        $this->patchJson(route('bar.orders.transition', $order), [
            'action' => 'start',
            'kitchen_batch' => 1,
        ])->assertOk();

        $barItem->refresh();

        $this->assertSame('preparing', $barItem->kitchen_status);
    }

    public function test_paid_orders_are_hidden_from_kitchen_and_bar_displays(): void
    {
        $kitchenProduct = $this->createProduct('Kitchen Paid Item', 18, 'kitchen');
        $barProduct = $this->createProduct('Bar Paid Item', 10, 'bar');

        $order = $this->createPendingOrder([
            'order_type' => 'dine_in',
            'status' => 'paid',
            'kitchen_status' => 'ready',
        ]);

        $this->createOrderItem($order, $kitchenProduct, 1, 1, 'ready', 'kitchen');
        $this->createOrderItem($order, $barProduct, 1, 1, 'ready', 'bar');

        $kitchenResponse = $this->getJson(route('kds.data'));
        $barResponse = $this->getJson(route('bar.data'));

        $kitchenResponse->assertOk();
        $barResponse->assertOk();

        $this->assertCount(0, $kitchenResponse->json('ordersByStage.pending'));
        $this->assertCount(0, $kitchenResponse->json('ordersByStage.preparing'));
        $this->assertCount(0, $kitchenResponse->json('ordersByStage.ready'));

        $this->assertCount(0, $barResponse->json('ordersByStage.pending'));
        $this->assertCount(0, $barResponse->json('ordersByStage.preparing'));
        $this->assertCount(0, $barResponse->json('ordersByStage.ready'));
    }

    public function test_paid_takeaway_orders_remain_visible_in_pending_stage(): void
    {
        $kitchenProduct = $this->createProduct('Kitchen Paid Takeaway Pending', 19, 'kitchen');

        $order = $this->createPendingOrder([
            'order_type' => 'takeaway',
            'status' => 'paid',
            'kitchen_status' => 'pending',
        ]);

        $this->createOrderItem($order, $kitchenProduct, 1, 1, 'pending', 'kitchen');

        $response = $this->getJson(route('kds.data'));

        $response->assertOk();
        $this->assertCount(1, $response->json('ordersByStage.pending'));
        $this->assertSame($order->order_number, $response->json('ordersByStage.pending.0.order_number'));
    }

    public function test_takeaway_ready_tickets_are_hidden_after_five_minutes_in_kitchen_display(): void
    {
        $kitchenProduct = $this->createProduct('Kitchen Hidden Ready Takeaway', 28, 'kitchen');

        $order = $this->createPendingOrder([
            'order_type' => 'takeaway',
            'kitchen_status' => 'ready',
        ]);

        $item = $this->createOrderItem($order, $kitchenProduct, 1, 1, 'ready', 'kitchen');

        DB::table('order_items')
            ->where('id', $item->id)
            ->update([
                'updated_at' => now()->subMinutes(6),
            ]);

        $response = $this->getJson(route('kds.data'));

        $response->assertOk();
        $this->assertCount(0, $response->json('ordersByStage.ready'));
    }

    public function test_delivery_ready_tickets_are_hidden_after_five_minutes_in_bar_display(): void
    {
        $barProduct = $this->createProduct('Bar Hidden Ready Delivery', 22, 'bar');

        $order = $this->createPendingOrder([
            'order_type' => 'delivery',
            'kitchen_status' => 'ready',
        ]);

        $item = $this->createOrderItem($order, $barProduct, 1, 1, 'ready', 'bar');

        DB::table('order_items')
            ->where('id', $item->id)
            ->update([
                'updated_at' => now()->subMinutes(6),
            ]);

        $response = $this->getJson(route('bar.data'));

        $response->assertOk();
        $this->assertCount(0, $response->json('ordersByStage.ready'));
    }

    public function test_dine_in_ready_tickets_remain_visible_after_five_minutes(): void
    {
        $kitchenProduct = $this->createProduct('Kitchen Ready Dine In', 33, 'kitchen');

        $order = $this->createPendingOrder([
            'order_type' => 'dine_in',
            'kitchen_status' => 'ready',
        ]);

        $item = $this->createOrderItem($order, $kitchenProduct, 1, 1, 'ready', 'kitchen');

        DB::table('order_items')
            ->where('id', $item->id)
            ->update([
                'updated_at' => now()->subMinutes(6),
            ]);

        $response = $this->getJson(route('kds.data'));

        $response->assertOk();
        $this->assertCount(1, $response->json('ordersByStage.ready'));
        $this->assertSame($order->order_number, $response->json('ordersByStage.ready.0.order_number'));
    }

    private function createProduct(string $name, float $price, string $station): Product
    {
        $product = Product::query()->create([
            'name' => $name,
            'sku' => 'SKU-' . Str::upper(Str::random(8)),
            'price' => $price,
            'stock' => 100,
            'is_active' => true,
            'preparation_station' => $station,
        ]);

        $this->createdProductIds[] = (int) $product->id;

        return $product;
    }

    private function createPendingOrder(array $overrides = []): Order
    {
        $order = Order::query()->create(array_merge([
            'order_number' => 'ORD-TEST-STATION-' . Str::upper(Str::random(6)),
            'order_type' => 'takeaway',
            'discount_type' => null,
            'discount_value' => 0,
            'subtotal' => 100,
            'discount_amount' => 0,
            'total' => 100,
            'status' => 'pending',
            'kitchen_status' => 'pending',
            'notes' => null,
        ], $overrides));

        $this->createdOrderIds[] = (int) $order->id;

        return $order;
    }

    private function createOrderItem(
        Order $order,
        Product $product,
        int $quantity,
        int $batch,
        string $kitchenStatus,
        string $station,
    ): OrderItem {
        $lineTotal = (float) $product->price * $quantity;

        return OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => $quantity,
            'line_total' => $lineTotal,
            'notes' => null,
            'kitchen_status' => $kitchenStatus,
            'kitchen_batch' => $batch,
            'preparation_station' => $station,
        ]);
    }
}
