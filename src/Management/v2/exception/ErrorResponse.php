<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\exception\ResponseBase;
use RightThisMinute\JWPlatform\Management\v2\response\Error;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function Functional\map;


abstract class ErrorResponse extends ResponseBase
{
  /**
   * The parsed error response.
   *
   * @var Error
   */
  public $error;

  /**
   * ErrorResponse constructor.
   *
   * @param string $method
   * @param string $uri
   * @param ResponseInterface $response
   * @param object|array $data
   *   The decoded JSON body of the response.
   *
   * @throws DecodeError
   */
  public function __construct
    ( string $method
    , string $uri
    , ResponseInterface $response
    , $data )
  {
    $this->error = new Error($response->getHeaders(), $data);

    $messages = map($this->error->errors, function($error){
      return "({$error->code}) {$error->description}";
    });
    $message = implode('; ', $messages);
    parent::__construct($method, $uri, $message, $response);
  }
}
