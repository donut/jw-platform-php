<?php


namespace RightThisMinute\JWPlatform\Management\v2\exception;


use Throwable;


class DefaultSiteIDNotSet extends \Exception
{
  public function __construct ()
  {
    parent::__construct
      ('No default site ID was set when constructing Client.');
  }
}
