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
    public static $instance;

    public static function generate() {
        self::$instance = new PHPMailer(true);
        self::$instance->CharSet = 'UTF-8';

        //Server settings
        if (Config::get("mail.debug"))
            self::$instance->SMTPDebug = SMTP::DEBUG_SERVER;
        self::$instance->isSMTP();
        // Send using SMTP
        self::$instance->Host = Config::get("mail.host");
        self::$instance->SMTPAuth = Config::get("mail.smtp_auth");
        self::$instance->Username = Config::get("mail.user");
        self::$instance->Password = Config::get("mail.password");
        self::$instance->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        self::$instance->Port = Config::get("mail.port");

        //Recipients
        self::$instance->setFrom(Config::get("mail.from.email"), Config::get("mail.from.name"));
    }

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
        if(!self::$instance instanceof self) {
            self::generate();
        }



        // Add address y $to is an email address
        if (is_string($to))
            if (filter_var($to, FILTER_VALIDATE_EMAIL))
                self::$instance->addAddress($to);
            else
                throw new Exception('Email address ' . $to . ' is not valid');

        // Parse some addresses if $to is an array
        elseif (is_array($to)) {
            foreach ($to as $address) {
                if (!filter_var($address['email'], FILTER_VALIDATE_EMAIL))
                    throw new Exception('Email address ' . $address['email'] . ' is not valid');

                if ($address['type'] == "to")
                    self::$instance->addAddress($address['email'], $address['name']);
                elseif ($address['type'] == "cc")
                    self::$instance->addCC($address['email']);
                elseif ($address['type'] == "bcc")
                    self::$instance->addBCC($address['email']);
            }
        }


        if (Config::get("mail.bbc.enable", false))
            self::$instance->addBCC(Config::get("mail.reply.email"), Config::get("mail.reply.name"));

        if (Config::get("mail.reply.enable", false))
            self::$instance->addReplyTo(Config::get("mail.reply.email"), Config::get("mail.reply.name"));

        // Content

        self::$instance->Subject = $subject;

        self::$instance->isHTML();
        if(is_string($body))
            self::$instance->msgHTML($body);
        elseif(is_array($body)) {
            self::$instance->Body = $body['html'];
            self::$instance->AltBody = self::$instance->html2text($body['text']);
        }
        foreach ($files as $file) {
            self::$instance->addAttachment($file['file'], $file['name']);
        }
        foreach ($embedImages as $image) {
            self::$instance->addEmbeddedImage($image['source'], $image['name']);
        }
        return self::$instance->send();
    }

}
