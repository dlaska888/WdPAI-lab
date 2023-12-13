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

    protected function getValidationResult(?array $data, string $validatorClass): array
    {
        if ($data === null) {
            return ['success' => false, 'errors' => 'Invalid request data'];
        }

        return (new $validatorClass($data))->validate();
    }

    public function render(string $template = null, array $variables = []): void
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

    public function response(HttpStatusCode $code, mixed $data = null): void
    {
        header('Content-type: application/json');
        http_response_code($code->value);

        if ($data) {
            echo json_encode($data);
        }

        exit();
    }

    protected function validationResponse(?array $data, string $validatorClass): void
    {
        $result = $this->getValidationResult($data, $validatorClass);
        
        if(!$result['success']){
            $this->response(HttpStatusCode::BAD_REQUEST, $result);
        }
    }

    protected function redirect($url): void
    {
        header('Location: ' . $url);
        exit();
    }


}
