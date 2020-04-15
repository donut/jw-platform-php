<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\videos;


use RightThisMinute\JWPlatform\Management\Client;
use RightThisMinute\JWPlatform\Management\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Management\response\SuccessBody;
use RightThisMinute\JWPlatform\Management\response\VideosShowBody;
use function Functional\map;
use function Functional\reduce_left;
use function Functional\reject;

/**
 * Show the properties of a video.
 *
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 *   Key of the video which information to show.
 *
 * @return \RightThisMinute\JWPlatform\Management\response\VideosShowBody|null
 *   Null if the video is not found at JW.
 *
 * @throws \RightThisMinute\JWPlatform\Management\exception\BadRequestResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\ConflictResponse
 * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
 * @throws \RightThisMinute\JWPlatform\Management\exception\MethodNotAllowedResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\TooManyRequestsResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\UnknownErrorResponse
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function show (Client $client, string $video_key) : ?VideosShowBody
{
  $endpoint = '/videos/show';
  $query = ['video_key' => $video_key];

  try {
    $response_body = $client->get($endpoint, $query);
  }
  catch (NotFoundResponse $_) {
    return null;
  }

  return new VideosShowBody($response_body->json);
}


/**
 * Update the properties of a video.
 *
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 *   Key of the video which information to update.
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
 *  , 'update_file' => true
 *  , 'download_url' => 'https://example.com/videos/the_best.mp4'
 *  , 'md5' => '65a8e27d8879283831b664bd8b7f0ad4'
 *  , 'custom' => []
 *  , 'size' => '254803968'
 *  , 'upload_method' => ['single', 'multipart', 's3'][$i]
 *  , 'upload_content_type' => 'video/mp4' ]
 *
 * @return \RightThisMinute\JWPlatform\Management\response\SuccessBody|null
 *  null if video is not found at JW.
 *
 * @throws \RightThisMinute\JWPlatform\Management\exception\BadRequestResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\ConflictResponse
 * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
 * @throws \RightThisMinute\JWPlatform\Management\exception\MethodNotAllowedResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\TooManyRequestsResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\UnknownErrorResponse
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
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

  try {
    return $client->post($endpoint, [], $values);
  }
  catch (NotFoundResponse $_) {
    return null;
  }
}


/**
 * Add the passed tags to a video.
 *
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 * @param string[] $tags
 *
 * @return string[]|null
 *   The final list of tags. NULL if no video is found with $video_key.
 *
 * @throws \RightThisMinute\JWPlatform\Management\exception\BadRequestResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\ConflictResponse
 * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
 * @throws \RightThisMinute\JWPlatform\Management\exception\MethodNotAllowedResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\TooManyRequestsResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\UnknownErrorResponse
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function add_tags (Client $client, string $video_key, array $tags) : ?array
{
  $video = show($client, $video_key);
  if (!isset($video))
    return null;

  $existing = $video->video->tags;
  $updated = reduce_left($tags, function($tag, $_, $__, $acc){
    if (in_array($tag, $acc))
      return $acc;

    $acc[] = $tag;
    return $acc;
  }, $existing);

  if (count($updated) === count($existing))
    return $existing;

  $response = update($client, $video_key, ['tags' => $updated]);
  return isset($response) ? $updated : null;
}


/**
 * Remove the passed tags from a video.
 *
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 * @param string[] $tags
 *
 * @return string[]|null
 *   The final list of tags. NULL if the video does not exist.
 *
 * @throws \RightThisMinute\JWPlatform\Management\exception\BadRequestResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\ConflictResponse
 * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
 * @throws \RightThisMinute\JWPlatform\Management\exception\MethodNotAllowedResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\TooManyRequestsResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\UnknownErrorResponse
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function remove_tags (Client $client, string $video_key, array $tags) : ?array
{
  $video = show($client, $video_key);
  if (!isset($video))
    return null;

  $existing = $video->video->tags;
  $updated = reject($existing, function ($tag) use ($tags) {
    return in_array($tag, $tags);
  });

  if (count($updated) === count($existing))
    return $existing;

  $response = update($client, $video_key, ['tags' => $updated]);
  return isset($response) ? $updated : null;
}
