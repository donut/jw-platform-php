<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\exception;



class WebhooksRequestAuthError extends \Exception
{
  public function __construct ($message)
  {
    parent::__construct($message);
  }
}
