<?php

namespace ClienteIzie\Http\Controllers;

use ClienteIzie\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function storeV1(Request $request)
    {
        return $this->clientService->saveBasicInfo($request);
    }

    public function storeV2(Request $request)
    {
        return $this->clientService->saveClientV2($request);
    }

    public function storeV3(Request $request)
    {
        return $this->clientService->saveClientV3($request);
    }

    public function listV1()
    {
        return $this->clientService->listClient();
    }

    public function find($id)
    {
        return $this->clientService->find($id);
    }

    public function findV3($id)
    {
        return $this->clientService->findV3($id);
    }
}
