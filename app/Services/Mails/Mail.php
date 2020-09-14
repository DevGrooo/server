<?php

namespace App\Services\Mails;

use Exception;
use Illuminate\Support\Facades\Mail as BasicMail;

class Mail {
    public static function send($to_email, $to_name, $subject, $template, $data = null, $from_email = null, $from_name = null) {
        try {
            if ($from_email === null) {
                $from_email = config('mail.from.address');
            }
            if ($from_name === null) {
                $from_name = config('mail.from.name');
            }
            BasicMail::send($template, $data, function($message) use ($from_email, $from_name, $to_name, $to_email, $subject) {
                $message->to($to_email, $to_name)->subject($subject);
                $message->from($from_email, $from_name);
            });
            return true;
        } catch (Exception $e) {
            var_dump($e);
        }
        return false;
    }
}