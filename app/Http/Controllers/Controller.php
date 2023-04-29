<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

 /**
     * @OA\OpenApi(
     *     @OA\Info(
     *         version="1.0",
     *         title="Clientes-API",
     *         description="Em caso de <b>problemas/bugs</b> com alguma rota na API, por favor abra uma issue detalhando o erro e, se possível, forneça os passos necessários para reproduzi-lo."
     *     )
     * )
     */
class Controller extends BaseController
{
    
    use AuthorizesRequests, ValidatesRequests;
}
