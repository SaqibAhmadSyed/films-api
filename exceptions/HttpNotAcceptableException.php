<?php

namespace Vanier\Api\Exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpNotAcceptableException extends HttpSpecializedException
{
     /**
     * @var int
     */
    protected $code = 406;

    /**
     * @var string
     */

    protected $message = 'Not acceptable.';
    protected $title = '406 Not Acceptable';
    protected $description = 'the server cannot produce a response matching the list of acceptable values.';

    protected $errorMessage = [$message, $title, $description];
}
?>
