<?php


namespace RightThisMinute\JWPlatform\Management\response;


class BadRequestBody extends ErrorBody
{
  /**
   * Decode a bad request response body into an instance of this class.
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
    return new self($parent['code'], $parent['title'], $parent['message']);
  }

  public function __construct (string $code, string $title, string $message)
  {
    parent::__construct($code, $title, $message);
  }
}
