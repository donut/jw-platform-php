<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v1\videos;


use RightThisMinute\JWPlatform\exception\InvalidResponseJSON;
use RightThisMinute\JWPlatform\exception\URLTooLong;
use RightThisMinute\JWPlatform\Management\v1\Client;
use RightThisMinute\JWPlatform\Management\v1\exception\BadRequestResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\ConflictResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\MethodNotAllowedResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\PreconditionFailedResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\TooManyRequestsResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\TooManyRequestsResponseWithRetry;
use RightThisMinute\JWPlatform\Management\v1\exception\UnknownErrorResponse;
use RightThisMinute\JWPlatform\Management\v1\response\SuccessBody;
use RightThisMinute\JWPlatform\Management\v1\response\VideosCreateBody;
use RightThisMinute\JWPlatform\Management\v1\response\VideosDeleteBody;
use RightThisMinute\JWPlatform\Management\v1\response\VideosShowBody;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function Functional\map;
use function Functional\reduce_left;
use function Functional\reject;


/**
 * Show the properties of a video.
 *
 * @param Client $client
 * @param string $video_key
 *   Key of the video which information to show.
 *
 * @return VideosShowBody|null
 *   Null if the video is not found at JW.
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws UnknownErrorResponse
 * @throws URLTooLong
 * @throws DecodeError
 * @throws TooManyRequestsResponseWithRetry
 * @throws PreconditionFailedResponse
 */
function show (Client $client, string $video_key) : ?VideosShowBody
{
  $endpoint = '/videos/show';
  $query = ['video_key' => $video_key];

  try {
    $response_body = $client->get($endpoint, $query);
  }
  catch (TooManyRequestsResponse $exn) {
    $args = func_get_args();
    $remake = function()use($args) { return show(...$args); };
    throw $exn->addRetry($remake);
  }
  catch (NotFoundResponse $_) {
    return null;
  }

  return new VideosShowBody($response_body->json);
}


/**
 * Create a video asset at JW.
 *
 * @param Client $client
 * @param array $values =
 *  [ 'title' => 'The best video ever!!!'
 *  , 'tags' => ['Exciting', 'Gross', 'From App']
 *  , 'description' => 'Okay, so this is the best video every and ...'
 *  , 'category' =>
 *      [ 'Automotive', 'Books and Literature', 'Business and Finance'
 *      , 'Careers', 'Education', 'Events and Attractions'
 *      , 'Family and Relationships', 'Fine Art', 'Food & Drink'
 *      , 'Healthy Living', 'Hobbies & Interests', 'Home & Garden'
 *      , 'Medical Health', 'Movies', 'Music and Audio'
 *      , 'News and Politics', 'Personal Finance', 'Pets', 'Pop Culture'
 *      , 'Real Estate', 'Religion & Spirituality', 'Science', 'Shopping'
 *      , 'Sports', 'Style & Fashion', 'Technology & Computing', 'Television'
 *      , 'Travel', 'Video Gaming'][$one]
 *  , 'author' => 'Jayne Dough'
 *  , 'date' => 1584482447
 *  , 'expires_date' => 1584828075
 *  , 'duration' => 132.43 // In seconds.
 *  , 'trim_in_point' => '03:02:45.106'
 *  , 'trim_out_point' => '03:42:05.032'
 *  , 'link' => 'https://isitchristmas.com'
 *  , 'protectionrule_key' => '????'
 *  , 'sourcetype' => ['file', 'url'][$one]
 *  , 'sourceurl' => 'https://www.youtube.com/watch?v=N_RRBGKrrUA'
 *  , 'sourceformat' =>
 *      ['aac', 'flv', 'm3u8', 'mp3', 'mp4', 'smil', 'vorbis', 'webm'][$one]
 *  , 'update_file' => true
 *  , 'download_url' => 'https://example.com/videos/the_best.mp4'
 *  , 'size' => '254803968'
 *  , 'md5' => '65a8e27d8879283831b664bd8b7f0ad4'
 *  , 'custom' => []
 *  , 'upload_method' => ['single', 'multipart', 's3'][$i]
 *  , 'upload_content_type' => 'video/mp4' ]
 *
 * @return VideosCreateBody
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws DecodeError
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws NotFoundResponse
 * @throws TooManyRequestsResponseWithRetry
 * @throws URLTooLong
 * @throws UnknownErrorResponse
 * @throws PreconditionFailedResponse
 */
function create (Client $client, array $values) : VideosCreateBody
{
  $values = _prep_create_update_params($values);

  try {
    $response_body = $client->post('/videos/create', [], $values);
  }
  catch (TooManyRequestsResponse $exn) {
    $args = func_get_args();
    $remake = function()use($args) { return create(...$args); };
    throw $exn->addRetry($remake);
  }

  return new VideosCreateBody($response_body->json);
}


