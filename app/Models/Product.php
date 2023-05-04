<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\ItemPedido;


class Product extends Model
{
    use HasFactory;

    public function itemPedidos() {
        return $this->hasMany(ItemPedido::class);
    }

    protected $fillable = [
        'description',
        'price',
    ];
}
