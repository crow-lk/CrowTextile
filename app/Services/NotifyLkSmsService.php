<?php

namespace App\Services;

use App\Models\Setting;
use NotifyLk\Api\SmsApi;

class NotifyLkSmsService
{
    public function sendSms(string $to, string $message, array $contact = []): bool
    {
        $to = $this->normalizePhone($to);

        if (! $to) {
            throw new \InvalidArgumentException('Invalid phone number format.');
        }

        $settings = Setting::query()->first();

        if (! $settings || ! $settings->notify_enabled) {
            return false;
        }

        $userId = $settings->notify_user_id;
        $apiKey = $settings->notify_api_key;
        $senderId = $settings->notify_sender_id;

        if (! $userId || ! $apiKey || ! $senderId) {
            return false;
        }

        $api = new SmsApi();

        $contactFirstName = (string) ($contact['first_name'] ?? '');
        $contactLastName = (string) ($contact['last_name'] ?? '');
        $contactEmail = (string) ($contact['email'] ?? '');
        $contactAddress = (string) ($contact['address'] ?? '');
        $contactGroup = (int) ($contact['group_id'] ?? 0);
        $type = $settings->notify_unicode ? 'unicode' : null;

        $api->sendSMS(
            $userId,
            $apiKey,
            $message,
            $to,
            $senderId,
            $contactFirstName,
            $contactLastName,
            $contactEmail,
            $contactAddress,
            $contactGroup,
            $type
        );

        return true;
    }

    private function normalizePhone(string $phone): ?string
    {
        $normalized = trim($phone);
        $normalized = str_replace([' ', '-', '(', ')'], '', $normalized);

        if ($normalized === '') {
            return null;
        }

        if (str_starts_with($normalized, '+')) {
            $normalized = ltrim($normalized, '+');
        }

        if (str_starts_with($normalized, '0') && strlen($normalized) === 10) {
            return '94' . substr($normalized, 1);
        }

        if (str_starts_with($normalized, '7') && strlen($normalized) === 9) {
            return '94' . $normalized;
        }

        if (str_starts_with($normalized, '94') && strlen($normalized) >= 11 && strlen($normalized) <= 12) {
            return $normalized;
        }

        return null;
    }
}
