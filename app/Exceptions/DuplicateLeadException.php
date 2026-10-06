<?php

namespace App\Exceptions;

use Exception;

class DuplicateLeadException extends Exception
{
    public function __construct(string $message = 'Lead already submitted recently')
    {
        parent::__construct($message, 409);
    }
}
