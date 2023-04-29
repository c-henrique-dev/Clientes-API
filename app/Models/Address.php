<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class Address extends Model
{
    use HasFactory;

    protected $table = 'address';

    protected $fillable = [
        'cep',
        'number',
        'neighborhood',
        'city',
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

}
