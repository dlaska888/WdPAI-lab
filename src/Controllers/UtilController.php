<?php

namespace LinkyApp\Controllers;

use LinkyApp\Enums\UserRole;
use LinkyApp\LinkyRouting\Attributes\Authorization\Authorize;
use LinkyApp\LinkyRouting\Attributes\Controller\ApiController;
use LinkyApp\LinkyRouting\Attributes\Route;
use LinkyApp\LinkyRouting\Responses\Json;
use LinkyApp\Validators\GetWebTitleValidator;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

#[ApiController]
#[Authorize([UserRole::NORMAL, UserRole::ADMIN])]
class UtilController extends AppController
{
    #[Route("util/webtitle")]
    function getPageTitle(): Json
    {
        $this->validateRequestData($_GET, GetWebTitleValidator::class);

        $client = HttpClient::create();
        $url = $_GET['url'];

        try {
            $response = $client->request('GET', $url, [
                'max_redirects' => 5,
                'verify_peer' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ]
            ]);
        } catch (\Exception $e) {
            return new Json(["error" => $e->getMessage()]);
        }

        $htmlContent = $response->getContent();

        $crawler = new Crawler($htmlContent);

        $title = $crawler->filter('title')->text();
        $title = mb_convert_encoding($title, 'UTF-8');
        $title = isset($_GET['maxLength']) ?
            mb_substr($title, 0, $_GET['maxLength']) :
            $title;

        return new Json(["title" => $title]);
    }
}
