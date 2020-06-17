<?php declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\optional_field;


class PlaylistBody
{
  /** @var string */
  public $feedid;

  /** @var string */
  public $title;

  /** @var string */
  public $description;

  /** @var string */
  public $kind;

  /** @var string */
  public $feed_instance_id;

  /** @var Video[] */
  public $playlist;

  /** @var Links|null */
  public $links;


  /**
   * PlaylistsBody constructor.
   */
  public function __construct ($data)
  {
    $this->feedid = field($data, 'feedid', T\string());
    $this->title = field($data, 'title', T\string());
    $this->description = field($data, 'description', T\string());
    $this->kind = field($data, 'kind', T\string());
    $this->feed_instance_id = field($data, 'feed_instance_id', T\string());
    $this->playlist = field($data, 'playlist', T\array_of(Video::decoder()));
    $this->links = optional_field($data, 'links', Links::decoder());
  }
}


class Links
{
  use DecoderTrait;

  /** @var string|null */
  public $previous;

  /** @var string|null */
  public $next;

  /** @var string */
  public $last;

  /** @var string */
  public $first;

  public function __construct ($data)
  {
    $this->last = field($data, 'last', T\string());
    $this->first = field($data, 'first', T\string());
    $this->next = optional_field($data, 'next', T\string());
    $this->previous = optional_field($data, 'previous', T\string());
  }
}

