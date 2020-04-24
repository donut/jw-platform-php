<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Management\response\ErrorWithRateLimitBody;

class ErrorWithRateLimitResponse extends ErrorResponse
{
    /** @var ErrorWithRateLimitBody */
  public $body;

  public function __construct
    ( string $method
    , string $uri
    , ResponseInterface $response
    , object $json_body )
  {
    $body = new ErrorWithRateLimitBody($json_body);
    parent::__construct($method, $uri, $response, $body);
  }
}
