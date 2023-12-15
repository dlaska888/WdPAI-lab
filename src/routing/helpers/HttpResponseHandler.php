<?php

namespace src\routing\helpers;

use src\routing\responses\Json;
use src\routing\responses\View;

class HttpResponseHandler
{
    private string $viewsPath;
    
    public function __construct(string $templatesPath)
    {
        $this->viewsPath = $templatesPath;
    }

    public function view(View $view):
    void
    {
        $templatePath = $this->viewsPath . '/' . $view->getTemplate() . '.php';
        $output = 'View not found';

        if (file_exists($templatePath)) {
            extract($view->getVariables());

            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }

        http_response_code($view->getCode()->value);
        echo $output;
        exit();
    }

    public function json(Json $jsonResponse): void
    {
        header('Content-type: application/json');
        http_response_code($jsonResponse->getCode()->value);

        if ($jsonResponse->getData()) {
            echo json_encode($jsonResponse->getData());
        }

        exit();
    }
}