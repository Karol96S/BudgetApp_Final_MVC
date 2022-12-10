<?php

namespace App;

class Mail
{
    /**
     * Send a message
     */
    public static function send($to, $subject, $text, $html)
    {
        // the message
        //$msg = "First line of text\nSecond line of text";

        //basic mail doesn't support both text and html so for the sake of this project
        //we will operate with just html.
        $body = $html;

        // use wordwrap() if lines are longer than 70 characters
        //$text = wordwrap($text, 70);

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: karol <karol.szypkowski96@gmail.com>' . "\r\n";

        // send email
        mail($to, $subject, $body, $headers);

    }
}
