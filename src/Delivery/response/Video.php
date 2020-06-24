<?php declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\optional_field;


class Video
{
  use DecoderTrait;

  /** @var string */
  public $mediaid;

  /** @var string */
  public $description;

  /** @var int */
  public $pubdate;

  /** @var string */
  public $title;

  /** @var string */
  public $image;

  /** @var string[] */
  public $tags;

  /** @var object */
  public $variations;

  /** @var Image[] */
  public $images;

  /** @var string */
  public $link;

  /** @var int */
  public $duration;

  /** @var Source[] */
  public $sources;

  /** @var string|null */
  public $feedid;

  /**
   * Video constructor.
   * @param $data
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $tags = function($v) {
      $tags = T\string()($v);
      return preg_split
        ('/(\s*,\s*)+/', $tags, PREG_SPLIT_NO_EMPTY);
    };

    $this->mediaid = field($data, 'mediaid', T\string());
    $this->title = field($data, 'title', T\string());
    $this->description = field($data, 'description', T\string());
    $this->duration = field($data, 'duration', T\int());
    $this->pubdate = field($data, 'pubdate', T\int());
    $this->link = field($data, 'link', T\string());
    $this->image = field($data, 'image', T\string());
    $this->images = field($data, 'images', T\array_of(Image::decoder()));
    $this->tags = optional_field($data, 'tags', $tags, []);
    $this->variations = field($data, 'variations', T\object());
    $this->sources = field
      ($data, 'sources', T\array_of(Source::decoder()));
    $this->feedid = optional_field($data, 'feedid', T\string());
  }

}


class Image
{
  use DecoderTrait;

  /** @var string */
  public $src;

  /** @var string */
  public $type;

  /** @var int */
  public $width;

  /**
   * Image constructor.
   * @param $data
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $this->src = field($data, 'src', T\string());
    $this->type = field($data, 'type', T\string());
    $this->width = field($data, 'width', T\int());
  }
}


class Track
{
  /** @var string */
  public $kind;

  /** @var string */
  public $file;

  /**
   * Track constructor.
   * @param string $kind
   * @param string $file
   */
  public function __construct (string $kind, string $file)
  {
    $this->kind = $kind;
    $this->file = $file;
  }
}

class Source
{
  use DecoderTrait;

  /** @var string */
  public $file;

  /** @var string|null */
  public $type;

  /** @var string|null */
  public $label;

  /** @var int|null */
  public $width;

  /** @var int|null */
  public $height;

  /**
   * Source constructor.
   * @param $data
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $this->file = field($data, 'file', T\string());
    $this->type = optional_field($data, 'type', T\string());
    $this->label = optional_field($data, 'label', T\string());
    $this->width = optional_field($data, 'width', T\int());
    $this->height = optional_field($data, 'height', T\int());
  }
}
