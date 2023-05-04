<?php

namespace App\Enums;

enum OrderStatus : string {
    case REALIZADO = 'REALIZADO';
    case CANCELADO = 'CANCELADO';
}
