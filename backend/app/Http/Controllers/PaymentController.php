<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as Controller;
use Inertia\Inertia;
use MercadoPago;

class PaymentController extends Controller
{
    public function index()
    {
        $items_list = array(
            array('producto'=>'Monitor 24', 'precio'=>25000, 'cantidad'=> 1),
            array('producto'=>'Teclado Redragon Yama', 'precio'=>8500, 'cantidad'=> 3),
            array('producto'=>'Luces RGB', 'precio'=>1700, 'cantidad'=> 7)
        );
        MercadoPago\SDK::setAccessToken(env("ACCESS_TOKEN"));
        $items = $this->createItems($items_list);
        $preference = $this->createParameter();
        $preference->items = $items;
        $preference->save();
        dd($preference);
        $response = array(
            'id' => $preference->id
        );
        return json_encode($response);
        #return Inertia::render('Pages/Resumen', ['response' => $response]);
        ##echo json_encode($response);
        #return view('resumen', ['response' => $response]);
        #header("Location:https://www.mercadopago.com.uy/checkout/v1/payment/redirect/86aa7bb4-3099-42bb-aaa8-93605bef9056/payment-option-form/?source=link&preference-id=".$preference->id);
    }

    private function createParameter()
    {
        $preference = new MercadoPago\Preference();
        $preference->back_urls = [
            "success" => "http://localhost/php/MercadoPagoMVC/index.php?c=pagos&a=success",
            "failure" => "http://localhost/php/MercadoPagoMVC/index.php?c=pagos&a=failure",
            "pending" => "http://localhost/php/MercadoPagoMVC/index.php?c=pagos&a=pending"
        ];
        $preference->auto_return = "approved";
        return $preference;
    }

    private function createItems($items_list)
    {
        $items = [];
        foreach($items_list as $i){
            $item = new MercadoPago\Item();
            $item->title = $i['producto'];
            $item->quantity = $i['cantidad'];
            $item->unit_price = $i['precio'];
            array_push($items,$item);
        }
        return $items;
    }
}
