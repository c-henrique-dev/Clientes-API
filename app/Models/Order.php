<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\OrderItem;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function ordersItems() {
        return $this->hasMany(OrderItem::class);
    }

}