/**
 * Update the properties of a video.
 *
 * @param Client $client
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
 * @return SuccessBody|null
 *  null if video is not found at JW.
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws UnknownErrorResponse
 * @throws URLTooLong
 * @throws DecodeError
 * @throws TooManyRequestsResponseWithRetry
 * @throws PreconditionFailedResponse
 */
function update (Client $client, string $video_key, array $values)
  : ?SuccessBody
{
  $values = _prep_create_update_params($values);
  $values['video_key'] = $video_key;

  try {
    return $client->post('/videos/update', [], $values);
  }
  catch (TooManyRequestsResponse $exn) {
    $args = func_get_args();
    $remake = function()use($args) { return update(...$args); };
    throw $exn->addRetry($remake);
  }
  catch (NotFoundResponse $_) {
    return null;
  }
}


/**
 * [Internal] prepares array of values to be sent to JW for create or update
 * operations.
 *
 * @param array $values =
 *  [ 'title' => 'The best video ever!!!'
 *  , 'tags' => ['Exciting', 'Gross', 'From App']
 *  , 'description' => 'Okay, so this is the best video every and ...'
 *  , 'category' =>
 *      [ 'Automotive', 'Books and Literature', 'Business and Finance'
 *      , 'Careers', 'Education', 'Events and Attractions'
 *      , 'Family and Relationships', 'Fine Art', 'Food & Drink'
 *      , 'Healthy Living', 'Hobbies & Interests', 'Home & Garden'
 *      , 'Medical Health', 'Movies', 'Music and Audio'
 *      , 'News and Politics', 'Personal Finance', 'Pets', 'Pop Culture'
 *      , 'Real Estate', 'Religion & Spirituality', 'Science', 'Shopping'
 *      , 'Sports', 'Style & Fashion', 'Technology & Computing', 'Television'
 *      , 'Travel', 'Video Gaming'][$one]
 *  , 'author' => 'Jayne Dough'
 *  , 'date' => 1584482447
 *  , 'expires_date' => 1584828075
 *  , 'duration' => 132.43 // In seconds.
 *  , 'trim_in_point' => '03:02:45.106'
 *  , 'trim_out_point' => '03:42:05.032'
 *  , 'link' => 'https://isitchristmas.com'
 *  , 'protectionrule_key' => '????'
 *  , 'sourcetype' => ['file', 'url'][$one]
 *  , 'sourceurl' => 'https://www.youtube.com/watch?v=N_RRBGKrrUA'
 *  , 'sourceformat' =>
 *      ['aac', 'flv', 'm3u8', 'mp3', 'mp4', 'smil', 'vorbis', 'webm'][$one]
 *  , 'update_file' => true
 *  , 'download_url' => 'https://example.com/videos/the_best.mp4'
 *  , 'size' => '254803968'
 *  , 'md5' => '65a8e27d8879283831b664bd8b7f0ad4'
 *  , 'custom' => []
 *  , 'upload_method' => ['single', 'multipart', 's3'][$i]
 *  , 'upload_content_type' => 'video/mp4' ]
 *
 * @return array
 */
function _prep_create_update_params (array $values) : array
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

  return $values;
}


/**
 * Delete the videos at JW corresponding the list of passed media IDs.
 *
 * @param Client $client
 * @param string[] $video_keys
 *
 * @return VideosDeleteBody
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws DecodeError
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws URLTooLong
 * @throws UnknownErrorResponse
 * @throws NotFoundResponse
 * @throws TooManyRequestsResponseWithRetry
 * @throws PreconditionFailedResponse
 */
function delete (Client $client, array $video_keys) : VideosDeleteBody
{

  $values = ['video_key' => implode(',', $video_keys)];

  try {
    $response_body = $client->post('/videos/delete', [], $values);
  }
  catch (TooManyRequestsResponse $exn) {
    $args = func_get_args();
    $remake = function()use($args) { return delete(...$args); };
    throw $exn->addRetry($remake);
  }

  return new VideosDeleteBody($response_body->json);
}


/**
 * Add the passed tags to a video.
 *
 * @param Client $client
 * @param string $video_key
 * @param string[] $tags
 *
 * @return string[]|null
 *   The final list of tags. NULL if no video is found with $video_key.
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws UnknownErrorResponse
 * @throws URLTooLong
 * @throws DecodeError
 * @throws TooManyRequestsResponseWithRetry
 * @throws PreconditionFailedResponse
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
 * @param Client $client
 * @param string $video_key
 * @param string[] $tags
 *
 * @return string[]|null
 *   The final list of tags. NULL if the video does not exist.
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws UnknownErrorResponse
 * @throws URLTooLong
 * @throws DecodeError
 * @throws TooManyRequestsResponseWithRetry
 * @throws PreconditionFailedResponse
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
