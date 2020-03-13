<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\videos;


use RightThisMinute\JWPlatform\exception\UnexpectedResponseBody;
use RightThisMinute\JWPlatform\Management\Client;
use RightThisMinute\JWPlatform\Management\response\NotFoundBody;
use RightThisMinute\JWPlatform\Management\response\SuccessJSONBody;

/**
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 *
 * @return \RightThisMinute\JWPlatform\Management\videos\VideosShowBody|null
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponseBody
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function show (Client $client, string $video_key) : ?VideosShowBody
{
  $endpoint = '/videos/show';
  $query = ['video_key' => $video_key];

  $response_body = $client->get($endpoint, $query);

  if ($response_body instanceof NotFoundBody)
    return null;

  if (!($response_body instanceof SuccessJSONBody))
    throw new UnexpectedResponseBody($endpoint, $response_body);

  return VideosShowBody::fromJSON($response_body->json);
}
