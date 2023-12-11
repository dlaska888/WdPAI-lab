<?php

namespace src\Controllers;

use src\Enums\HttpStatusCode;

class AppController
{
    private array|null $requestBody;
    
    public function __construct()
    {
        $this->requestBody = json_decode(file_get_contents('php://input'), true);
    }

    protected function getRequestBody(): array|null
    {
        return $this->requestBody;
    }

    protected function validateRequestData(?array $data, string $validatorClass) : void
    {
        if($data === null)
            $this->response(HttpStatusCode::BAD_REQUEST, 'Request body cannot be null');

        $validationResult = (new $validatorClass($data))->validate();
        if (!$validationResult['success']) {
            $this->response(HttpStatusCode::BAD_REQUEST, $validationResult);
        }
    }
    
    protected function render(string $template = null, array $variables = []): void
    {
        $templatePath = 'src/views/' . $template . '.php';
        $output = 'File not found';

        if (file_exists($templatePath)) {
            extract($variables);

            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }

        echo $output;
        exit();
    }

    protected function response(HttpStatusCode $code, mixed $data = null): void
    {
        header('Content-type: application/json');
        http_response_code($code->value);

        if ($data)
            echo json_encode($data);

        exit();
    }

    protected function redirect($url): void
    {
        header('Location: ' . $url);
        exit();
    }

    
}
