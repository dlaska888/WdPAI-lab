<?php

namespace LinkyApp\Controllers;

use LinkyApp\Enums\UserRole;
use LinkyApp\LinkyRouting\Attributes\Authorization\Authorize;
use LinkyApp\LinkyRouting\Attributes\Controller\ApiController;
use LinkyApp\LinkyRouting\Attributes\Route;
use LinkyApp\LinkyRouting\Responses\Json;
use LinkyApp\Validators\GetWebTitle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

#[ApiController]
#[Authorize([UserRole::NORMAL, UserRole::ADMIN])]
class UtilController extends AppController
{
    #[Route("util/webtitle")]
    function getPageTitle(): Json
    {
        $this->validateRequestData($_GET, GetWebTitle::class);

        $client = HttpClient::create();
        $url = $_GET['url'];

        $response = $client->request('GET', $url);
        $htmlContent = $response->getContent();

        $crawler = new Crawler($htmlContent);

        $title = $crawler->filter('title')->text();

        return new Json(["title" => $title]);
    }
}