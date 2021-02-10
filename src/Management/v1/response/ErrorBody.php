<?php


namespace RightThisMinute\JWPlatform\Management\v1\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


class ErrorBody extends ResponseBody
{
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


  /**
   * ErrorBody constructor.
   *
   * @param object|array $data =
   *  [ 'code' => (string)
   *  , 'title' => (string)
   *  , 'message' => (string) ]
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    parent::__construct('error');

    $this->code = field($data, 'code', T\string());
    $this->title = field($data, 'title', T\string());
    $this->message = field($data, 'message', T\string());
  }
}
