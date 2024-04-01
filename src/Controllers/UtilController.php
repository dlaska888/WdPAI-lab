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

        $response = $client->request('GET', $url);
        $htmlContent = $response->getContent();

        $crawler = new Crawler($htmlContent);

        $title = $crawler->filter('title')->text();

        $title = isset($_GET['maxLength']) ? mb_substr($title, 0, $_GET['maxLength']) : $title;

        return new Json(["title" => $title]);
    }
}