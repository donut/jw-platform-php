<?php declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\optional_field;


class PlaylistBody extends MediaBody
{
  /** @var string */
  public $feedid;

  /** @var Links|null */
  public $links;


  /**
   * PlaylistsBody constructor.
   */
  public function __construct ($data)
  {
    parent::__construct($data);

    $this->feedid = field($data, 'feedid', T\string());
    $this->links = otional_field($data, 'links', Links::decoder());
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

