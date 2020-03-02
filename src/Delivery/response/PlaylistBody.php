<?php declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\optional_field;

class PlaylistBody
{

  /**
   * Decode the playlists response body into an instance of this class.
   *
   * @param object|array $json
   *   The API response JSON body as decoded by `json_decode()`.
   *
   * @return static
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public static function fromJSON ($json) : self
  {
    $video = function($v){ return Video::fromJSON($v); };
    $links = function($v){ return Links::fromJSON($v); };

    return new self
      ( field($json, 'feedid', T\string())
      , field($json, 'title', T\string())
      , field($json, 'description', T\string())
      , field($json, 'kind', T\string())
      , field($json, 'feed_instance_id', T\string())
      , field($json, 'playlist', T\array_of($video))
      , optional_field($json, 'links', $links) );
  }

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
   *
   * @param string $feedid
   * @param string $title
   * @param string $description
   * @param string $kind
   * @param string $feed_instance_id
   * @param Video[] $playlist
   * @param Links $links
   */
  public function __construct
    ( string $feedid
    , string $title
    , string $description
    , string $kind
    , string $feed_instance_id
    , array $playlist
    , ?Links $links )
  {

    $this->feedid = $feedid;
    $this->title = $title;
    $this->description = $description;
    $this->kind = $kind;
    $this->feed_instance_id = $feed_instance_id;
    $this->playlist = $playlist;
    $this->links = $links;
  }
}


class Links
{
  static public function fromJSON ($json) : self
  {
    return new self
      ( field($json, 'last', T\string())
      , field($json, 'first', T\string())
      , optional_field($json, 'next', T\string())
      , optional_field($json, 'previous', T\string()) );
  }

  /** @var string|null */
  public $previous;

  /** @var string|null */
  public $next;

  /** @var string */
  public $last;

  /** @var string */
  public $first;

  public function __construct
    (string $last, string $first, ?string $next, ?string $previous)
  {
    $this->last = $last;
    $this->first = $first;
    $this->next = $next;
    $this->previous = $previous;
  }
}

