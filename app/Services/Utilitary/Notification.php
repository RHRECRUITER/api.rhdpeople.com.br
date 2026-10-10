<?php

namespace App\Services\Utilitary;

use Illuminate\Support\Facades\Mail;

class Notification
{
    public function sendByEmail(string $recepientName, string $recepientEmail, string $subjetc, string $bodyhtml): bool
    {
        try {
            Mail::html($bodyhtml, fn ($m) => $m->to($recepientEmail, $recepientName)->subject($subjetc));

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
