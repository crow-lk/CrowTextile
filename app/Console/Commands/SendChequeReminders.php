<?php

namespace App\Console\Commands;

use App\Models\Cheque;
use App\Models\Setting;
use App\Services\NotifyLkSmsService;
use Illuminate\Console\Command;

class SendChequeReminders extends Command
{
    protected $signature = 'cheques:send-reminders';

    protected $description = 'Send SMS reminders for scheduled cheques.';

    public function handle(NotifyLkSmsService $smsService): int
    {
        $settings = Setting::query()->first();

        if (! $settings || ! $settings->notify_enabled) {
            $this->info('Notify.lk is disabled or settings not configured.');
            return self::SUCCESS;
        }

        $phones = array_values(array_filter(
            (array) ($settings->notify_phones ?? []),
            static fn ($value): bool => is_string($value) && trim($value) !== ''
        ));

        if (empty($phones) && is_string($settings->notify_phone) && trim($settings->notify_phone) !== '') {
            $phones = [trim($settings->notify_phone)];
        }

        if (empty($phones)) {
            $this->warn('Notification phones are not set.');
            return self::SUCCESS;
        }

        $cheques = Cheque::query()
            ->whereNotNull('remind_at')
            ->whereNull('reminder_sent_at')
            ->where('remind_at', '<=', now())
            ->where('status', 'pending')
            ->get();

        if ($cheques->isEmpty()) {
            $this->info('No cheque reminders to send.');
            return self::SUCCESS;
        }

        foreach ($cheques as $cheque) {
            $message = $this->buildMessage($cheque);
            $sent = false;
            foreach ($phones as $phone) {
                try {
                    $result = $smsService->sendSms($phone, $message, [
                        'first_name' => 'Admin',
                    ]);
                } catch (\Throwable $exception) {
                    report($exception);
                    $this->error(
                        'Failed to send reminder for cheque #' . $cheque->id . ' to ' . $phone . '. ' .
                        $exception->getMessage()
                    );
                    continue;
                }

                if ($result === false) {
                    $this->warn('Notify.lk settings incomplete. Reminder not sent for cheque #' . $cheque->id . '.');
                    continue;
                }

                $sent = true;
            }

            if ($sent) {
                $cheque->forceFill(['reminder_sent_at' => now()])->save();
            }
        }

        $this->info('Cheque reminders sent: ' . $cheques->count());

        return self::SUCCESS;
    }

    private function buildMessage(Cheque $cheque): string
    {
        $type = $cheque->direction === 'from_account'
            ? 'From Account'
            : 'To Accounts';

        $amount = number_format((float) $cheque->amount, 2);
        $dueDate = $cheque->due_date ? $cheque->due_date->format('Y-m-d') : null;
        $party = $cheque->party_name ? 'Party: ' . $cheque->party_name . '. ' : '';
        $chequeNo = $cheque->cheque_number ? 'Cheque #: ' . $cheque->cheque_number . '. ' : '';
        $dueText = $dueDate ? 'Due: ' . $dueDate . '. ' : '';

        return "Cheque Reminder ({$type}). {$chequeNo}{$party}Amount: LKR {$amount}. {$dueText}";
    }
}
