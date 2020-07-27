<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


class VideosCreateBody extends SuccessBody
{
  /** @var VideosCreateBodyVideoField */
  public $video;

  public function __construct($data)
  {
    parent::__construct($data);
    $this->video = field
      ($data, 'video', VideosCreateBodyVideoField::decoder());
  }
}


class VideosCreateBodyVideoField
{
  use DecoderTrait;

  /** @var string */
  public $key;

  public function __construct ($data)
  {
    $this->key = field($data, 'key', T\string());
  }
}
