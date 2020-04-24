<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Delivery\response\ErrorBody;
use RightThisMinute\JWPlatform\exception\ResponseBase;

class ErrorResponse extends ResponseBase
{
  /** @var \RightThisMinute\JWPlatform\Delivery\response\ErrorBody */
  public $body;

  /**
   * ErrorResponse constructor.
   *
   * @param string $method
   * @param string $uri
   * @param \Psr\Http\Message\ResponseInterface $response
   * @param object $json_body
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct
    ( string $method
    , string $uri
    , ResponseInterface $response
    , object $json_body )
  {
    $this->body = new ErrorBody($json_body);
    parent::__construct($method, $uri, $this->body->message, $response);
  }
}
