<?php


namespace RightThisMinute\JWPlatform\Management\response;


abstract class SuccessBody extends ResponseBody
{
  use RateLimitTrait;

  public function __construct (RateLimitField $rate_limit)
  {
    parent::__construct('ok');
    $this->rate_limit = $rate_limit;
  }
}
