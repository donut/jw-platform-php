<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v1\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\exception\ResponseBase;
use RightThisMinute\JWPlatform\Management\v1\response\ErrorBody;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;


abstract class ErrorResponse extends ResponseBase
{
  /** @var ErrorBody */
  public $body;

  /**
   * ErrorResponse constructor.
   *
   * @param string $method
   * @param string $uri
   * @param ResponseInterface $response
   * @param object|ErrorBody $body
   *   Either the decoded JSON object from the response body or an instance
   *   of ErrorBody.
   *
   * @throws DecodeError
   */
  public function __construct
    ( string $method
    , string $uri
    , ResponseInterface $response
    , object $body )
  {
    if ($body instanceof ErrorBody)
      $this->body = $body;
    else
      $this->body = new ErrorBody($body);

    $message = "({$this->body->code}) {$this->body->message}";
    parent::__construct($method, $uri, $message, $response);
  }
}
