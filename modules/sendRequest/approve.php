<?php

$init = function (array $data) : array { return []; };

$exec = function (array $data, array $data_init) : array {
    require_once __DIR__ . "/../../lib/libCommitRequest/libApprove.php";

    approve($data, TYPE_REQUEST);

    return [
        "response_data" => [],
        "status_code" => 200,
    ];
};