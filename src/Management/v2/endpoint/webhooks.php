<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\endpoint\webhooks;


use Firebase\JWT\JWT;
use JsonException;
use RightThisMinute\JWPlatform\exception\InvalidResponseJSON;
use RightThisMinute\JWPlatform\Management\v2\Client;
use RightThisMinute\JWPlatform\Management\v2\exception\BadRequestResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\ConflictResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\DefaultSiteIDNotSet;
use RightThisMinute\JWPlatform\Management\v2\exception\MethodNotAllowedResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\PreconditionFailedResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\TooManyRequestsResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\TooManyRequestsResponseWithRetry;
use RightThisMinute\JWPlatform\Management\v2\exception\UnknownErrorResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\WebhooksRequestAuthError;
use RightThisMinute\JWPlatform\Management\v2\exception\WebhooksRequestInvalidJSON;
use RightThisMinute\JWPlatform\Management\v2\response;
use RightThisMinute\JWPlatform\Management\v2\request;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


/**
 * List all webhooks on the account (not just to a specific property).
 *
 * @see https://developer.jwplayer.com/jwplayer/reference#get_v2-webhooks
 *
 * @param Client $client
 *   The v2 Management API client.
 * @param int|null $page
 *   Sets the page number for pagination. First page is 1.
 * @param int|null $page_length
 *   Sets the page length (number of items you get in the response) for
 *   pagination.
 * @param string|null $sort
 *   Allows for sorting results, e.g. sort=created:asc
 *
 * @return response\WebhooksList
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws DecodeError
 * @throws DefaultSiteIDNotSet
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws NotFoundResponse
 * @throws PreconditionFailedResponse
 * @throws TooManyRequestsResponseWithRetry
 * @throws UnknownErrorResponse
 */
function list_
  (Client $client, ?int $page=1, ?int $page_length=10, ?string $sort=null)
  : response\WebhooksList
{
  $query = [];

  if (isset($page))
    $query['page'] = $page;
  if (isset($page_length))
    $query['page_length'] = $page_length;
  if (isset($sort))
    $query['sort'] = $sort;

  try {
    /** @var response\SuccessWithBody $response */
    $response = $client->get(false, 'webhooks', $query);
  }
  catch (TooManyRequestsResponse $exn) {
    throw $exn->addRetry(__FUNCTION__, func_get_args());
  }

  return new response\WebhooksList($response);
}


/**
 * Create a new webhook resource.
 *
 * The available events and what triggers them:
 *
 * - `media_available`
 *   Once the first conversion is available of a media file and it's playable,
 *   whether for newly uploaded or re-uploaded media.
 *
 * - `conversions_complete`
 *   When all conversions of an uploaded media file are finished and available.
 *   Triggers for both new media and re-uploaded media.
 *
 * - `media_updated`
 *   When any of the metadata changes (title, description, thumbnail,
 *   custom fields, etc). Note that as of writing, JW seems to be sending
 *   this out twice for each actual occurrence. I've reached out to them to
 *   learn more.
 *
 * - `media_reuploaded`
 *   When the source file of an existing media asset is re-uploaded. This is
 *   event is sent out before `media_available` and `conversions_complete`.
 *
 * - `media_deleted`
 *   When a media asset is deleted.
 *
 * @param Client $client
 *   The v2 Management API client.
 * @param string $name
 *   Display name for webhook subscription.
 * @param string[] $events
 *   Subscribed events that trigger a notification.
 * @param string $pub_url
 *   Valid HTTPS endpoint to be notified upon event(s) occurring.
 * @param string|null $description
 *   Description for the webhook subscription.
 * @param string[]|null $site_ids
 *   List of site IDs corresponding to the webhook. If null or empty, this will
 *  default to the default site ID of $client, if any.
 *
 * @return response\WebhooksCreate
 *
 * @throws DefaultSiteIDNotSet
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws MethodNotAllowedResponse
 * @throws NotFoundResponse
 * @throws PreconditionFailedResponse
 * @throws TooManyRequestsResponseWithRetry
 * @throws UnknownErrorResponse
 * @throws InvalidResponseJSON
 * @throws DecodeError
 *
 * @see https://developer.jwplayer.com/jwplayer/docs/learn-about-webhooks#section-create-a-webhook
 * @see https://developer.jwplayer.com/jwplayer/reference#post_v2-webhooks
 *
 */
