<?php

namespace src\Controllers;

use src\Enums\HttpStatusCode;
use JetBrains\PhpStorm\NoReturn;

class AppController
{
    private string $request;
    private array | null $requestBody;

    public function __construct()
    {
        $this->request = $_SERVER['REQUEST_METHOD'];
        $this->requestBody = json_decode(file_get_contents('php://input'), true);
    }

    protected function isGet(): bool
    {
        return $this->request === 'GET';
    }

    protected function isPost(): bool
    {
        return $this->request === 'POST';
    }

    protected function isPut(): bool
    {
        return $this->request === 'PUT';
    }
    
    protected function isDelete() : bool
    {
        return $this->request === 'DELETE';    
    }
    
    protected function getRequestBody() : array | null
    {
        return $this->requestBody;
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

        print $output;
    }
    
    #[NoReturn] protected function response(HttpStatusCode $code, mixed $data = null) : void
    {
        header('Content-type: application/json');
        http_response_code($code->value);

        if($data)
            echo json_encode($data);
        
        exit();
    }
}
