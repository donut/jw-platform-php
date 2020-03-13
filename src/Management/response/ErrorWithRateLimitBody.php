<?php


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;


abstract class ErrorWithRateLimitBody extends ErrorBody
{
  use RateLimitTrait;

  /**
   * Decode a not found error response body into an instance of this class.
   *
   * @param object|array $json
   *   The API response JSON body as decoded by `json_decode()`.
   *
   * @return static
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public static function fromJSON ($json) : self
  {
    $parent = self::fieldsFromJSON($json);

    return new static
      ( $parent['code']
      , $parent['title']
      , $parent['message']
      , field($json, 'rate_limit', RateLimitField::decoder()) );
  }


  public function __construct
    (string $code, string $title, string $message, RateLimitField $rate_limit)
  {
    parent::__construct($code, $title, $message);
    $this->rate_limit = $rate_limit;
  }
}
