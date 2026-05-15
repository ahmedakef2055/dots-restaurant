<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateUser;
use App\Http\Middleware\EnsureUserHasPermission;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RestaurantTable;
use Illuminate\Support\Str;
use Tests\TestCase;

class PosDineInTableOrderTest extends TestCase
{
    /** @var int[] */
    private array $createdTableIds = [];

    /** @var int[] */
    private array $createdProductIds = [];

    /** @var int[] */
    private array $createdOrderIds = [];

    /** @var int[] */
    private array $createdOfferIds = [];

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
        $tableIds = array_values(array_unique($this->createdTableIds));
        $productIds = array_values(array_unique($this->createdProductIds));
        $offerIds = array_values(array_unique($this->createdOfferIds));

        $tableOrderIds = [];

        if (! empty($tableIds)) {
            $tableOrderIds = Order::query()
                ->whereIn('restaurant_table_id', $tableIds)
                ->pluck('id')
                ->map(static fn($id): int => (int) $id)
                ->all();
        }

        $manualOrderIds = Order::query()
            ->where('order_number', 'like', 'ORD-TEST-%')
            ->pluck('id')
            ->map(static fn($id): int => (int) $id)
            ->all();

        $orderIds = array_values(array_unique(array_merge(
            $this->createdOrderIds,
            $tableOrderIds,
            $manualOrderIds,
        )));

        if (! empty($orderIds)) {
            OrderItem::query()->whereIn('order_id', $orderIds)->delete();
            Order::query()->whereIn('id', $orderIds)->delete();
        }

        if (! empty($tableIds)) {
            RestaurantTable::query()->whereIn('id', $tableIds)->delete();
        }

        if (! empty($productIds)) {
            Product::query()->whereIn('id', $productIds)->delete();
        }

        if (! empty($offerIds)) {
            Offer::query()->whereIn('id', $offerIds)->delete();
        }

