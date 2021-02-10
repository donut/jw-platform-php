<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v1\videos\conversions;


use RightThisMinute\JWPlatform\exception\InvalidResponseJSON;
use RightThisMinute\JWPlatform\exception\URLTooLong;
use RightThisMinute\JWPlatform\Management\v1\Client;
use RightThisMinute\JWPlatform\Management\v1\exception\BadRequestResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\ConflictResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\MethodNotAllowedResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\PreconditionFailedResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\TooManyRequestsResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\UnknownErrorResponse;
use RightThisMinute\JWPlatform\Management\v1\response\SuccessBody;
use RightThisMinute\JWPlatform\Management\v1\response\VideosConversionsCreateBody;
use RightThisMinute\JWPlatform\Management\v1\response\VideosConversionsListBody;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;


/**
 * Create a new video conversion.
 *
 * @param Client $client
 * @param string $video_key
 *   Key of the video for which conversion should be created.
 * @param string $template_key
 *   Key of the conversion template that should be used for this conversion.
 *   This can be found by calling /accounts/templates/list.
 *
 * @return VideosConversionsCreateBody|null
 *   The response object on success, null if $video_key or $template_key don't
 *   exist at JW.
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws TooManyRequestsResponse
 * @throws UnknownErrorResponse
 * @throws URLTooLong
 * @throws DecodeError
 * @throws PreconditionFailedResponse
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
 * @param Client $client
 * @param string $conversion_key
 *   Key of the conversion that should be delete.
 *
 * @return SuccessBody|null
 *  Null if $conversion_key doesn't exist at JW.
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws TooManyRequestsResponse
 * @throws UnknownErrorResponse
 * @throws URLTooLong
 * @throws DecodeError
 * @throws PreconditionFailedResponse
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
 * @param Client $client
 * @param string $video_key
 *   Key of the video which conversions to list.
 *
 * @return VideosConversionsListBody
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws NotFoundResponse
 * @throws TooManyRequestsResponse
 * @throws UnknownErrorResponse
 * @throws URLTooLong
 * @throws DecodeError
 * @throws PreconditionFailedResponse
 */
function list_ (Client $client, string $video_key) : VideosConversionsListBody
{
  $endpoint = '/videos/conversions/list';
  $query = ['video_key' => $video_key];

  $response_body = $client->get($endpoint, $query);

  return new VideosConversionsListBody($response_body->json);
}
