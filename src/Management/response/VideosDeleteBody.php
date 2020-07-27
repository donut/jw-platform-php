<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


class VideosDeleteBody extends SuccessBody
{
  /** @var string[] */
  public $videos;

  public function __construct ($data)
  {
    parent::__construct($data);
    $this->videos = field($data, 'videos', T\dict_of(T\string()));
  }
}
