<?php


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;

class RateLimitField
{

  /**
   * Decode the `rate_limit` field of a response body.
   *
   * @param object|array $json
   *   The API response JSON body as decoded by `json_decode()`.
   *
   * @return static
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  static public function fromJSON ($json) : self
  {
    return new self
      ( field($json, 'reset', T\int())
      , field($json, 'limit', T\int())
      , field($json, 'remaining', T\int()) );
  }

  /**
   * Returns a function for decoding structured data into an instance of this
   * class.
   *
   * @return callable
   */
  static public function decoder () : callable
  {
    return function($v){ return self::fromJSON($v); };
  }

  /**
   * @var int
   */
  private $reset;

  /**
   * @var int
   */
  private $limit;

  /**
   * @var int
   */
  private $remaining;

  public function __construct (int $reset, int $limit, int $remaining)
  {
    $this->reset = $reset;
    $this->limit = $limit;
    $this->remaining = $remaining;
  }
}
