<?php

define("MAIL_APPROVED", 1);
define("MAIL_NEW_COMMIT", 2);
define("MAIL_NEW_PATCH", 3);

foreach (scandir(__DIR__) as $file) {
    switch ($file){
        case ".":
        case "..":
        case "include.php":
            break;
        default:
            if (strpos($file, ".php"))
                require_once __DIR__ . "/" . $file;
            break;
    }
}

function send($to_user_id, $id, $type, $typeCommit) {
    global $db;
    global $user;
    $idCommit;

    switch($typeCommit) {
        case TYPE_COMMIT:
            $idCommit = TYPE_COMMIT_ID;
            break;
        case TYPE_REQUEST:
            $idCommit = TYPE_REQUEST_ID;
            break;
    }

    $from = $user;
    $to = getUserInfo($to_user_id);
    $mail;

    switch($type) {
        case MAIL_APPROVED:
            $mail = new ApprovedMail($from['name'], $to['name'], $typeCommit, $idCommit, $id);
            break;
        case MAIL_NEW_COMMIT:
            $mail = new NewCommitMail($from['name'], $to['name'],$typeCommit, $idCommit, $id);
            break;
        case MAIL_NEW_PATCH:
            $mail = new NewPatchMail($from['name'], $to['name']);
            break;
        default:
            throw new InvalidRequestException("Tipo di mail non riconosciuto!");
            break;
    }

    $subject = $mail->getSubject();
    $message = $mail->getMsg();

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: <${from['email']}>" . "\r\n";

    mail("aum.coopcisf@gmail.com", $subject, $message, $headers);
}