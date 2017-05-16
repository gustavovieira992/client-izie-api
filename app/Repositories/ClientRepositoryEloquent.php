<?php

namespace ClienteIzie\Repositories;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use ClienteIzie\Repositories\ClientRepository;
use ClienteIzie\Entities\Client;
use ClienteIzie\Validators\ClientValidator;

/**
 * Class ClientRepositoryEloquent
 * @package namespace ClienteIzie\Repositories;
 */
class ClientRepositoryEloquent extends BaseRepository implements ClientRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Client::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findClient($id)
    {
        $client = DB::table('clients')
            ->leftJoin('addresses', 'clients.id', '=', 'addresses.id_client')
            ->select('clients.*', 'addresses.*')
            ->where('clients.id', '=', $id)
            ->get();
        return $client;
    }
}
