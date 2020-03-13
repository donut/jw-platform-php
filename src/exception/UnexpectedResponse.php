<?php

declare(strict_types=1);


namespace RightThisMinute\JWPlatform\exception;


use Psr\Http\Message\ResponseInterface;

class UnexpectedResponse extends \Exception
{
  public function __construct
    (string $request_description, ResponseInterface $response)
  {
    parent::__construct
      ( "Unexpected responose to request: $request_description"
      , $response->getStatusCode() );
  }
}
