<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\videos;


use RightThisMinute\JWPlatform\exception\UnexpectedResponseBody;
use RightThisMinute\JWPlatform\Management\Client;
use RightThisMinute\JWPlatform\Management\response\NotFoundBody;
use RightThisMinute\JWPlatform\Management\response\SuccessBody;
use RightThisMinute\JWPlatform\Management\response\SuccessJSONBody;
use function Functional\map;

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

  return new VideosShowBody($response_body->json);
}


/**
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 * @param array $values =
 *  [ 'title' => 'The best video ever!!!'
 *  , 'tags' => ['Exciting', 'Gross', 'From App']
 *  , 'description' => 'Okay, so this is the best video every and ...'
 *  , 'author' => 'Jayne Dough'
 *  , 'date' => 1584482447
 *  , 'expires_date' => 1584828075
 *  , 'duration' => 132.43 // In seconds.
 *  , 'trim_in_point' => '03:02:45.106'
 *  , 'trim_out_point' => '03:42:05.032'
 *  , 'link' => 'https://isitchristmas.com'
 *  , 'protectionrule_key' => '????'
 *  , 'sourceurl' => 'https://www.youtube.com/watch?v=N_RRBGKrrUA'
 *  , 'sourceformat' =>
 *      ['aac', 'flv', 'm3u8', 'mp3', 'mp4', 'smil', 'vorbis', 'webm'][$i]
 *  ' update_file' => true
 *  , 'download_url' => 'https://example.com/videos/the_best.mp4'
 *  , 'md5' => '65a8e27d8879283831b664bd8b7f0ad4'
 *  , 'custom' => []
 *  , 'size' => '254803968'
 *  , 'upload_method' => ['single', 'multipart', 's3'][$i]
 *  , 'upload_content_type' => 'video/mp4' ]
 *
 * @return \RightThisMinute\JWPlatform\Management\response\SuccessBody|null
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponseBody
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function update (Client $client, string $video_key, array $values)
  : ?SuccessBody
{
  if (isset($values['tags']) and is_array($values['tags'])) {
    $tags = map($values['tags'], function($t){ return trim($t); });
    $tags = implode(',', $tags);
    $values['tags'] = $tags;
  }

  if (isset($values['custom'])) {
    $custom = $values['custom'];
    unset($values['custom']);
    foreach ($custom as $key => $value)
      $values["custom.$key"] = $value;
  }

  $values['video_key'] = $video_key;

  $endpoint = '/videos/update';

  $response_body = $client->post($endpoint, [], $values);

  if ($response_body instanceof NotFoundBody)
    return null;

  if ($response_body instanceof SuccessBody)
    return $response_body;

  throw new UnexpectedResponseBody($endpoint, $response_body);
}
