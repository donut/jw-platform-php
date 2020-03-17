<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;

abstract class SuccessBody extends ResponseBody
{
  use RateLimitTrait;

  /**
   * SuccessBody constructor.
   *
   * @param object|array $data =
   *   [ 'status' => 'ok'
   *   , 'rate_limit' =>
   *      [ 'reset' => 1584408530
   *      , 'limit' => 60
   *      , 'remaining' => 39 ]]
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
