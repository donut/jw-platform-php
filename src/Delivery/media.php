<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\media;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Delivery\Client;
use RightThisMinute\JWPlatform\Delivery\exception\BadRequestResponse;
use RightThisMinute\JWPlatform\Delivery\exception\ErrorResponse;
use RightThisMinute\JWPlatform\Delivery\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Delivery\response\MediaBody;
use RightThisMinute\JWPlatform\exception\InvalidResponseJSON;
use RightThisMinute\JWPlatform\exception\UnexpectedResponse;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;


/**
 * Get the specified media from JW.
 *
 * @param string $media_id
 *   An 8 character code (ex: WXu7kuaW) that uniquely identifies the media.
 *   You can find it in the video details page in the dashboard.
 * @param array $params =
 *   [ 'poster_width' => [40, 120, 320, 480, 720, 1280, 1920][$any]
 *   , 'recommendations_playlist_id' => '1234abcd'
 *   , 'sources' => ['dash', 'hls', '720p', '1080p', '1920p']
 *   , 'default_source_fallback' => false
 *   , 'token' => '<PROTECTED CONTENT ACCESS TOKEN>' ]
 * @param ResponseInterface|null $response
 *   Pass a variable here to get access to the response object.
 *
 * @return MediaBody|null
 *   null if the media was not found at JW.
 *
 * @throws BadRequestResponse
 * @throws ErrorResponse
 * @throws InvalidResponseJSON
 * @throws UnexpectedResponse
 * @throws DecodeError
 */
function get
  (string $media_id, array $params=[], ?ResponseInterface &$response=null)
  : ?MediaBody
{
  $params['format'] = 'json';
  $endpoint = "media/$media_id";

  try {
    $body = Client::singleton()->get($endpoint, $params, $response);
  }
  catch (NotFoundResponse $_) {
    return null;
  }

  return new MediaBody($body);
}
