<?php

require_once "src/controllers/AppController.php";

class LinkController extends AppController
{
    private LinkRepo $linkRepo;

    private UserSessionHandler $sessionHandler;

    public function __construct()
    {
        parent::__construct();
        $this->sessionHandler = new UserSessionHandler();
        $this->linkRepo = new LinkRepo();
    }

    public function link(string $id): void
    {
        if (!$this->sessionHandler->isSessionSet()) {
            $this->jsonResponse(HttpStatusCode::UNAUTHORIZED, "User unauthorized.");
        }

        if ($this->isGet()) {
            $result = $this->linkRepo->findById($id);
            
            if(!$result)
                $this->jsonResponse(HttpStatusCode::NOT_FOUND, "Link with this id does not exist.");
            
            $this->jsonResponse(HttpStatusCode::OK, $result);
        }
    }
}