<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\videos\conversions;


use RightThisMinute\JWPlatform\exception\UnexpectedResponseBody;
use RightThisMinute\JWPlatform\Management\Client;
use RightThisMinute\JWPlatform\Management\response\NotFoundBody;
use RightThisMinute\JWPlatform\Management\response\SuccessJSONBody;
use RightThisMinute\JWPlatform\Management\response\VideosConversionsCreateBody;
use RightThisMinute\JWPlatform\Management\response\VideosConversionsListBody;


/**
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 * @param string $template_key
 *
 * @return \RightThisMinute\JWPlatform\Management\response\VideosConversionsCreateBody|null
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponseBody
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function create (Client $client, string $video_key, string $template_key)
  : ?VideosConversionsCreateBody
{
  $endpoint = '/videos/conversions/create';
  $form_data = ['video_key' => $video_key, 'template_key' => $template_key];

  $response_body = $client->post($endpoint, [], $form_data);

  if ($response_body instanceof NotFoundBody)
    return null;

  if (!($response_body instanceof SuccessJSONBody))
    throw new UnexpectedResponseBody($endpoint, $response_body);

  return new VideosConversionsCreateBody($response_body->json);
}


/**
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 * @param string $video_key
 *
 * @return VideosConversionsListBody
 *
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponseBody
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function list_ (Client $client, string $video_key) : VideosConversionsListBody
{
  $endpoint = '/videos/conversions/list';
  $query = ['video_key' => $video_key];

  $response_body = $client->get($endpoint, $query);

  if (!($response_body instanceof SuccessJSONBody))
    throw new UnexpectedResponseBody($endpoint, $response_body);

  return new VideosConversionsListBody($response_body->json);
}
