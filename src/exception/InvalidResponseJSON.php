<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Management\exception\ResponseBase;


class InvalidResponseJSON extends ResponseBase
{
  public function __construct
    ( string $method
    , string $uri
    , ResponseInterface $response
    , \JsonException $exception )
  {
    parent::__construct
      ( $method
      , $uri
      , "Error parsing response body as JSON: "
        . $exception->getMessage()
      , $response
      , null
      , $exception );
  }
}
