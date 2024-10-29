<?php

namespace Cavesman;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Email class
 * @example Mail::send(['type' => 'to', 'email' => 'address', 'name' => 'John Doe'], 'Hello world!', '<b>Hello Everyone! Alright!</b>', ['file' => $filepath, 'name' => 'Report.pdf'])
 */
class Mail
{

    /**
     * @param string|array $to email@domain.tld or [{type: <to|cc|bcc>, email: string<email address>, name: string <Address name>}]
     * @param string $subject
     * @param string|array $body Body or {html: HtmlBody, message: Plaintext}
     * @param array $files
     * @param array $embedImages
     * @return bool
     * @throws Exception
     */
    public static function send(string|array $to, string $subject = '', string|array  $body = '', array $files = [], array $embedImages = []): bool
    {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        //Server settings
        if (Config::get("mail.debug"))
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        // Send using SMTP
        $mail->Host = Config::get("mail.host");
        $mail->SMTPAuth = Config::get("mail.smtp_auth");
        $mail->Username = Config::get("mail.user");
        $mail->Password = Config::get("mail.password");
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = Config::get("mail.port");

        //Recipients
        $mail->setFrom(Config::get("mail.from.email"), Config::get("mail.from.name"));

        // Add address y $to is an email address
        if (is_string($to))
            if (filter_var($to, FILTER_VALIDATE_EMAIL))
                $mail->addAddress($to);
            else
                throw new Exception('Email address ' . $to . ' is not valid');

        // Parse some addresses if $to is an array
        elseif (is_array($to)) {
            foreach ($to as $address) {
                if (!filter_var($address['email'], FILTER_VALIDATE_EMAIL))
                    throw new Exception('Email address ' . $address['email'] . ' is not valid');

                if ($address['type'] == "to")
                    $mail->addAddress($address['email'], $address['name']);
                elseif ($address['type'] == "cc")
                    $mail->addCC($address['email']);
                elseif ($address['type'] == "bcc")
                    $mail->addBCC($address['email']);
            }
        }


        if (Config::get("mail.bbc.enable", false))
            $mail->addBCC(Config::get("mail.reply.email"), Config::get("mail.reply.name"));

        if (Config::get("mail.reply.enable", false))
            $mail->addReplyTo(Config::get("mail.reply.email"), Config::get("mail.reply.name"));

        $mail->addEmbeddedImage(_THEME_ . '/assets/img/logo/logo-yellow.png', 'logo');

        // Content

        $mail->Subject = $subject;

        $mail->isHTML();
        if(is_string($body))
            $mail->msgHTML($body);
        elseif(is_array($body)) {
            $mail->Body = $body['html'];
            $mail->AltBody = $mail->html2text($body['text']);
        }
        foreach ($files as $file) {
            $mail->addAttachment($file['file'], $file['name']);
        }
        foreach ($embedImages as $image) {
            $mail->addEmbeddedImage($image['source'], $image['name']);
        }
        return $mail->send();
    }

}
