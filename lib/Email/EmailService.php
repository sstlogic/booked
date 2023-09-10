<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(ROOT_DIR . 'lib/Email/namespace.php');
require_once(ROOT_DIR . 'lib/external/phpmailer/src/Exception.php');
require_once(ROOT_DIR . 'lib/external/phpmailer/src/PHPMailer.php');
require_once(ROOT_DIR . 'lib/external/phpmailer/src/SMTP.php');

class EmailService implements IEmailService
{
    public function Send(IEmailMessage $emailMessage)
    {
        $phpMailer = new PHPMailer();
        $phpMailer->Mailer = $this->Config('mailer');
        $phpMailer->Host = $this->Config('smtp.host');
        $phpMailer->Port = $this->Config('smtp.port', new IntConverter());
        $phpMailer->SMTPSecure = $this->Config('smtp.secure');
        $phpMailer->SMTPAuth = $this->Config('smtp.auth', new BooleanConverter());
        $phpMailer->Username = $this->Config('smtp.username');
        $phpMailer->Password = $this->Config('smtp.password');
        $phpMailer->Sendmail = $this->Config('sendmail.path');
        $phpMailer->SMTPDebug = $this->Config('smtp.debug', new BooleanConverter());

        $phpMailer->CharSet = $emailMessage->Charset();
        $phpMailer->Subject = $emailMessage->Subject();
        $phpMailer->Body = $emailMessage->Body();
        $phpMailer->isHTML(true);

        $from = $emailMessage->From();
        $defaultFrom = Configuration::Instance()->GetSectionKey(ConfigSection::EMAIL, ConfigKeys::DEFAULT_FROM_ADDRESS);
        $defaultName = Configuration::Instance()->GetSectionKey(ConfigSection::EMAIL, ConfigKeys::DEFAULT_FROM_NAME);
        $address = empty($defaultFrom) ? $from->Address() : $defaultFrom;
        $name = empty($defaultName) ? $from->Name() : $defaultName;
        $phpMailer->setFrom($address, $name);

        $replyTo = $emailMessage->ReplyTo();
        $phpMailer->addReplyTo($replyTo->Address(), $replyTo->Name());

        $to = $this->ensureArray($emailMessage->To());
        $toAddresses = new StringBuilder();
        foreach ($to as $address) {
            $toAddresses->Append($address->Address());
            $phpMailer->addAddress($address->Address(), $address->Name());
        }

        $cc = $this->ensureArray($emailMessage->CC());
        foreach ($cc as $address) {
            $phpMailer->addCC($address->Address(), $address->Name());
        }

        $bcc = $this->ensureArray($emailMessage->BCC());
        foreach ($bcc as $address) {
            $phpMailer->addBCC($address->Address(), $address->Name());
        }

        if ($emailMessage->HasStringAttachment()) {
            Log::Debug('Adding email attachment', ['attachmentName' => $emailMessage->AttachmentFileName()]);
            $phpMailer->addStringAttachment($emailMessage->AttachmentContents(), $emailMessage->AttachmentFileName());
        }

        $embeddedImages = $emailMessage->EmbeddedImages();
        if (!empty($embeddedImages)) {
            foreach ($embeddedImages as $image) {
                Log::Debug("Adding email embedded image");
                $phpMailer->addStringEmbeddedImage(base64_decode($image->Contents), $image->Cid, $image->Cid . '.png', 'base64', 'image/png');
            }
        }

        Log::Debug('Sending email', ['emailType' => get_class($emailMessage), 'to' => $toAddresses->ToString(), 'from' => $from->Address()]);

        $success = false;
        try {
            $success = $phpMailer->send();
        } catch (Exception $ex) {
            Log::Error('Failed sending email', ['exception' => $ex]);
        }

        Log::Debug('Email send result', ['wasSuccessful' => $success, 'errorInfo' => $phpMailer->ErrorInfo]);
    }

    /**
     * @param $key
     * @param IConvert|null $converter
     * @return mixed|string
     */
    private function Config($key, $converter = null)
    {
        return Configuration::Instance()->GetSectionKey('phpmailer', $key, $converter);
    }

    /**
     * @param $possibleArray array|EmailAddress[]
     * @return array|EmailAddress[]
     */
    private function ensureArray($possibleArray)
    {
        if (is_array($possibleArray)) {
            return $possibleArray;
        }

        return array($possibleArray);
    }

}

class NullEmailService implements IEmailService
{
    /**
     * @param IEmailMessage $emailMessage
     */
    function Send(IEmailMessage $emailMessage)
    {
        // no-op
    }
}
