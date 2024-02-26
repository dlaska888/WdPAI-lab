<?php

namespace src\middlewares;

use src\Exceptions\BadRequestException;
use src\Exceptions\NotFoundException;
use src\Exceptions\ForbiddenException;
use src\Exceptions\UnauthorizedException;
use src\Exceptions\ValidationException;
use src\LinkyRouting\Enums\HttpStatusCode;
use src\LinkyRouting\Middleware\BaseMiddleware;
use src\LinkyRouting\Request;
use src\LinkyRouting\Responses\Error;
use src\LinkyRouting\Responses\Response;
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