<?php

$init = function (array $data) : array { return []; };

/**
 * Get the list of roles present in the DB (id + desc)
 */
$exec = function (array $data, array $data_init) : array {
    global $db;

    $data = $db->query("SELECT * FROM roles");

    $out = [];

    foreach ($data as $entry)
        $out[] = [
            'role_id' => $entry['role_id'],
            'role_string' => $entry['role_string']
        ];

    return [
        "response_data" => $out,
        "status_code" => 200
    ];
};