<?php

declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery;


use RightThisMinute\JWPlatform\Delivery\response\PlaylistsBody;
use RightThisMinute\JWPlatform\exception\UnexpectedResponse;
use Psr\Http\Message\ResponseInterface;
const BASE_URI = 'https://cdn.jwplayer.com/v2/';


class Client
{

  /**
   * @var \GuzzleHttp\Client
   */
  private $guzzle;


  public function __construct ()
  {
    $this->guzzle = new \GuzzleHttp\Client(
      [ 'base_uri' => BASE_URI
      , 'http_errors' => false ]
    );
  }


  public function getPlaylist (string $id, ?ResponseInterface &$response=null)
    : ?PlaylistsBody
  {
    $path = "playlists/$id";
    $response = $this->guzzle->get($path);

    if ($response->getStatusCode() === 404)
      return null;

    if ($response->getStatusCode() !== 200)
      throw new UnexpectedResponse("[GET $path]", $response);

    $body = json_decode
      ( $response->getBody()->getContents()
      , false
      , 512 #default
      , JSON_THROW_ON_ERROR );

    return PlaylistsBody::fromJSON($body);
  }
}
