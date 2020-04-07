<?php

declare(strict_types=1);


namespace RightThisMinute\JWPlatform\exception;


use Psr\Http\Message\ResponseInterface;

class UnexpectedResponse extends \Exception
{
  public function __construct
    ( string $request_description
    , ResponseInterface $response
    , ?\Throwable $exn=null )
  {
    $status = $response->getStatusCode();
    $reason = $response->getReasonPhrase();

    parent::__construct
      ( "Unexpected response [$status $reason] to request [$request_description]: $exn"
      , $response->getStatusCode()
      , $exn );
  }
}
