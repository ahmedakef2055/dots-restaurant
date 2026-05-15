<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PromotionService
{
    public function resolveOffer(float $subtotal, string $orderType): ?array
    {
        if ($subtotal <= 0) {
            return null;
        }

        $now = now();

        $offers = Offer::query()
            ->where('is_active', true)
            ->where(function ($query) use ($orderType): void {
                $query->whereNull('order_type')->orWhere('order_type', $orderType);
            })
            ->where('min_order_amount', '<=', $subtotal)
            ->where(function ($query) use ($now): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('priority')
            ->orderByDesc('discount_value')
            ->get();

        if ($offers->isEmpty()) {
            return null;
        }

        $best = null;

        foreach ($offers as $offer) {
            $discountAmount = $this->calculateDiscountAmount(
                $offer->discount_type,
                (float) $offer->discount_value,
                $subtotal,
                $offer->max_discount_amount !== null ? (float) $offer->max_discount_amount : null,
            );

            if ($discountAmount <= 0) {
                continue;
            }

            if (! $best || $discountAmount > $best['discount_amount']) {
                $best = [
                    'offer' => $offer,
                    'discount_amount' => round($discountAmount, 2),
                ];
            }
        }

        return $best;
    }

    public function validateCoupon(?string $couponCode, float $subtotal, bool $lockForUpdate = false): ?array
    {
        if (! $couponCode) {
            return null;
        }

        $couponQuery = Coupon::query()
            ->where('code', strtoupper(trim($couponCode)));

        if ($lockForUpdate) {
            $couponQuery->lockForUpdate();
        }

        $coupon = $couponQuery->first();

        if (! $coupon || ! $coupon->is_active) {
            throw ValidationException::withMessages([
                'coupon_code' => __('messages.errors.coupon_invalid_or_inactive'),
            ]);
        }

        $now = now();

        if ($coupon->starts_at && $now->lt(Carbon::parse($coupon->starts_at))) {
            throw ValidationException::withMessages([
                'coupon_code' => __('messages.errors.coupon_not_active_yet'),
            ]);
        }

        if ($coupon->ends_at && $now->gt(Carbon::parse($coupon->ends_at))) {
            throw ValidationException::withMessages([
                'coupon_code' => __('messages.errors.coupon_expired'),
            ]);
        }

        if ($subtotal < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => __('messages.errors.coupon_min_order_not_met'),
            ]);
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw ValidationException::withMessages([
                'coupon_code' => __('messages.errors.coupon_usage_limit_reached'),
            ]);
        }

        $userId = Auth::id();

        if ($coupon->per_user_limit !== null && $userId) {
            $usedByUser = $coupon->redemptions()->where('user_id', $userId)->count();
            if ($usedByUser >= $coupon->per_user_limit) {
                throw ValidationException::withMessages([
                    'coupon_code' => __('messages.errors.coupon_per_user_limit_reached'),
                ]);
            }
        }

        $discountAmount = $this->calculateDiscountAmount(
            $coupon->discount_type,
            (float) $coupon->discount_value,
            $subtotal,
            $coupon->max_discount_amount !== null ? (float) $coupon->max_discount_amount : null,
        );

        if ($discountAmount <= 0) {
            throw ValidationException::withMessages([
                'coupon_code' => __('messages.errors.coupon_discount_not_applicable'),
            ]);
        }

        return [
            'coupon' => $coupon,
            'discount_amount' => round($discountAmount, 2),
        ];
    }

    public function calculateDiscountAmount(string $type, float $value, float $subtotal, ?float $maxDiscount = null): float
    {
        $discountAmount = $type === 'percentage'
            ? ($subtotal * max($value, 0)) / 100
            : max($value, 0);

        if ($maxDiscount !== null) {
            $discountAmount = min($discountAmount, max($maxDiscount, 0));
        }

        return min($discountAmount, max($subtotal, 0));
    }
}
