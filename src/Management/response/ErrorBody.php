<?php


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


abstract class ErrorBody extends ResponseBody
{

  /**
   * @param $json
   *
   * @return array
   *   An associative array of fields to be passed to a child class'
   *   constructor.
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  protected static function fieldsFromJSON ($json) : array
  {
    return
      [ 'code' => field($json, 'code', T\string())
      , 'title' => field($json, 'title', T\string())
      , 'message' => field($json, 'message', T\string()) ];
  }

  /**
   * @var string
   */
  public $code;

  /**
   * @var string
   */
  public $title;

  /**
   * @var string
   */
  public $message;


  public function __construct (string $code, string $title, string $message)
  {
    parent::__construct('error');

    $this->code = $code;
    $this->title = $title;
    $this->message = $message;
  }
}
