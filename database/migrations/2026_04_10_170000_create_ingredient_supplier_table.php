<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ingredient_supplier')) {
            Schema::create('ingredient_supplier', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['ingredient_id', 'supplier_id'], 'ingredient_supplier_unique');
            });
        }

        if (! Schema::hasTable('ingredients') || ! Schema::hasColumn('ingredients', 'supplier_id')) {
            return;
        }

        $rows = DB::table('ingredients')
            ->select(['id as ingredient_id', 'supplier_id'])
            ->whereNotNull('supplier_id')
            ->orderBy('id')
            ->get()
            ->map(static fn($row): array => [
                'ingredient_id' => (int) $row->ingredient_id,
                'supplier_id' => (int) $row->supplier_id,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        if (! empty($rows)) {
            DB::table('ingredient_supplier')->insertOrIgnore($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_supplier');
    }
};
