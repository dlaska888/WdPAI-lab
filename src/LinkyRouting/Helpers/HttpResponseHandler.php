<?php

namespace src\LinkyRouting\Helpers;

use src\LinkyRouting\Attributes\Controller\ApiController;
use src\LinkyRouting\Attributes\Controller\MvcController;
use src\LinkyRouting\Enums\HttpStatusCode;
use src\LinkyRouting\Responses\BinaryFile;
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
            BinaryFile::class => $this->binaryFile($response),
            Error::class => $this->error($response),
            default => $this->error(
                new Error(null,
                    "Invalid controller return type",
                    HttpStatusCode::INTERNAL_SERVER_ERROR)
            )
        };
    }

    private function view(View $view): void
    {
        $templatePath = $this->viewsPath . '/' . $view->getTemplate() . '.php';
        $output = 'View not found';

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

        echo json_encode($jsonResponse);

        exit();
    }


    private function redirect(Redirect $redirect): void
    {
        header('Location: ' . $redirect->getUrl());
        http_response_code($redirect->getCode()->value);

        exit();
    }

    private function binaryFile(BinaryFile $response): void // Add this method
    {
        $filePath = $response->getFilePath();

        header('Content-Type: ' . mime_content_type($filePath));
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');

        readfile($filePath);
        exit();
    }

    private function error(Error $response): void
    {
        $code = empty($response->getCode()) ? HttpStatusCode::INTERNAL_SERVER_ERROR : $response->getCode();
        $message = $response->getMessage();
        $data = $response->getData();
        $controllerType = $response->getRequest() ?
            $response->getRequest()->getRoute()->getControllerType() :
            MvcController::class;

        match ($controllerType) {
            ApiController::class => $this->json(new Json($data, $code, $message)),
            default => $this->view(
                new View(
                    $response->getTemplate(),
                    ['code' => $code, 'message' => $message, 'data' => $data ?? [$message]],
                    $code)
            )
        };
    }
}