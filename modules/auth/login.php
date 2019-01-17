<?php

$init = function (array $data) : array { return []; };

$exec = function (array $data, array $data_init) : array {
    global $db;

    //Check fields presence
    if(!isset($data['username']))
        throw new InvalidRequestException("'username' field cannot be blank");

    if(!isset($data['password']))
        throw new InvalidRequestException("'password' field cannot be blank");

    //Compute the hash of the password and verify if the user is present in the DB TODO: Verify
    $hash_pass = strtoupper(hash("sha256", $data['password']));
    $result = $db->query("SELECT user_id FROM users WHERE username = '{$data['username']}' AND hash_pass = '{$hash_pass}'");

    if(is_bool($result) or count($result) == 0)
        throw new InvalidCredentialsException("Credentials are wrong");

    //Get the ID of the user
    $user_id = $result[0]['user_id'];

    //Generate a random access token
    $token = sha1(random_bytes(64));

    #$db->query("UPDATE users SET token = '$token' WHERE user_id = $user_id");
    //Get the current user's token list
    $tokens = $db->query("SELECT token, token_expire FROM users_tokens WHERE user_id = $user_id ORDER BY token_expire ASC");

    //If there are more than 5 tokens, overwrite one of them (max 5 sessions are allowed); else add it to the list
    if(count($tokens) >= 5)
        $db->query("UPDATE users_tokens SET token = '$token' WHERE token_expire = {$tokens[0]['token_expire']} AND user_id = $user_id");
    else
        $db->query("INSERT INTO users_tokens(user_id,token) VALUES($user_id,'$token')");

    return [
        "response_data" => [
            "token" => $token,
            "user_id" => $user_id
        ],
        "status_code" => 200
    ];
};