<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Management\response\ErrorBody;


abstract class ErrorResponse extends ResponseBase
{
  /** @var ErrorBody */
  public $body;

  /**
   * ErrorResponse constructor.
   *
   * @param string $method
   * @param string $uri
   * @param \Psr\Http\Message\ResponseInterface $response
   * @param object|ErrorBody $body
   *   Either the decoded JSON object from the response body or an instance
   *   of ErrorBody.
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct
    ( string $method
    , string $uri
    , ResponseInterface $response
    , object $body )
  {
    if ($body instanceof ErrorBody)
      $this->body;
    else
      $this->body = new ErrorBody($body);

    $message = "({$this->body->code}) {$this->body->message}";
    parent::__construct($method, $uri, $message, $response);
  }
}
