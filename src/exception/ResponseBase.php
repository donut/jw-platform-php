<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\exception;


use Psr\Http\Message\ResponseInterface;


abstract class ResponseBase extends \Exception
{
  /** @var string */
  protected $method;

  /** @var string */
  protected $uri;

  /** @var \Psr\Http\Message\ResponseInterface  */
  public $response;

  public function __construct
    ( string $method
    , string $uri
    , string $message
    , ResponseInterface $response
    , ?int $code=null
    , ?\Throwable $previous=null )
  {
    $this->method = $method;
    $this->uri = $uri;
    $this->response = $response;

    $status_code = $response->getStatusCode();
    $status_phrase = $response->getReasonPhrase();
    $message = "[$method $uri -> $status_code $status_phrase] $message";

    if (!isset($code) && is_numeric($status_code))
      $code = (int)$status_code;

    parent::__construct($message, $code, $previous);
  }
}
