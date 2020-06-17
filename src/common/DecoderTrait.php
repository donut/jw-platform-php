<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\common;


trait DecoderTrait
{
  public static function decoder () : callable
  {
    return function ($data) : self { return new static($data); };
  }
}
