<?php

namespace App\Http\Controllers\rewardsProgram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Session;
use App\Http\Controllers\periodsController;
use App\Http\Controllers\routeGlobal;
use App\Http\Controllers\rewardsProgram\bonusController;
use App\Http\Controllers\emailController;

class ordersController extends Controller
{
    //
    public $date;
    protected $routeGlobal;
    protected $emailController;
    public function __construct( routeGlobal $routeGlobal, emailController $emailController){ 
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
        $this->routeGlobal = $routeGlobal->index();
        $this->emailController = $emailController;
    }

    public function ordersAdmin(){
        $orders = DB::table('orders')
        ->leftJoin('order_products', 'order_products.order_id', '=', 'orders.id')
        ->leftJoin('awards','order_products.product_id', '=', 'awards.id')
        ->leftJoin('users', 'orders.user_id', '=', 'users.id')
        ->leftJoin('status_catalog', 'orders.status_id', '=', 'status_catalog.id')
        ->leftJoin('order_address', 'orders.id', '=', 'order_address.order_id')
        ->select(
            'orders.id as orderId',
            'order_products.id as orderProductId',
            'order_products.created_at as orderProductDate',
            'status_catalog.name as statusName',
            'status_catalog.id as statusId',
            'users.name as userName',
            'users.cp',
            'users.country',
            'users.cellPhone',
            'order_address.street',
            'order_address.city',
            'order_address.zip',
            'order_address.suburb',
            'order_address.state',
            'awards.name as productName',
            'awards.image as imageProduct',
            'awards.price',
        )
        ->orderBy('order_products.id','DESC')
        ->paginate(9);

        // dd($orders);

        return view('admin.store.orders.index',['orders' => $orders, 'routeGlobal' => $this->routeGlobal]);

    }

    public function ordersDetailsAdmin($id){

        try {
            //code...
            $order = DB::table('orders')
            ->leftJoin('order_products', 'order_products.order_id', '=', 'orders.id')
            ->leftJoin('awards','order_products.product_id', '=', 'awards.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('status_catalog', 'orders.status_id', '=', 'status_catalog.id')
            ->leftJoin('order_address', 'orders.id', '=', 'order_address.order_id')
            ->select(
                'orders.id as orderId',
                'order_products.id as orderProductId',
                'order_products.created_at as orderProductDate',
                'status_catalog.name as statusName',
                'status_catalog.id as statusId',
                'users.name as userName',
                'users.cp',
                'users.country',
                'users.cellPhone',
                'order_address.street',
                'order_address.city',
                'order_address.zip',
                'order_address.suburb',
                'order_address.state',
                'awards.name as productName',
                'awards.image as imageProduct',
                'awards.price',
            )
            ->where('orders.id', $id)
            ->orderBy('order_products.id','DESC')
            ->first();

            $statusCatalog = DB::table('status_catalog')
            ->where('table', 'orders')
            ->get();

            $selectStatus = "<label for='user' class='form-label'>Estatus: </label>
            <select class='form-select' id='statusSelectOrder' aria-label='Default select example'>";
            foreach ($statusCatalog as $key => $status) {

                $selected = "";
                if ($status->id == $order->statusId) {
                    $selected = 'selected';
                }

                if($status->id == 35){
                    $statusName = 'Pendiente por validar';
                }elseif($status->id == 36){
                    $statusName = 'Confirmado';
                }elseif($status->id == 37){
                    $statusName = 'Cancelado';
                }elseif($status->id == 39){
                    $statusName = 'En camino';
                }elseif($status->id == 40){
                    $statusName = 'Entregado';
                }

                $selectStatus = $selectStatus."<option value='$status->id' $selected>$statusName</option>";
            }
            $selectStatus = $selectStatus."</select>";
    
            return [
                'result' => 'ok',
                'order' => $order,
                'image' => "<img src='".$this->routeGlobal.$order->imageProduct."' style='width: 100%;height: 280px;' alt='$order->productName'/>",
                'statusCatalog' => $statusCatalog,
                'selectStatus' => $selectStatus,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th
            ];
        }


    }

    public function ordersHistory(Request $request){

        try {

            $orders = DB::table('orders')
            ->leftJoin('order_products', 'order_products.order_id', '=', 'orders.id')
            ->leftJoin('awards','order_products.product_id', '=', 'awards.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('status_catalog', 'orders.status_id', '=', 'status_catalog.id')
            ->leftJoin('order_address', 'orders.id', '=', 'order_address.order_id')
            ->select(
                'orders.id as orderId',
                'order_products.id as orderProductId',
                DB::raw('DATE_FORMAT(order_products.created_at, "%d/%m/%Y") as orderProductDate'),
                'status_catalog.name as statusName',
                'status_catalog.id as statusId',
                'users.name as userName',
                'users.cp',
                'users.country',
                'users.cellPhone',
                'order_address.street',
                'order_address.city',
                'order_address.zip',
                'order_address.suburb',
                'order_address.state',
                'awards.name as productName',
                'awards.image as imageProduct',
                'awards.price',
            )
            ->orderBy('order_products.id','DESC')
            ->where('users.token', $request->token)
            ->get();
    
            return [
                'orders' => $orders,
                'routeGlobal' => $this->routeGlobal,
            ];
            
        } catch (\Throwable $th) {

            return [
                'return' => $th
            ];

        }


    }

    public function getOrder($id){

        $order = DB::table('orders')
        ->leftJoin('order_products', 'order_products.order_id', '=', 'orders.id')
        ->leftJoin('awards','order_products.product_id', '=', 'awards.id')
        ->leftJoin('users', 'orders.user_id', '=', 'users.id')
        ->leftJoin('status_catalog', 'orders.status_id', '=', 'status_catalog.id')
        ->leftJoin('order_address', 'orders.id', '=', 'order_address.order_id')
        ->select(
            'orders.id as orderId',
            'order_products.id as orderProductId',
            DB::raw('DATE_FORMAT(order_products.created_at, "%d/%m/%Y") as orderProductDate'),
            'status_catalog.name as statusName',
            'status_catalog.id as statusId',
            'users.name as userName',
            'users.cp',
            'users.country',
            'users.cellPhone',
            'users.email',
            'order_address.street',
            'order_address.city',
            'order_address.zip',
            'order_address.suburb',
            'order_address.state',
            'awards.name as productName',
            'awards.image as imageProduct',
            'awards.price',
        )
        ->where('orders.id', $id)
        ->first();

        return $order;

    }

    public function ordersUpdateStatus(Request $request){

        try {

            DB::table('orders')
            ->where('orders.id', $request->orderId)
            ->update([
                'orders.status_id' => $request->status_id
            ]);

            $order = $this->getOrder($request->orderId);

            $emailBuy = $this->emailController->buyProductEmail($order);

            if($emailBuy['result'] != 'ok'){
                return [
                    'result' => $emailBuy
                ];
            }

            return [
                'result' => 'ok'
            ];

        } catch (\Throwable $th) {
            return [
                'result' => $th
            ];
        }


    }


}
