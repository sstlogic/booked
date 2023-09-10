<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class EmailLogger implements IEmailService
{
    /**
     * @param IEmailMessage $emailMessage
     */
    function Send(IEmailMessage $emailMessage)
    {
        if (is_array($emailMessage->To())) {
            $to = implode(', ', $emailMessage->To());
        } else {
            $to = $emailMessage->To();
        }
        Log::Debug('Sending Email.', ['to' => $to, 'from' => $emailMessage->From(),
            'subject' => $emailMessage->Subject(), 'body' => $emailMessage->Body()]);
    }
}

