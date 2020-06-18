<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\response;


use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\field;


class MediaBody
{
  /** @var string */
  public $title;

  /** @var string */
  public $description;

  /** @var string */
  public $feed_instance_id;

  /** @var string */
  public $kind;

  /** @var Video[] */
  public $playlist;


  /**
   * MediaBody constructor.
   * @param $data
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct($data)
  {
    $this->title = field($data, 'title', T\string());
    $this->description = field($data, 'description', T\string());
    $this->kind = field($data, 'kind', T\string());
    $this->feed_instance_id = field
      ($data, 'feed_instance_id', T\string());
    $this->playlist = field
      ($data, 'playlist', T\array_of(Video::decoder()));
  }
}
