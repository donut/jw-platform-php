<?php


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;


class ErrorWithRateLimitBody extends ErrorBody
{
  use RateLimitTrait;

  public function __construct ($data)
  {
    parent::__construct($data);
    $this->rate_limit =
      field($data, 'rate_limit', RateLimitField::decoder());
  }
}
