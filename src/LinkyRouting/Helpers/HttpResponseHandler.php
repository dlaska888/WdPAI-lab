<?php

namespace src\LinkyRouting\helpers;

use src\LinkyRouting\attributes\controller\ApiController;
use src\LinkyRouting\attributes\controller\MvcController;
use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\Responses\Error;
use src\LinkyRouting\Responses\Json;
use src\LinkyRouting\Responses\Redirect;
use src\LinkyRouting\Responses\Response;
use src\LinkyRouting\Responses\View;

class HttpResponseHandler
{
    private string $viewsPath;

    public function __construct(string $templatesPath)
    {
        $this->viewsPath = $templatesPath;
    }

    public function handleResponse(Response $response): void
    {
        match (get_class($response)) {
            View::class => $this->view($response),
            Json::class => $this->json($response),
            Redirect::class => $this->redirect($response),
            Error::class => $this->error($response),
            default => $this->error(new Error(MvcController::class, "Invalid controller return type",
                "error", HttpStatusCode::INTERNAL_SERVER_ERROR))
        };
    }

    private function view(View $view): void
    {
        $templatePath = $this->viewsPath . '/' . $view->getTemplate() . '.php';
        $output = 'View not found';
        $variables = $view->getVariables();
        extract($view->getVariables());
        ob_start();

        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            include __DIR__ . "/../BasicViews/error.php";
        }

        $output = ob_get_clean();

        http_response_code($view->getCode()->value);
        echo $output;
        exit();
    }

    private function json(Json $jsonResponse): void
    {
        header('Content-type: application/json');
        http_response_code($jsonResponse->getCode()->value);

        if ($jsonResponse->getData()) {
            echo json_encode($jsonResponse->getData());
        }

        exit();
    }

    private function redirect(Redirect $redirect): void
    {
        header('Location: ' . $redirect->getUrl());
        http_response_code($redirect->getCode()->value);

        exit();
    }

    private function error(Error $response): void
    {
        $code = empty($response->getCode()) ? HttpStatusCode::INTERNAL_SERVER_ERROR : $response->getCode();
        $message = empty($response->getData()) ? "Something went wrong" : $response->getData();

        match ($response->getControllerType()) {
            ApiController::class => $this->json(new Json($message, $code)),
            default => $this->view(new View($response->getTemplate(), ['code' => $code, 'description' => $message], $response->getCode()))
        };
    }
}