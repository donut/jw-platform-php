<?php


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;

abstract class SuccessBody extends ResponseBody
{
  use RateLimitTrait;

  /**
   * SuccessBody constructor.
   *
   * @param object|array $data
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    parent::__construct('ok');
    $this->rate_limit =
      field($data, 'rate_limit', RateLimitField::decoder());
  }
}
