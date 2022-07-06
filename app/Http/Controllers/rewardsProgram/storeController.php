<?php

namespace App\Http\Controllers\rewardsProgram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
// use App\Http\Controllers\rewardsProgram\promotionController;
use App\Http\Controllers\routeGlobal;
use App\Http\Controllers\emailController;
use App\Http\Controllers\rewardsProgram\ordersController;

class storeController extends Controller
{

    public $date;
    protected $routeGlobal;
    protected $emailController;
    protected $ordersController;
    public function __construct( routeGlobal $routeGlobal, emailController $emailController, ordersController $ordersController)
    { 
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
        $this->routeGlobal = $routeGlobal->index();
        $this->emailController = $emailController;
        $this->ordersController = $ordersController;
    }


    public function storeAdmin(){
        try {
            //code...
            $awards = $this->writeAwardTable('nonUpdate');
            // dd($awards);

            
            return view('admin.store.index',['awards' => $awards, 'routeGlobal' => $this->routeGlobal]);
            
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }

    public function storeAddProductAdmin(Request $request){

        try {
            //code...
            // Get promo with admin selected
            $promo = DB::table('promo')
            ->where('id',$request->promoId)
            ->first();

          
            $description = $request->descriptionAward;
            $name = $request->nameAward;
            
            if($request->image){
                $nameImage = str_replace(' ', '', $name);
                $data = explode( ',', $request->image );
                $pathP = 'img/award/promo'.$promo->id.'/'.$nameImage.'.'.$request->extension;
                $content = base64_decode($data[1]);
                $typeFile = Str::substr($data[0],0,10);
                if($typeFile == 'data:image' ){
                    Storage::disk('public')->put( $pathP, $content);
                }
            }

           
            DB::table('awards')
            ->insert([
                'promo_id' => $promo->id,
                'image' => $pathP,
                'name' => $name,
                'description' => $description,
                'price' => $request->addAwardPrice,
                'redeem' => 0,
                'stock' => $request->stockAward,
                'status_id' => 26,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);

            $tableAwards = $this->writeAwardTable(); 

            return [
                'result' => 'ok',
                'table' => $tableAwards,
            ];

        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }

    }

    public function storeDeleteProduct(Request $request){

        try {
            //code...
            DB::table('awards')
            ->where('awards.id', '=', $request->awardId)
            ->update([
                'status_id' => 27
            ]);
    
            $tableAwards = $this->writeAwardTable();
    
            return [
                'result' => 'ok',
                'table' => $tableAwards,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

        
    }

    public function storeUpdateProduct(Request $request){

        try {
            //code...
            // Get promo with admin selected
            $promo = DB::table('promo')
            ->where('id',$request->promoId)
            ->first();

            // return [$request->all()];

            $name = $request->awardName;
            $description = $request->description;

            if($request->image){
                $nameImage = str_replace(' ', '', $name);
                $data = explode( ',', $request->image );
                $pathP = 'img/award/promo'.$promo->id.'/'.$nameImage.'.'.$request->extension;
                $content = base64_decode($data[1]);
                $typeFile = Str::substr($data[0],0,10);
                if($typeFile == 'data:image' ){
                    Storage::disk('public')->put( $pathP, $content);
                }

                DB::table('awards')
                ->where('awards.id', $request->awardId)
                ->update([
                    'image' => $pathP,
                ]);

            }



            DB::table('awards')
            ->where('awards.id', $request->awardId)
            ->update([
                'name' => $name,
                'description' => $description,
                'stock' => $request->stock,
                'price' => $request->price,
                'updated_at' => $this->date,
            ]);

            
            $tableAwards = $this->writeAwardTable(); 

            return [
                'result' => 'ok',
                'table' => $tableAwards,
            ];

        } catch (\Throwable $th) {

            $json = ['resp' => $th];
            return $json;
        }

    }

    public function storeAdminDetails($id){

        $award = DB::table('awards')
        ->leftJoin('status_catalog as status', 'awards.status_id', '=', 'status.id')
        ->leftJoin('promo', 'awards.promo_id', '=', 'promo.id')
        ->select(
            'awards.id as awardId',
            'awards.promo_id as promoId',
            'awards.name as awardName',
            'awards.description',
            'awards.image',
            'awards.stock',
            'awards.position',
            'awards.redeem',
            'awards.price',
            'promo.title as promoTitle',
            'status.name as statusName',
            'status.id as statusId',
        )
        ->where('awards.id',$id)
        ->first();

        return [
            'result' => 'ok',
            'award' => $award,
        ];

    }

    // Drawing table in HTML
    public function writeAwardTable($update = null){

        try {
            //code...
            $awards = DB::table('awards')
            ->leftJoin('status_catalog as status', 'awards.status_id', '=', 'status.id')
            ->leftJoin('promo', 'awards.promo_id', '=', 'promo.id')
            ->select(
                'awards.id as awardId',
                'awards.promo_id as promoId',
                'awards.name as awardName',
                'awards.image',
                'awards.stock',
                'awards.position',
                'awards.redeem',
                'awards.price',
                'promo.title as promoTitle',
                'status.name as statusName',
                'status.id as statusId',
            )
            ->whereIn('promo.type_id',[1,4])
            ->orderBy('awards.position', 'ASC')
            ->paginate(10);

            if ( !$update ) {
                # code...
                $tableAwards = "";
        
                foreach ($awards as $key => $award) {
                    $textClass = 'text-danger';
                    if ( $award->statusId == 26 ){
                        $textClass = 'text-success';
                    }
        
                    $tableAwards = $tableAwards ."<tr>
                        <td scope='row' style='width: 20%;'>
                        <img src='".$this->routeGlobal.'/'.$award->image."' style='width: 100px;' alt='$award->awardName' />
                        </th>
                        <th>$award->awardName</td>
                        <td>$award->stock</td>
                        <td>$award->price</td>
                        <td class='$textClass'>$award->statusName</td>
                        <td>
                            <button type='button' data-bs-toggle='modal' data-bs-target='#viewDetailsAwards' onclick='getDetailsAwards($award->awardId)' class='btn btn-success'><i class='fas fa-eye me-2'></i>Ver detalles</button>
        
                            <button type='button' class='btn btn-danger' onclick='deleteAward($award->awardId)' >
                                <i class='fas fa-trash me-2'></i>Borrar premio
                            </button>
                        </td>
                    </tr>";
                }
                return  $tableAwards;
            }

            return  $awards;

        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

    }

    // PWA
    public function getProductStorePwa($promo_id){

        $products = DB::table('awards')
        ->where('promo_id', $promo_id)
        ->get();

        // el segundo where sirve para la rifa de la giftCard

        return [
            'products' => $products,
            'routeGlobal' => $this->routeGlobal
        ];

    }

    public function getDetailsProduct($product_id, $token_user){
        try {

            $validExistOrder = false;
            $user = DB::table('users')
            ->where('users.token', $token_user)
            ->first();

            $product = DB::table('awards')
            ->where('id', $product_id)
            ->first();

            $validExistOrder = $this->validOrderExist($user->id, $product);
            
    
            return [
                // 'orderP' => $orderP,
                'product' => $product,
                'validExistOrder' => $validExistOrder,
                'routeGlobal' => $this->routeGlobal
            ];

        } catch (\Throwable $th) {
            return ['result' => $th];
        }
    }

    public function buyProduct(Request $request){
        try {

            $productArr = $request->product;
            $walletArr = $request->wallet;

            if ($walletArr['balance'] >= $productArr['price']) {

                $product = DB::table('awards')
                ->where('awards.id', $productArr['id'])
                ->first();

                $wallet = DB::table('users')
                ->leftJoin('wallets', 'users.id', '=', 'wallets.user_id')
                ->select([
                    'wallets.id',
                    'wallets.user_id',
                    'wallets.balance',
                    'users.city',
                ])
                ->where('users.id', $walletArr['id'])
                ->first();

                if ($product->stock > 0) {

                    DB::table('orders')
                    ->insert([
                        'price' => $productArr['price'],
                        'user_id' => $wallet->user_id,
                        'status_id' => 35,
                        'notes' => '',
                        'created_at' => $this->date,
                        'updated_at' => $this->date,
                    ]);
    
                    $order = DB::table('orders')
                    ->where('user_id', $wallet->user_id)
                    ->orderBy('id', 'DESC')
                    ->first();
    
                    DB::table('order_products')
                    ->insert([
                        'price' => $product->price,
                        'product_id' => $product->id,
                        'order_id' => $order->id,
                        'created_at' => $this->date,
                        'updated_at' => $this->date,
                    ]);

                    DB::table('order_address')
                    ->insert([
                        'street' => $request->address['street'],
                        'suburb' => $request->address['suburb'],
                        'city' => $request->address['city'],
                        'state' => $request->address['state'],
                        'zip' => $request->address['zip'],
                        'order_id' => $order->id,
                        'created_at' => $this->date,
                        'updated_at' => $this->date,
                    ]);

                    if(!$wallet->city){
                        DB::table('users')
                        ->where('users.id',$wallet->user_id)
                        ->update([
                            'street' => $request->address['street'],
                            'suburb' => $request->address['suburb'],
                            'city' => $request->address['city'],
                            'state' => $request->address['state'],
                            'cp' => $request->address['zip'],
                        ]);
                    }
    
                    DB::table('awards')
                    ->where('awards.id', $product->id)
                    ->update([
                        'stock' => $product->stock - 1,
                        'redeem' => $product->redeem + 1,
                    ]);

                    DB::table('wallets')
                    ->where('wallets.id', $wallet->id)
                    ->update([
                        'balance' => ($wallet->balance - $order->price)
                    ]);

                    DB::table('transactions')
                    ->insert([
                        'wallet_id' => $wallet->id,
                        'amount' =>  $order->price,
                        'type_id' => 8,
                        'promo_id' => 1,
                        'title' => 'Compra en tienda',
                        'created_at' => $this->date,
                        'updated_at' => $this->date,
                    ]);

                    $award = DB::table('awards')
                    ->where('awards.id', $product->id)
                    ->first();

                    $wallet = DB::table('wallets')
                    ->select(
                        'wallets.user_id as id',
                        'wallets.balance',
                    )
                    ->where('wallets.id', $wallet->id)
                    ->first();

                    $orderDetails = $this->ordersController->getOrder($order->id);

                    $emailBuy = $this->emailController->buyProductEmail($orderDetails);

                    $validExistOrder = false;

                    $validExistOrder = $this->validOrderExist($wallet->id, $award);

                    // return [$validExistOrder];

                    if ($emailBuy['result'] != 'ok') {
                        return [$emailBuy];
                    }

                    return [
                        'result' => 'ok',
                        'validExistOrder' => $validExistOrder,
                        'product' => $award,
                        'wallet' => $wallet,
                    ];

                }else{
                    $result = "Lo sentimos el stock de este producto esta agotado.";
                }

            }else{
                $result = " Aun no tienes el saldo suficiente para poder canjear este producto.";
            }
    
            return [
                'result' => $result
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th
            ];
        }

    }

    public function validOrderExist($user_id, $award){


        $orderP = DB::table('order_products')
        ->leftJoin('orders','order_products.order_id', '=', 'orders.id')
        ->where([
            ['orders.user_id', '=', $user_id],
            ['order_products.product_id', '=', $award->id]
        ])
        ->count();

        // return $orderP;

        if($orderP >= $award->max_per_user){

            if($award->max_per_user != 0){
                return true;
            }
        }

        return false;
        
    }

}
