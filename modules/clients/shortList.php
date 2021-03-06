<?php

/**
 * Get the list of clients present in the DB
 */
$exec = function (array $data, array $data_init) : array {
    global $db;
    $out = [];

    $data = $db->preparedQuery("SELECT users.user_id FROM users, users_roles WHERE 
        users.user_id=users_roles.user_id AND role_id=?", [4]);   //users
    
    foreach ($data as $entry)
        $out[] = getUserInfo($entry['user_id']);

    return [
        "response_data" => $out,
        "status_code" => 200
    ];
};