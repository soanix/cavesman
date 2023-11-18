<?php

namespace Cavesman;

use Cavesman\Http\JsonResponse;
use Cavesman\Http\Redirect;
use Cavesman\Http\Response;

class Http
{

    /**
     * @deprecated Use new \Cavesman\Http\JsonRepsonse
     * @param string $message
     * @param int $code
     * @param int $flags
     * @return JsonResponse
     */
    public static function jsonResponse(mixed $message, int $code = 200, int $flags = 0): JsonResponse
    {
        return new JsonResponse($message, $code, $flags);
    }

    /**
     * @deprecated Use new \Cavesman\Http\Repsonse
     * @param string $message
     * @param int $code
     * @param string $contentType
     * @return Response
     */
    public static function response(mixed $message, int $code = 200, string $contentType = 'text/html'): Response
    {
        return new Response($message, $code, $contentType);
    }

    /**
     * @deprecated Use new \Cavesman\Http\Redirect
     * @param string $url
     * @param int $statusCode
     * @return Redirect
     */
    public static function redirect(string $url, int $statusCode = 303): Redirect
    {
        return new Redirect($url, $statusCode);
    }

}
