<?php

namespace ClienteIzie\Services;

use ClienteIzie\Entities\Address;
use ClienteIzie\Entities\Client;
use ClienteIzie\Repositories\AddressRepositoryEloquent;
use ClienteIzie\Repositories\ClientRepository;
use ClienteIzie\Repositories\ClientRepositoryEloquent;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Created by IntelliJ IDEA.
 * User: gvieira
 * Date: 15/05/17
 * Time: 09:52
 */
class ClientService
{
    private $clientRepository;
    private $addressRepository;

    public function __construct(ClientRepositoryEloquent $clientRepository, AddressRepositoryEloquent $addressRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->addressRepository = $addressRepository;
    }

    public function saveBasicInfo(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $this->validateRequestBasicInfo($data);
            $client = $this->prepareEntityClient($data);
            $client->save();
            DB::commit();
            return response()->json(array('message' => 'Cliente salvo'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('message' => $e->getMessage()), $e->getCode());
        }
    }

    public function saveClientV2(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $this->validateRequestBasicInfo($data);
            $client = $this->prepareEntityClient($data);
            $this->validateRequestV2($data);
            $client = $this->prepareEntityClientV2($client, $data);
            $client->save();

            DB::commit();
            return response()->json(array('message' => 'Cliente salvo'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('message' => $e->getMessage()), $e->getCode());
        }
    }

    public function saveClientV3(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $this->validateRequestBasicInfo($data);
            $client = $this->prepareEntityClient($data);
            $this->validateRequestV2($data);
            $client = $this->prepareEntityClientV2($client, $data);
            $client->save();

            if (!empty($data['arAddress'])) {
                $this->addressRepository->deleteWhere(array('id_client' => $client->id));
                foreach ($data['arAddress'] as $address) {
                    $this->saveAddress($address, $client->id);
                }
            }

            DB::commit();
            return response()->json(array('message' => 'Cliente salvo'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('message' => $e->getMessage()), 400);
        }
    }

    public function saveAddress($data, $idClient)
    {
        $address = new Address;
        $address->cep = $data['cep'];
        $address->address = $data['address'];
        $address->number = $data['number'];
        $address->complement = $data['complement'];
        $address->city = $data['city'];
        $address->state = $data['state'];
        $address->id_client = $idClient;
        $address->save();
    }

    public function prepareEntityClientV2($client, $data)
    {
        $client->cpf = $data['cpf'];
        $client->email = $data['email'];
        return $client;
    }

    public function prepareEntityClient($data)
    {
        $client = new Client;
        if (!empty($data['id'])) {
            $client = $this->find($data['id']);
        }
        $client->name = $data['name'];
        $client->gender = $data['gender'];
        $client->dt_birth = $data['dt_birth'];
//            $client->image = $data['image'];
        return $client;
    }

    public function validateRequestBasicInfo($data)
    {
        if (empty($data['name'])) {
            throw new BadRequestHttpException('Campo nome é obrigatório', null, 400);
        }

        if (empty($data['gender'])) {
            throw new BadRequestHttpException('Campo sexo é obrigatório', null, 400);
        }

        if (empty($data['dt_birth'])) {
            throw new BadRequestHttpException('Campo data de nascimento é obrigatório', null, 400);
        }
    }

    public function validateRequestV2($data)
    {
        if (empty($data['email'])) {
            throw new BadRequestHttpException('Campo email é obrigatório', null, 400);
        }

        if (empty($data['cpf'])) {
            throw new BadRequestHttpException('Campo cpf é obrigatório', null, 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException('Email inválido', null, 400);
        }
    }

    public function listClient()
    {
        return $this->clientRepository->all();
    }

    public function find($id)
    {
        return $this->clientRepository->find($id);
    }

    public function findV3($id)
    {
        $clients = $this->clientRepository->findClient($id);

        $arResult = array();

        foreach ($clients as $v) {
            $client = json_decode(json_encode($v), true);

            $arResult[$client['id_client']] = array(
                'id' => $client['id'],
                'name' => $client['name'],
                'gender' => $client['gender'],
                'dt_birth' => $client['dt_birth'],
                'created_at' => $client['created_at'],
                'email' => $client['email'],
                'cpf' => $client['cpf'],
            );

            if (!empty($client['address'])) {
                $address[] = array(
                    'address' => $client['address'],
                    'number' => $client['number'],
                    'complement' => $client['complement'],
                    'cep' => $client['cep'],
                    'city' => $client['city'],
                    'state' => $client['state'],
                );
                $arResult[$client['id_client']]['arAddress'] = $address;
            }

        }

        return response()->json(current($arResult));

    }
}
