<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\playlists;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Delivery\Client;
use RightThisMinute\JWPlatform\Delivery\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Delivery\response\PlaylistBody;
use RightThisMinute\JWPlatform\exception\UnexpectedResponse;


/**
 * Get the specified playlist from JW.
 *
 * @param string $playlist_id
 *   An 8 character code (ex: WXu7kuaW) that uniquely identifies the playlist.
 *   You can find it in the playlist details page in the dashboard OR in the
 *   JW Platform Management API in /channels.
 * @param array $params =
 *   [ 'related_media_id' => 'WXu7kuaW'
 *   , 'search' => 'keywords go here "and phrases"'
 *   , 'poster_width' => [40, 120, 320, 480, 720, 1280, 1920][$any]
 *   , 'sources' => ['dash', 'hls', '1920p', '1080p', '720p', '<template ID>']
 *   , 'default_source_fallback' => null ?? true ?? false
 *   , 'recommendations_playlist_id' => '1234abcd'
 *   , 'page_limit' => 100
 *   , 'page_offset' => 100
 *   , 'recency' => '2D'
 *   , 'backfill' => false
 *   , 'tags_mode' => ['ALL', 'ANY'][$any]
 *   , 'tags' => 'Submitted, Wholesome'
 *   , 'exclude_tags_mode' => ['ALL', 'ANY'][$any]
 *   , 'exclude_tags' => 'Gruesome, Fight'
 *   , 'token' => '<PROTECTED CONTENT ACCESS TOKEN>' ]
 * @param \Psr\Http\Message\ResponseInterface|null $response
 *   Pass a variable here to get access to the response object.
 *
 * @return \RightThisMinute\JWPlatform\Delivery\response\PlaylistBody|null
 *   null if the playlist was not found at JW.
 *
 * @throws \RightThisMinute\JWPlatform\Delivery\exception\BadRequestResponse
 * @throws \RightThisMinute\JWPlatform\Delivery\exception\ErrorResponse
 * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function get
  (string $playlist_id, array $params=[], ?ResponseInterface &$response=null)
  : ?PlaylistBody
{
  $params['format'] = 'json';
  $endpoint = "playlists/$playlist_id";

  try {
    $body = Client::singleton()->get($endpoint, $params, $response);
  }
  catch (NotFoundResponse $_) {
    return null;
  }

  return PlaylistBody::fromJSON($body);
}


/**
 * Get all results of the specified playlist from JW (if 'page_offset' is set,
 * the items before that offset will not be included).
 *
 * @param string $playlist_id
 *   An 8 character code (ex: WXu7kuaW) that uniquely identifies the playlist.
 *   You can find it in the playlist details page in the dashboard OR in the
 *   JW Platform Management API in /channels.
 * @param array $params =
 *   [ 'related_media_id' => 'WXu7kuaW'
 *   , 'search' => 'keywords go here "and phrases"'
 *   , 'poster_width' => [40, 120, 320, 480, 720, 1280, 1920][$any]
 *   , 'sources' => ['dash', 'hls', '1920p', '1080p', '720p', '<template ID>']
 *   , 'default_source_fallback' => null ?? true ?? false
 *   , 'recommendations_playlist_id' => '1234abcd'
 *   , 'page_limit' => 100
 *   , 'page_offset' => 100
 *   , 'recency' => '2D'
 *   , 'backfill' => false
 *   , 'tags_mode' => ['ALL', 'ANY'][$any]
 *   , 'tags' => 'Submitted, Wholesome'
 *   , 'exclude_tags_mode' => ['ALL', 'ANY'][$any]
 *   , 'exclude_tags' => 'Gruesome, Fight'
 *   , 'token' => '<PROTECTED CONTENT ACCESS TOKEN>' ]
 *
 * @return array|null
 *   When found, an array of ordered associative arrays, each representing
 *   a page of results, with this structure:
 *       [ 'playlist' : PlaylistBody
 *       , 'response' : ResponseInterface ]
 *   Returns null if the playlist is not found at JW.
 *
 * @throws \RightThisMinute\JWPlatform\Delivery\exception\BadRequestResponse
 * @throws \RightThisMinute\JWPlatform\Delivery\exception\ErrorResponse
 * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function get_all_pages (string $playlist_id, array $params=[]) : ?array
{
  $pages = [];

  $page = ['response' => null];
  $playlist = get($playlist_id, $params, $page['response']);

  do {
    if (!isset($playlist))
      # Playlist was not found at JW.
      return null;

    $page['playlist'] = $playlist;
    $pages[] = $page;

    if (!isset($playlist->links->next))
      return $pages;

    # Need to redefine this structure every time to avoid all pages having
    # the same response value since it is passed by reference when making
    # the request.
    $page = ['response' => null];
    $next_page_uri = $playlist->links->next;
    $next_query = parse_url($next_page_uri, PHP_URL_QUERY);
    parse_str($next_query, $next_params);

    if (empty($next_params))
      throw new UnexpectedResponse
        ( 'GET', $next_page_uri
        , 'Playlist next page URI has as an empty query string.'
        , $page['response'] );

    $playlist = get($playlist_id, $next_params, $page['response']);
  } while (true);
}

