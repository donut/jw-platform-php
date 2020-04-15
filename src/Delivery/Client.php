<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery;


use RightThisMinute\JWPlatform\Delivery\response\PlaylistBody;
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


  /**
   * @param string $id
   * @param \Psr\Http\Message\ResponseInterface|null &$response
   *
   * @return \RightThisMinute\JWPlatform\Delivery\response\PlaylistBody|null
   *
   * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function getPlaylist (string $id, ?ResponseInterface &$response=null)
    : ?PlaylistBody
  {
    $path = "playlists/$id";
    return $this->getPlaylist_byURI($path, $response);
  }


  /**
   * Make a request to $uri and assume the response will be the same as
   * /v2/playlists/{playlist ID}.
   *
   * @param string $uri
   * @param \Psr\Http\Message\ResponseInterface|null $response
   *
   * @return \RightThisMinute\JWPlatform\Delivery\response\PlaylistBody|null
   * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   * @throws \JsonException
   */
  public function getPlaylist_byURI (string $uri, ?ResponseInterface &$response)
    : ?PlaylistBody
  {
    $response = $this->guzzle->get($uri);

    if ($response->getStatusCode() === 404)
      return null;

    if ($response->getStatusCode() !== 200)
      throw new UnexpectedResponse("GET $uri", $response);

    $body = json_decode
      ( $response->getBody()->getContents()
      , false
      , 512 #default
      , JSON_THROW_ON_ERROR );

    return PlaylistBody::fromJSON($body);
  }


  /**
   * Returns all pages of results of a request to /v2/playlists/{playlist ID}.
   *
   * @param string $id
   *   The playlist ID (sometimes called 'media ID' or 'feedid' or 'key' by JW.
   *
   * @return array [
   *    ['playlist' => PlaylistBody, 'response' => ResponseInterface]
   * ]
   *
   * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   * @throws \JsonException
   */
  public function getPlaylist_allPages (string $id) : array
  {
    $pages = [];

    $page = [];
    $playlist = $this->getPlaylist($id, $page['response']);

    do {
      if (!isset($playlist)) {
        if (!isset($next_page_uri))
          return $pages;

        # JW shouldn't return a 404 error for the next page of results if it
        # gave us the URL for that page. Either the playlist was removed or
        # something else wonky is going on. We want to error out since this
        # may not be the complete list of results.
        throw new UnexpectedResponse
          ("GET $next_page_uri", $page['response']);
      }

      $page['playlist'] = $playlist;
      $pages[] = $page;

      if (!isset($playlist->links->next))
        return $pages;

      # Need to redefine this structure every time to avoid all pages having
      # the same response value since it is passed by reference when making
      # the request..
      $page = [];
      $next_page_uri = $playlist->links->next;
      $playlist = $this->getPlaylist_byURI
        ($next_page_uri, $page['response']);
    } while (true);
  }
}
