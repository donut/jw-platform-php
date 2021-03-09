<?php


namespace RightThisMinute\JWPlatform\Management\v2\response;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;


class SuccessWithBody extends Success
{
  /**
   * @var object|array
   */
  public $body;

  /**
   * SuccessWithBody constructor.
   *
   * @param BaseJWHeaders|string[][] $headers
   *   The headers array as returned by `ResponseInterface::getHeaders()` or
   *   headers of an existing Success instance.
   * @param object|array $body
   *   The decoded JSON body of a response.
   *
   * @throws DecodeError
   */
  public function __construct ($headers, $body)
  {
    parent::__construct($headers);
    $this->body = $body;
  }
}
