<?php


namespace RightThisMinute\JWPlatform\Management\v1\response;


use function RightThisMinute\StructureDecoder\field;

class SuccessJSONBody extends SuccessBody
{
  /**
   * @var object|array
   */
  public $json;

  public function __construct ($json)
  {
    parent::__construct($json);
    $this->json = $json;
  }

}
