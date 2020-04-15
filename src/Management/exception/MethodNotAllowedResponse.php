<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Management\response\MethodNotAllowedBody;

class MethodNotAllowedResponse extends ErrorResponse
{
  /** @var MethodNotAllowedBody */
  public $body;

  /**
   * MethodNotAllowedResponse constructor.
   *
   * @param string $method
   * @param string $endpoint
   * @param \Psr\Http\Message\ResponseInterface $response
   * @param $body_json
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct
    ( string $method
    , string $endpoint
    , ResponseInterface $response
    , $body_json )
  {
    $body = new MethodNotAllowedBody($body_json);
    parent::__construct($method, $endpoint, $response, $body);
  }

}