        parent::tearDown();
    }

    public function test_it_does_not_apply_automatic_offer_discount_when_not_requested(): void
    {
        $product = $this->createProduct('No Auto Offer Product', 20.00);
        $table = $this->createTable('T-NO-AUTO-OFFER');

        $this->createOffer('Auto Five Pounds', [
            'discount_type' => 'fixed',
            'discount_value' => 5,
            'min_order_amount' => 0,
            'order_type' => 'dine_in',
        ]);

        $response = $this->postJson(route('pos.orders.store'), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $table->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('order.discount_amount', 0)
            ->assertJsonPath('order.total', 20)
            ->assertJsonPath('order.offer_name', null)
            ->assertJsonPath('order.coupon_code', null);

        $createdOrderId = (int) $response->json('order.id');
        $this->createdOrderIds[] = $createdOrderId;

        $this->assertDatabaseHas('orders', [
            'id' => $createdOrderId,
            'discount_amount' => 0,
            'offer_id' => null,
            'offer_name' => null,
        ]);
    }

    public function test_it_loads_existing_active_order_when_selecting_table(): void
    {
        $product = $this->createProduct('Loaded Product', 10.00);
        $table = $this->createTable('T-LOAD');
        $order = $this->createDineInOrder($table, [
            'subtotal' => 10.00,
            'total' => 10.00,
            'status' => 'pending',
        ]);

        $this->createOrderItem($order, $product, 1);

        $response = $this->getJson(route('pos.tables.order', $table));

        $response
            ->assertOk()
            ->assertJsonPath('order.id', $order->id)
            ->assertJsonPath('order.order_number', $order->order_number)
            ->assertJsonPath('order.items_count', 1)
            ->assertJsonPath('order.restaurant_table_id', $table->id);

        $this->assertDatabaseHas('restaurant_tables', [
            'id' => $table->id,
            'status' => 'occupied',
        ]);
    }

    public function test_it_marks_manually_occupied_table_as_occupied_in_pos_and_waiter_payloads(): void
    {
        $table = $this->createTable('T-RESERVED-PAYLOAD');
        $table->update([
            'status' => 'occupied',
        ]);

        $posResponse = $this->getJson(route('pos.tables.order', $table));

        $posResponse
            ->assertStatus(422)
            ->assertJsonPath('order', null);

        $posTablePayload = collect($posResponse->json('tables'))->firstWhere('id', $table->id);

        $this->assertIsArray($posTablePayload);
        $this->assertSame('occupied', $posTablePayload['status'] ?? null);

        $waiterResponse = $this->get(route('waiter.index'));

        $waiterResponse
            ->assertOk()
            ->assertViewHas('tables', function (array $tables) use ($table): bool {
                $waiterTablePayload = collect($tables)->firstWhere('id', $table->id);

                return is_array($waiterTablePayload)
                    && ($waiterTablePayload['status'] ?? null) === 'occupied';
            });
    }

    public function test_it_creates_a_new_pending_dine_in_order_for_an_available_table(): void
    {
        $product = $this->createProduct('New Order Product', 12.50);
        $table = $this->createTable('T-NEW');

        $response = $this->postJson(route('pos.orders.store'), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $table->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('updated', false)
            ->assertJsonPath('order.status', 'pending')
            ->assertJsonPath('order.restaurant_table_id', $table->id)
            ->assertJsonPath('order.items_count', 1);

        $createdOrderId = (int) $response->json('order.id');
        $this->createdOrderIds[] = $createdOrderId;

        $this->assertSame(
            1,
            Order::query()->where('restaurant_table_id', $table->id)->count(),
        );

        $this->assertDatabaseHas('restaurant_tables', [
            'id' => $table->id,
            'status' => 'occupied',
        ]);
    }

    public function test_it_rejects_creating_new_dine_in_order_for_manually_occupied_table(): void
    {
        $product = $this->createProduct('Reserved Table Product', 14.00);
        $table = $this->createTable('T-RESERVED-STORE');
        $table->update([
            'status' => 'occupied',
        ]);

        $response = $this->postJson(route('pos.orders.store'), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $table->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['restaurant_table_id']);

        $this->assertSame(
            0,
            Order::query()->where('restaurant_table_id', $table->id)->count(),
        );
    }

    public function test_it_appends_items_to_existing_active_order_and_does_not_create_another_order(): void
    {
        $existingProduct = $this->createProduct('Existing Product', 10.00);
        $newProduct = $this->createProduct('Append Product', 6.00);
        $table = $this->createTable('T-APPEND');

        $order = $this->createDineInOrder($table, [
            'subtotal' => 10.00,
            'total' => 10.00,
            'status' => 'pending',
        ]);

        $this->createOrderItem($order, $existingProduct, 1);

        $response = $this->postJson(route('pos.orders.store'), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $table->id,
            'items' => [
                [
                    'product_id' => $newProduct->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('updated', true)
            ->assertJsonPath('order.id', $order->id)
            ->assertJsonPath('order.items_count', 2);

        $this->assertSame(
            1,
            Order::query()->where('restaurant_table_id', $table->id)->count(),
        );

        $order->refresh();

        $this->assertSame(22.0, (float) $order->subtotal);
        $this->assertSame(22.0, (float) $order->total);
        $this->assertSame(2, $order->items()->count());

        $this->assertDatabaseHas('restaurant_tables', [
            'id' => $table->id,
            'status' => 'occupied',
        ]);
    }

    public function test_it_creates_new_pending_kitchen_batch_when_appending_items_to_same_table_order(): void
    {
        $existingProduct = $this->createProduct('Kitchen Existing Product', 8.00);
        $newProduct = $this->createProduct('Kitchen New Product', 5.00);
        $table = $this->createTable('T-KDS-BATCH');

        $order = $this->createDineInOrder($table, [
            'subtotal' => 8.00,
            'total' => 8.00,
            'status' => 'pending',
            'kitchen_status' => 'ready',
        ]);

        $existingItem = $this->createOrderItem($order, $existingProduct, 1, [
            'kitchen_status' => 'ready',
            'kitchen_batch' => 1,
        ]);

        $response = $this->postJson(route('pos.orders.store'), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $table->id,
            'items' => [
                [
                    'product_id' => $newProduct->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('updated', true)
            ->assertJsonPath('order.id', $order->id);

        $newItem = OrderItem::query()
            ->where('order_id', $order->id)
            ->where('product_id', $newProduct->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($newItem);
        $this->assertSame('pending', $newItem->kitchen_status);
        $this->assertSame(2, (int) $newItem->kitchen_batch);

        $existingItem->refresh();

        $this->assertSame('ready', $existingItem->kitchen_status);
        $this->assertSame(1, (int) $existingItem->kitchen_batch);
    }

    public function test_it_transfers_active_order_to_available_destination_table(): void
    {
        $sourceTable = $this->createTable('T-SOURCE');
        $destinationTable = $this->createTable('T-DEST');
        $order = $this->createDineInOrder($sourceTable, [
            'status' => 'pending',
        ]);

        $response = $this->postJson(route('pos.orders.transfer-table', $order), [
            'restaurant_table_id' => $destinationTable->id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('order.id', $order->id)
            ->assertJsonPath('order.restaurant_table_id', $destinationTable->id)
            ->assertJsonPath('order.restaurant_table_name', $destinationTable->name);

        $order->refresh();

        $this->assertSame($destinationTable->id, $order->restaurant_table_id);

        $this->assertDatabaseHas('restaurant_tables', [
            'id' => $sourceTable->id,
            'status' => 'available',
        ]);

        $this->assertDatabaseHas('restaurant_tables', [
            'id' => $destinationTable->id,
            'status' => 'occupied',
        ]);
    }

    public function test_it_rejects_transfer_when_destination_table_has_another_active_order(): void
    {
        $sourceTable = $this->createTable('T-SOURCE-BLOCK');
        $destinationTable = $this->createTable('T-DEST-BLOCK');

        $sourceOrder = $this->createDineInOrder($sourceTable, ['status' => 'pending']);
        $this->createDineInOrder($destinationTable, ['status' => 'pending']);

        $response = $this->postJson(route('pos.orders.transfer-table', $sourceOrder), [
            'restaurant_table_id' => $destinationTable->id,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['restaurant_table_id']);

        $sourceOrder->refresh();

        $this->assertSame($sourceTable->id, $sourceOrder->restaurant_table_id);
    }

    public function test_updating_order_status_to_paid_sets_table_available_when_no_active_orders_remain(): void
    {
        $table = $this->createTable('T-STATUS');
        $order = $this->createDineInOrder($table, ['status' => 'pending']);

        $this->patch(route('orders.status.update', $order), [
            'status' => 'paid',
        ])->assertStatus(302);

        $order->refresh();

        $this->assertSame('paid', $order->status);

        $this->assertDatabaseHas('restaurant_tables', [
            'id' => $table->id,
            'status' => 'available',
        ]);
    }

    public function test_it_enforces_single_active_order_per_table_with_database_guard(): void
    {
        $table = $this->createTable('T-UNIQUE-GUARD');

        $this->createDineInOrder($table, [
            'status' => 'pending',
            'active_table_guard' => $table->id,
        ]);

        $product = $this->createProduct('Guard Product', 7.50);

        $response = $this->postJson(route('pos.orders.store'), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $table->id,
            'active_order_id' => 999999,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['active_order_id']);

        $this->assertSame(
            1,
            Order::query()->where('restaurant_table_id', $table->id)->count(),
        );
    }

    public function test_it_updates_discount_for_pending_order_before_payment(): void
    {
        $table = $this->createTable('T-DISCOUNT-PENDING');
        $order = $this->createDineInOrder($table, [
            'subtotal' => 30,
            'discount_amount' => 0,
            'total' => 30,
            'status' => 'pending',
        ]);

        $this->patch(route('orders.discount.update', $order), [
            'discount_type' => 'fixed',
            'discount_value' => 5,
            'coupon_code' => '',
        ])->assertStatus(302);

        $order->refresh();

        $this->assertSame('fixed', $order->discount_type);
        $this->assertSame(5.0, (float) $order->discount_value);
        $this->assertSame(5.0, (float) $order->discount_amount);
        $this->assertSame(25.0, (float) $order->total);
        $this->assertNull($order->offer_id);
    }

    public function test_it_rejects_discount_update_when_order_is_not_pending(): void
    {
        $table = $this->createTable('T-DISCOUNT-PAID');
        $order = $this->createDineInOrder($table, [
            'subtotal' => 30,
            'discount_amount' => 0,
            'total' => 30,
            'status' => 'paid',
        ]);

        $this->patch(route('orders.discount.update', $order), [
            'discount_type' => 'fixed',
            'discount_value' => 5,
            'coupon_code' => '',
        ])
            ->assertStatus(302)
            ->assertSessionHasErrors(['discount_type']);

        $order->refresh();

        $this->assertSame('paid', $order->status);
        $this->assertSame(0.0, (float) $order->discount_amount);
        $this->assertSame(30.0, (float) $order->total);
    }

    private function createProduct(string $name, float $price): Product
    {
        $product = Product::query()->create([
            'name' => $name,
            'sku' => 'SKU-' . Str::upper(Str::random(8)),
            'price' => $price,
            'stock' => 100,
            'is_active' => true,
        ]);

        $this->createdProductIds[] = (int) $product->id;

        return $product;
    }

    private function createTable(string $name): RestaurantTable
    {
        $table = RestaurantTable::query()->create([
            'name' => $name,
            'capacity' => 4,
            'status' => 'available',
        ]);

        $this->createdTableIds[] = (int) $table->id;

        return $table;
    }

    private function createDineInOrder(RestaurantTable $table, array $overrides = []): Order
    {
        $order = Order::query()->create(array_merge([
            'order_number' => 'ORD-TEST-' . Str::upper(Str::random(6)),
            'order_type' => 'dine_in',
            'restaurant_table_id' => $table->id,
            'discount_type' => null,
            'discount_value' => 0,
            'subtotal' => 20,
            'discount_amount' => 0,
            'total' => 20,
            'status' => 'pending',
            'notes' => null,
        ], $overrides));

        $this->createdOrderIds[] = (int) $order->id;

        return $order;
    }

    private function createOffer(string $name, array $overrides = []): Offer
    {
        $offer = Offer::query()->create(array_merge([
            'name' => $name,
            'discount_type' => 'fixed',
            'discount_value' => 5,
            'min_order_amount' => 0,
            'max_discount_amount' => null,
            'order_type' => null,
            'starts_at' => null,
            'ends_at' => null,
            'priority' => 0,
            'stackable_with_coupon' => false,
            'is_active' => true,
            'notes' => null,
        ], $overrides));

        $this->createdOfferIds[] = (int) $offer->id;

        return $offer;
    }

    private function createOrderItem(Order $order, Product $product, int $quantity, array $overrides = []): OrderItem
    {
        $unitPrice = (float) $product->price;
        $lineTotal = $unitPrice * $quantity;

        return OrderItem::query()->create(array_merge([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'line_total' => $lineTotal,
            'kitchen_status' => 'pending',
            'kitchen_batch' => 1,
        ], $overrides));
    }
}
