<?php

class NotImplementedException extends ExceptionRequest {

    public function __construct(string $message = "Not implemented", string $error_code = "ERROR_GLOBAL_NOT_IMPLEMENTED", int $status_code = 404) {
        parent::__construct($message, $error_code, $status_code);
    }

    public function getErrorResponse() : array {
        return parent::getErrorResponse();
    }

}
