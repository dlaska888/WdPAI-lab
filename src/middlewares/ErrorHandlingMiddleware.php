<?php

namespace src\middlewares;

use PDOException;
use src\routing\middleware\BaseMiddleware;
use src\routing\Request;
use src\routing\responses\Response;
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
            die("Error" . $e->getMessage());
        }
    }
}