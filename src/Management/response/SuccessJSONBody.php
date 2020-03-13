<?php


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;

class SuccessJSONBody extends SuccessBody
{

  /**
   * @var object|array
   */
  public $json;

  public function __construct ($json)
  {
    $this->json = $json;
    $rate_limit = field($json, 'rate_limit', RateLimitField::decoder());

    parent::__construct($rate_limit);
  }

}
