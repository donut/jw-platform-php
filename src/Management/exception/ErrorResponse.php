<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Management\response\ErrorBody;


abstract class ErrorResponse extends ResponseBase
{
  /** @var ErrorBody */
  public $body;

  public function __construct
    ( string $method
    , string $endpoint
    , ResponseInterface $response
    , ErrorBody $body )
  {
    $this->body = $body;
    $message = "({$body->code}) {$body->message}";
    parent::__construct($method, $endpoint, $message, $response);
  }
}
