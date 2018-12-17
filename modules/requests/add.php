<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 29/11/2018
 * Time: 20:43
 */

$init = function (array $data) : array { return [
    // Source: https://stackoverflow.com/questions/15737408/php-find-all-occurrences-of-a-substring-in-a-string
    'strpos_all' => function (string $haystack, string $needle) : array {
        $offset = 0;
        $allpos = array();
        while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
            $offset   = $pos + 1;
            $allpos[] = $pos;
        }
        return $allpos;
    }
]; };

$exec = function (array $data, array $data_init) : array {

    global $db;
    global $token;

    if(!isset($data['description']))
        throw new InvalidRequestException("description comment be blank", 3000);
    else
        str_replace('"', '\"', $data['description'], count($data_init['strpos_all']($data['description'],'"')));

    if(!isset($data['destination_client'])) throw new InvalidRequestException("destination_client cannot be blank", 3000);
    else{
        if((string)((int) $data['destination_client']) !== $data['destination_client'])
            throw new InvalidRequestException("destination_client must be an ID integer");
    }

    $user_id = getUserData($db, $token)['user_id'];

    $db->query("INSERT INTO requests_m(description, requester, destination_client) VALUES (\"{$data['description']}\",$user_id, {$data['destination_client']})");

    return [
        "response_data" => [],
        "status_code" => 200
    ];

};