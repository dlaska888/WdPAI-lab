<?php

namespace src\middlewares;

use BadRequestException;
use NotFoundException;
use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\middleware\BaseMiddleware;
use src\LinkyRouting\Request;
use src\LinkyRouting\Responses\Error;
use src\LinkyRouting\Responses\Response;
use Throwable;

class ErrorHandlingMiddleware extends BaseMiddleware
{
    //TODO refactor
    public function invoke(Request $request): Response
    {
        try {
            return parent::invoke($request);
        } catch (NotFoundException $e) {
            return new Error($request->getRoute()->getControllerType(), $e->getMessage(), 'error', HttpStatusCode::NOT_FOUND);
        } catch (BadRequestException $e) {
            return new Error($request->getRoute()->getControllerType(), $e->getMessage(), 'error', HttpStatusCode::BAD_REQUEST);
        } catch (Throwable $e) {
            return new Error($request->getRoute()->getControllerType(), $e->getMessage(), 'error', 
                HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}