<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceRequestNotification extends Notification
{
    use Queueable;

    // Type constants
    const REQUEST_CREATED  = 'service_request_created';
    const REQUEST_APPROVED = 'service_request_approved';
    const REQUEST_REJECTED = 'service_request_rejected';

    public function __construct(
        private ServiceRequest $request,
        private string $type,
        private string $message,
    ) {}

    public function via(): array
    {
        return ['database'];
    }

    public function toDatabase(): array
    {
        return [
            'type'           => $this->type,
            'request_id'     => $this->request->id,
            'request_number' => $this->request->request_number,
            'service_name'   => $this->request->service?->name ?? '—',
            'message'        => $this->message,
            'icon'           => $this->iconForType(),
            'color'          => $this->colorForType(),
            'url'            => $this->urlForType(),
        ];
    }

    private function iconForType(): string
    {
        return match($this->type) {
            self::REQUEST_CREATED  => 'storefront',
            self::REQUEST_APPROVED => 'check_circle',
            self::REQUEST_REJECTED => 'cancel',
            default                => 'notifications',
        };
    }

    private function colorForType(): string
    {
        return match($this->type) {
            self::REQUEST_CREATED  => 'status-info',
            self::REQUEST_APPROVED => 'status-success',
            self::REQUEST_REJECTED => 'status-danger',
            default                => 'accent-light',
        };
    }

    private function urlForType(): string
    {
        // For created requests: point approvers/managers to the approvals page
        // For decision notifications: point the requester to their requests page
        return match($this->type) {
            self::REQUEST_CREATED  => route('service-requests.approvals'),
            self::REQUEST_APPROVED,
            self::REQUEST_REJECTED => route('service-requests.my'),
            default                => route('service-requests.my'),
        };
    }
}
