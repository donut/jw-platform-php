<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\videos\conversions;


use RightThisMinute\JWPlatform\Management\Client;
use RightThisMinute\JWPlatform\Management\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Management\response\SuccessBody;
use RightThisMinute\JWPlatform\Management\response\VideosConversionsCreateBody;
use RightThisMinute\JWPlatform\Management\response\VideosConversionsListBody;


/**
 * Create a new video conversion.
 *
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 *   Key of the video for which conversion should be created.
 * @param string $template_key
 *   Key of the conversion template that should be used for this conversion.
 *   This can be found by calling /accounts/templates/list.
 *
 * @return \RightThisMinute\JWPlatform\Management\response\VideosConversionsCreateBody|null
 *   The response object on success, null if $video_key or $template_key don't
 *   exist at JW.
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
function create (Client $client, string $video_key, string $template_key)
  : ?VideosConversionsCreateBody
{
  $endpoint = '/videos/conversions/create';
  $form_data = ['video_key' => $video_key, 'template_key' => $template_key];

  try {
    $response_body = $client->post($endpoint, [], $form_data);
  }
  catch (NotFoundResponse $_) {
    return null;
  }

  return new VideosConversionsCreateBody($response_body->json);
}


/**
 * Delete a video conversion.
 *
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $conversion_key
 *   Key of the conversion that should be delete.
 *
 * @return \RightThisMinute\JWPlatform\Management\response\SuccessBody|null
 *  Null if $conversion_key doesn't exist at JW.
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
function delete (Client $client, string $conversion_key) : ?SuccessBody
{
  $endpoint = '/videos/conversions/delete';
  $form_data = ['conversion_key' => $conversion_key];

  try {
    $response_body = $client->post($endpoint, [], $form_data);
  }
  catch (NotFoundResponse $_) {
    return null;
  }

  return $response_body;
}


/**
 * List of conversions for the specified video.
 *
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 *   Key of the video which conversions to list.
 *
 * @return VideosConversionsListBody
 *
 * @throws \RightThisMinute\JWPlatform\Management\exception\BadRequestResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\ConflictResponse
 * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
 * @throws \RightThisMinute\JWPlatform\Management\exception\MethodNotAllowedResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\NotFoundResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\TooManyRequestsResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\UnknownErrorResponse
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function list_ (Client $client, string $video_key) : VideosConversionsListBody
{
  $endpoint = '/videos/conversions/list';
  $query = ['video_key' => $video_key];

  $response_body = $client->get($endpoint, $query);

  return new VideosConversionsListBody($response_body->json);
}
