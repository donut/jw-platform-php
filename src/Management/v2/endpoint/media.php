<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\endpoint\media;


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
use RightThisMinute\JWPlatform\Management\v2\response;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function Functional\reject;


/**
 * List/search media
 *
 * @param Client $client
 *   The v2 Management API client.
 * @param int|null $page
 *   Sets the page number for pagination. First page is 1.
 * @param int|null $page_length
 *   Sets the page length (number of items you get in the response) for
 *   pagination.
 * @param string|null $q
 *   Allows for filtering results. @see https://developer.jwplayer.com/jwplayer/reference/building-a-request#query-parameter-q
 * @param string|null $sort
 *   Allows for sorting results, e.g. sort=created:asc
 *
 * @return response\MediaList
 *
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws DefaultSiteIDNotSet
 * @throws MethodNotAllowedResponse
 * @throws NotFoundResponse
 * @throws PreconditionFailedResponse
 * @throws TooManyRequestsResponseWithRetry
 * @throws UnknownErrorResponse
 * @throws InvalidResponseJSON
 * @throws DecodeError
 */
function list_
  ( Client $client
  , ?int $page=1
  , ?int $page_length=10
  , ?string $q=null
  , ?string $sort=null )
  : response\MediaList
{
  $query =
    [ 'page' => $page ?? null
    , 'page_length' => $page_length ?? null
    , 'q' => $q ?? null
    , 'sort' => $sort ?? null ];
  
  $query = reject($query, function($value){ return $value === null; });
  
  try {
    /** @var response\SuccessWithBody $response */
    $response = $client->get(true, 'media', $query);
  }
  catch (TooManyRequestsResponse $exn) {
    throw $exn->addRetry(__FUNCTION__, func_get_args());
  }
  
  return new response\MediaList($response);
}
