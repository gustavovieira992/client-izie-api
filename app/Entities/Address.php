<?php

namespace ClienteIzie\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Address extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'cep',  'address', 'number', 'complement', 'city', 'state', 'id_client'
    ];

}
