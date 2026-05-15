<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalaryAdjustment;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeSalaryAdjustmentController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {}

    public function storeDeduction(Request $request, Employee $employee): RedirectResponse
    {
        if (! Schema::hasTable('employee_salary_adjustments')) {
            return back()->with('error', __('messages.errors.employee_salary_adjustments_migration_required'));
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'adjustment_date' => ['required', 'date'],
            'note' => ['required', 'string', 'max:1000'],
        ]);

        $employee->salaryAdjustments()->create([
            'type' => 'manual_deduction',
            'quantity' => 1,
            'unit_price' => round((float) $validated['amount'], 2),
            'amount' => round((float) $validated['amount'], 2),
            'adjustment_date' => $validated['adjustment_date'],
            'note' => $validated['note'],
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('messages.success.employee_deduction_added'));
    }

    public function storeProductCharge(Request $request, Employee $employee): RedirectResponse
    {
        if (! Schema::hasTable('employee_salary_adjustments')) {
            return back()->with('error', __('messages.errors.employee_salary_adjustments_migration_required'));
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:1', 'max:9999'],
            'adjustment_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::query()
            ->where('is_active', true)
            ->findOrFail($validated['product_id']);

        $quantity = round((float) $validated['quantity'], 2);
        $unitPrice = round((float) $product->price, 2);
        $amount = round($quantity * $unitPrice, 2);

        $note = trim((string) ($validated['note'] ?? ''));
        if ($note === '') {
            $note = sprintf('Loaded product: %s', $product->name);
        }

        DB::transaction(function () use ($employee, $product, $quantity, $unitPrice, $amount, $validated, $note): void {
            $adjustment = $employee->salaryAdjustments()->create([
                'type' => 'product_charge',
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'amount' => $amount,
                'adjustment_date' => $validated['adjustment_date'],
                'note' => $note,
                'created_by' => Auth::id(),
            ]);

            $this->inventoryService->deductInventoryForProductItems(
                items: [[
                    'product_id' => (int) $product->id,
                    'quantity' => $quantity,
                ]],
                note: __('messages.notes.employee_product_charge_consumption', [
                    'employee' => $employee->full_name,
                    'product' => $product->name,
                ]),
                referenceType: EmployeeSalaryAdjustment::class,
                referenceId: (int) $adjustment->id,
                strict: true,
            );
        });

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('messages.success.employee_product_charge_added'));
    }

    public function update(Request $request, Employee $employee, EmployeeSalaryAdjustment $adjustment): RedirectResponse
    {
        $this->ensureAdjustmentsFeatureEnabled();
        $this->ensureAdjustmentBelongsToEmployee($employee, $adjustment);

        if ($adjustment->type === 'manual_deduction') {
            $validated = $request->validateWithBag('adjustmentUpdate', [
                'amount' => ['required', 'numeric', 'min:0.01'],
                'adjustment_date' => ['required', 'date'],
                'note' => ['required', 'string', 'max:1000'],
            ]);

            $amount = round((float) $validated['amount'], 2);

            $adjustment->update([
                'product_id' => null,
                'quantity' => 1,
                'unit_price' => $amount,
                'amount' => $amount,
                'adjustment_date' => $validated['adjustment_date'],
                'note' => $validated['note'],
            ]);
        } else {
            $validated = $request->validateWithBag('adjustmentUpdate', [
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'quantity' => ['required', 'numeric', 'min:1', 'max:9999'],
                'adjustment_date' => ['required', 'date'],
                'note' => ['nullable', 'string', 'max:1000'],
            ]);

            $currentProductId = (int) ($adjustment->product_id ?? 0);
            $currentQuantity = round((float) $adjustment->quantity, 2);
            $requestedProductId = (int) $validated['product_id'];
            $requestedQuantity = round((float) $validated['quantity'], 2);

            if ($requestedProductId !== $currentProductId || $requestedQuantity !== $currentQuantity) {
                return back()
                    ->withErrors([
                        'quantity' => __('messages.errors.employee_product_charge_inventory_locked'),
                    ], 'adjustmentUpdate')
                    ->withInput();
            }

            $product = Product::query()
                ->where('id', $currentProductId)
                ->where(function ($query) use ($adjustment): void {
                    $query
                        ->where('is_active', true)
                        ->orWhere('id', $adjustment->product_id);
                })
                ->firstOrFail();

            $note = trim((string) ($validated['note'] ?? ''));
            if ($note === '') {
                $note = sprintf('Loaded product: %s', $product->name);
            }

            $adjustment->update([
                'product_id' => $currentProductId,
                'quantity' => $currentQuantity,
                'unit_price' => $adjustment->unit_price,
                'amount' => $adjustment->amount,
                'adjustment_date' => $validated['adjustment_date'],
                'note' => $note,
            ]);
        }

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('messages.success.employee_adjustment_updated'));
    }

    public function destroy(Employee $employee, EmployeeSalaryAdjustment $adjustment): RedirectResponse
    {
        $this->ensureAdjustmentsFeatureEnabled();
        $this->ensureAdjustmentBelongsToEmployee($employee, $adjustment);

        $adjustment->delete();

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('messages.success.employee_adjustment_deleted'));
    }

    private function ensureAdjustmentsFeatureEnabled(): void
    {
        if (! Schema::hasTable('employee_salary_adjustments')) {
            abort(404);
        }
    }

    private function ensureAdjustmentBelongsToEmployee(Employee $employee, EmployeeSalaryAdjustment $adjustment): void
    {
        if ((int) $adjustment->employee_id !== (int) $employee->id) {
            abort(404);
        }
    }
}
