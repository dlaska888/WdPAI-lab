<?php

namespace src\middlewares;

use PDOException;
use src\LinkyRouting\middleware\BaseMiddleware;
use src\LinkyRouting\Request;
use src\LinkyRouting\Responses\Response;
use Throwable;

class ErrorHandlingMiddleware extends BaseMiddleware
{
    //TODO refactor
    public function invoke(Request $request): Response
    {
        try {
            return parent::invoke($request);
        } catch (PDOException $e) {
            die("PDO exception:" . $e->getMessage());
        } catch (Throwable $e) {
            die("Error " . $e->getMessage());
        }
    }
}