function create
  ( Client $client
  , string $name
  , array $events
  , string $pub_url
  , ?string $description=null
  , ?array $site_ids=null )
  : response\WebhooksCreate
{
  if (!isset($site_ids) || count($site_ids) === 0) {
    if ($client->defaultSiteID() === null)
      throw new DefaultSiteIDNotSet();
    else
      $site_ids = [$client->defaultSiteID()];
  }

  $body = (object)
    [ 'metadata' => (object)
      [ 'webhook_url' => $pub_url
      , 'events' => $events
      , 'name' => $name
      , 'description' => $description ?? ''
      , 'site_ids' => $site_ids ]];

  try {
    /** @var response\SuccessWithBody $response */
    $response = $client->post(false, 'webhooks', [], $body);
  }
  catch (TooManyRequestsResponse $exn) {
    throw $exn->addRetry(__FUNCTION__, func_get_args());
  }

  return new response\WebhooksCreate($response);
}


/**
 * Delete a webhook resource
 *
 * @see https://developer.jwplayer.com/jwplayer/reference#delete_v2-webhooks-webhook-id-
 *
 * @param Client $client
 *   The v2 Management API client.
 * @param string $id
 *   Unique identifier for a resource
 *
 * @return response\Success
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws DecodeError
 * @throws DefaultSiteIDNotSet
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws NotFoundResponse
 * @throws PreconditionFailedResponse
 * @throws TooManyRequestsResponseWithRetry
 * @throws UnknownErrorResponse
 */
function delete (Client $client, string $id) : response\Success
{
  $path = 'webhooks/' . urlencode($id);

  try {
    return $client->delete(false, $path);
  }
  catch (TooManyRequestsResponse $exn) {
    throw $exn->addRetry(__FUNCTION__, func_get_args());
  }
}


/**
 * Get the webhook ID from the webhook request body received from JW. Necessary
 * for clients to look up a stored webhook secret to pass to the validation and
 * parsing functions.
 *
 * @param string $body
 *   The raw, unparsed body of the request JW sent.
 *
 * @return string
 *
 * @throws DecodeError
 * @throws WebhooksRequestInvalidJSON
 */
function webhook_id_of_publish_request_body (string $body) : string
{
  $json = _parse_publish_request_body_to_json($body);
  return field($json, 'webhook_id', T\string());
}


/**
 * Authenticate a request made to a `webhook_url` by JW.
 *
 * @param string $auth_header
 *   The value of the 'Authorization' header in the request.
 * @param string $secret
 *   The secret that JW returned when creating the webhook.
 * @param object $payload
 *   The parsed JSON from the request body.
 *
 * @return bool
 *   `true` if the request is authentic, `false` otherwise.
 * @throws WebhooksRequestAuthError
 *   Thrown if the `Authorization` header is malformed.
 */
function authenticate_publish_request
  (string $auth_header, string $secret, object $payload)
  : bool
{
  # Extract token
  $match_jwt_token = '/^\s*Bearer (\S+)\s*$/i';
  if (preg_match($match_jwt_token, $auth_header, $matches) === false)
    throw new WebhooksRequestAuthError
      ("Failed extracting JWT token from Authorization request header ($auth_header).");

  $jwt_token = $matches[1];

  # Verify authenticity
  $decoded = JWT::decode($jwt_token, $secret, ['HS256']);

  # Convert to array for comparison since PHP only compares objects by
  # reference.
  return ((array) $payload) === ((array) $decoded);
}


/**
 * Authenticate and parse the body of a JW webhook publish event request.
 *
 * @param string $auth_header
 *   The "Authorization" header sent along with the request from JW.
 * @param string $secret
 *   The secret for the webhook as returned by JW when creating the hook.
 * @param string $body
 *   The raw, unparsed body of the request from JW.
 *
 * @return request\WebhooksEventBody
 *
 * @throws DecodeError
 * @throws WebhooksRequestAuthError
 * @throws WebhooksRequestInvalidJSON
 */
function authenticate_and_parse_publish_request
  (string $auth_header, string $secret, string $body)
  : request\WebhooksEventBody
{
  $json = _parse_publish_request_body_to_json($body);

  if (!authenticate_publish_request($auth_header, $secret, $json))
    throw new WebhooksRequestAuthError
      ('Passed body does not match the decoded payload from the authorization header.');

  return new request\WebhooksEventBody($json);
}


/**
 * Parse the passed string into JSON. Only thing specific to JW webhook publish
 * requests is the exception that's thrown if the body is not valid JSON.
 *
 * @param string $body
 *   The raw, unparsed body of the request from JW.
 *
 * @return object
 *   The parsed JSON.
 *
 * @throws WebhooksRequestInvalidJSON
 */
function _parse_publish_request_body_to_json (string $body) : object
{
  try {
    return json_decode
      ( $body
      , false
      , 512 #default
      , JSON_THROW_ON_ERROR );
  }
  catch (JsonException $exn) {
    throw new WebhooksRequestInvalidJSON($body, $exn);
  }
}
