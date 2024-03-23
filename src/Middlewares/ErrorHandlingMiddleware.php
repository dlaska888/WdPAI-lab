<?php

namespace LinkyApp\Middlewares;

use LinkyApp\Exceptions\BadRequestException;
use LinkyApp\Exceptions\ForbiddenException;
use LinkyApp\Exceptions\NotFoundException;
use LinkyApp\Exceptions\UnauthorizedException;
use LinkyApp\Exceptions\ValidationException;
use LinkyApp\LinkyRouting\Enums\HttpStatusCode;
use LinkyApp\LinkyRouting\Middleware\BaseMiddleware;
use LinkyApp\LinkyRouting\Request;
use LinkyApp\LinkyRouting\Responses\Error;
use LinkyApp\LinkyRouting\Responses\Response;
use Throwable;

class ErrorHandlingMiddleware extends BaseMiddleware
{
    public function invoke(Request $request): Response
    {
        try {
            return parent::invoke($request);
        } catch (NotFoundException $e) {
            return new Error(
                $request,
                $e->getMessage(),
                HttpStatusCode::NOT_FOUND,
                null,
                $request->getRoute()->getPath()
            );
        } catch (BadRequestException $e) {
            return new Error(
                $request,
                $e->getMessage(),
                HttpStatusCode::BAD_REQUEST,
                null,
                $request->getRoute()->getPath()
            );

        } catch (UnauthorizedException $e) {
            return new Error(
                $request,
                $e->getMessage(),
                HttpStatusCode::UNAUTHORIZED,
                null,
                $request->getRoute()->getPath()
            );
        } catch (ForbiddenException $e) {
            return new Error(
                $request, 
                $e->getMessage(), 
                HttpStatusCode::FORBIDDEN, 
                null, 
                $request->getRoute()->getPath()
            );
        } catch (ValidationException $e) {
            return new Error(
                $request,
                "Validation error",
                HttpStatusCode::BAD_REQUEST,
                $e->getValidationResult()->getErrors(),
                $request->getRoute()->getPath()
            );
        } catch (Throwable $e) {
            error_log($e);
            return new Error($request, "Something went wrong", HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}