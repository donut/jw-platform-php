<?php


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;

class RateLimitField
{

  /**
   * Returns a function for decoding structured data into an instance of this
   * class.
   *
   * @return callable
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  static public function decoder () : callable
  {
    return function($v){ return new static($v); };
  }

  /**
   * @var int
   */
  public $reset;

  /**
   * @var int
   */
  public $limit;

  /**
   * @var int
   */
  public $remaining;

  /**
   * RateLimitField constructor.
   *
   * @param object|array $data =
   *    [ 'reset' => (int)
   *    , 'limit' => (int)
   *    , 'remaining' => (int) ]
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $this->reset = field($data, 'reset', T\int());
    $this->limit = field($data, 'limit', T\int());
    $this->remaining = field($data, 'remaining', T\int());
  }
}

