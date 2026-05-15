<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PurchaseRequestReviewedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int $purchaseId,
        private readonly string $purchaseNumber,
        private readonly string $action,
        private readonly ?string $reviewedBy,
        private readonly ?string $approvalComment,
        private readonly ?string $reviewedAt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'category' => 'purchase_review',
            'action' => $this->action,
            'purchase_id' => $this->purchaseId,
            'purchase_number' => $this->purchaseNumber,
            'reviewed_by' => $this->reviewedBy,
            'approval_comment' => $this->approvalComment,
            'reviewed_at' => $this->reviewedAt,
            'url' => route('purchases.show', $this->purchaseId),
        ];
    }
}
