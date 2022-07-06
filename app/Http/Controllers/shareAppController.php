<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\periodsController;
use Carbon\Carbon;
use Session;

class shareAppController extends Controller
{
    //
    protected $routeGlobal;
    protected $periodsController;
    public $date;
    
    public function __construct(routeGlobal $routeGlobal, periodsController $periodsController)
    {
        $this->periodsController = $periodsController;
        $this->routeGlobal = $routeGlobal;
        $this->date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }

    public function index(Request $request){

        // dd('hola');
        if($request->session()->exists('admin_id')){

            $promos = DB::table('promo')
            ->where('type_id', 5)
            ->get();

            // dd($promos);

            $status_id = 9;

            $appShare = DB::table('app_share')
            ->leftJoin('status_catalog', 'app_share.status', '=', 'status_catalog.id')
            ->leftJoin('users', 'app_share.user_id', '=', 'users.id')
            ->leftJoin('periods', 'app_share.period_id', '=', 'periods.id')
            ->select('app_share.url', 'app_share.created_at', 'users.name','periods.id as period_id', 'status_catalog.name as status','app_share.id')
            ->where('app_share.status', $status_id)
            ->paginate(9); 
    
            // dd($appShare);
    
            return view('admin.shareApp.index',['appShares' => $appShare, 'promos' => $promos, 'status_id' => $status_id]);
        }else{
            // dd('hola');
            return view('authAdmin.loginAdmin');
        }
    }

    public function createAppShare(Request $request){

        try {
            //code...
            $date =Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            $period = $this->periodsController->index($date,$request->promo_id);
            
            $user = DB::table('users')
            ->where("nickName",$request->nickName)
            ->first();
            
            $data = explode( ',', $request->image );
            $typeFile = Str::substr($data[0],0,10);
            if($typeFile == 'data:image' ){
                $pathP = 'img/share/'.'share-'.$user->token.'.'.$request->extension;
                $content = base64_decode($data[1]);
                Storage::disk('public')->put( $pathP, $content);
    
                $routeGlobal = $this->routeGlobal->index();
                $path = $routeGlobal.$pathP;
    
    
                DB::table('app_share')->insert([
                    ['user_id' => $user->id, 'period_id' => $period->id, 'image' => $path, 'url' => $request->url ,'status' => 9,
                    'created_at' => $date, 'updated_at' => $date],
                ]);  
    
                $app_share = DB::table('app_share')
                ->leftJoin('status_catalog', 'app_share.status', '=', 'status_catalog.id')
                ->leftJoin('users', 'app_share.user_id', '=', 'users.id')
                ->leftJoin('periods', 'app_share.period_id', '=', 'periods.id')
                ->select('app_share.url','app_share.image','app_share.period_id','users.name',
                        'users.id as user_id', 'status_catalog.name as status','periods.id as period_id',
                        'app_share.id','app_share.status as statusId','app_share.comment','app_share.validate')
                ->where("app_share.user_id",$user->id)
                ->where("app_share.period_id",$period->id)
                ->first();
    
    
                $json = [
                    "result" => [
                        'url' => $app_share->url,
                        'image' => $app_share->image,
                        'period_id' => $app_share->period_id,
                        'name' => $app_share->name,
                        'user_id' => $app_share->user_id, 
                        'status' => $app_share->status,
                        'period_id' => $app_share->period_id,
                        'id' => $app_share->id,
                        'statusId' => $app_share->statusId,
                        'comment' => $app_share->comment,
                        'validate' => $app_share->validate,
                    ]
                ];
                //echo json_encode($json);
                return $json;
            }else{
                $json = [
                    "message" => 'error',
                    'type' => $typeFile
                    // "minigame_score" => $minigame_score,
                ];
                return $json;
            }
        } catch (\Throwable $th) {
            //throw $th;

            $json = [
                "message" => $th
            ];
            //echo json_encode($json);
            return $json;

        }
        
    }

    public function updateAppSharePwa(Request $request){
        try {
            //code...
            $date =Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));

            // retorna los ids de los periodos
            $periods = $this->periodsController->index( $date,$request->promo_id );

            $user = DB::table('users')
            ->where("nickName",$request->nickName)
            ->first();


            $data = explode( ',', $request->image );
            $typeFile = Str::substr($data[0],0,10);
            if($typeFile == 'data:image' ){
                $pathP = 'img/share/'.'share-'.$user->token.'.'.$request->extension;
                $content = base64_decode($data[1]);
                Storage::disk('public')->put( $pathP, $content);
    
                $routeGlobal = $this->routeGlobal->index();
                $path = $routeGlobal.$pathP;
    
                DB::table('app_share')
                   ->where('id', $request->id)
                   ->update(['status' => 9, 'comment' => '', 'validate' => 0,
                   'image' => $path, 'url' => $request->url, 'updated_at' => $date]);
    
                $app_share = DB::table('app_share')
                ->leftJoin('status_catalog', 'app_share.status', '=', 'status_catalog.id')
                ->leftJoin('users', 'app_share.user_id', '=', 'users.id')
                ->leftJoin('periods', 'app_share.period_id', '=', 'periods.id')
                ->select('app_share.url','app_share.image','app_share.period_id','users.name',
                        'users.id as user_id', 'status_catalog.name as status','periods.id as period_id',
                        'app_share.id','app_share.status as statusId','app_share.comment','app_share.validate')
                ->where('app_share.id', $request->id)
                ->first();
    
    
                $json = [
                    "result" => [
                        'url' => $app_share->url,
                        'image' => $app_share->image,
                        'period_id' => $app_share->period_id,
                        'name' => $app_share->name,
                        'user_id' => $app_share->user_id, 
                        'status' => $app_share->status,
                        'period_id' => $app_share->period_id,
                        'id' => $app_share->id,
                        'statusId' => $app_share->statusId,
                        'comment' => $app_share->comment,
                        'validate' => $app_share->validate,
                    ]
                ];
                //echo json_encode($json);
                return $json;
            }else{
                $json = [
                    "message" => 'error',
                    'type' => $typeFile
                    // "minigame_score" => $minigame_score,
                ];
                return $json;
            }
        } catch (\Throwable $th) {
            //throw $th;

            $json = [
                "message" => $th
            ];
            //echo json_encode($json);
            return $json;

        }
    }

    public function sharesAprobe(Request $request){

        if($request->session()->exists('admin_id')){

            $promos = DB::table('promo')
            ->where('type_id', 5)
            ->get();

            $status_id = 8;

            $appShare = DB::table('app_share')
            ->leftJoin('status_catalog', 'app_share.status', '=', 'status_catalog.id')
            ->leftJoin('users', 'app_share.user_id', '=', 'users.id')
            ->leftJoin('periods', 'app_share.period_id', '=', 'periods.id')
            ->select('app_share.url', 'app_share.created_at', 'users.name','periods.id as period_id', 'periods.order', 'status_catalog.name as status','app_share.id')
            ->where('app_share.status', $status_id)
            ->paginate(9); 
    
            // dd($appShare);
    
            return view('admin.shareApp.shareAprobe',['appShares' => $appShare, 'promos' => $promos, 'status_id' => $status_id]);
        }else{
            // dd('hola');
            return view('authAdmin.loginAdmin');
        }

    }

    public function sharesCancel(Request $request){

        if($request->session()->exists('admin_id')){
            
            $promos = DB::table('promo')
            ->where('type_id', 5)
            ->get();

            $status_id = 7;


            $appShare = DB::table('app_share')
            ->leftJoin('status_catalog', 'app_share.status', '=', 'status_catalog.id')
            ->leftJoin('users', 'app_share.user_id', '=', 'users.id')
            ->leftJoin('periods', 'app_share.period_id', '=', 'periods.id')
            ->select('app_share.url', 'app_share.created_at', 'users.name', 'periods.id as period_id', 'status_catalog.name as status','app_share.id')
            ->where('app_share.status', $status_id)
            ->paginate(9); 
    
            // dd($appShare);
    
            return view('admin.shareApp.shareCancel',['appShares' => $appShare, 'promos' => $promos, 'status_id' => $status_id]);
        }else{
            // dd('hola');
            return view('authAdmin.loginAdmin');
        }

    }

    public function updateAppShare($id, Request $request){
        if($request->session()->exists('admin_id')){
            $app_share = DB::table('app_share')
            ->leftJoin('status_catalog', 'app_share.status', '=', 'status_catalog.id')
            ->leftJoin('users', 'app_share.user_id', '=', 'users.id')
            ->leftJoin('periods', 'app_share.period_id', '=', 'periods.id')
            ->leftJoin('admin_users','app_share.id_admin', '=', 'admin_users.id')
            ->select('app_share.url','app_share.image', 'admin_users.name as adminName', 'users.name', 'status_catalog.name as status',
            'app_share.id','app_share.status as statusId', 'app_share.comment','app_share.validate')
            ->where('app_share.id',$id)
            ->first(); 
    
            // dd($appShare);
            $status = DB::table('status_catalog')
            ->where('table','app_share')
            ->get();
    
            $appShare = [
                'image' => $app_share->image,
                'url' => $app_share->url,
                'name' => $app_share->name, 
                'status' => $app_share->status,
                'id' => $app_share->id,
                'statusId' => $app_share->statusId, 
                'comment' => $app_share->comment,
                'validate' => $app_share->validate,
                'adminName' => $app_share->adminName
            ];
            // dd($appShares);
    
            return view('admin.shareApp.update',['appShare' => $appShare, 'status' => $status]);
        }else{
            // dd('hola');
            return view('authAdmin.loginAdmin');
        }
        
    }

    public function updateAppShareConf($id,$status,$comment, Request $request){
        try {

            if($comment == 'nada'){
                $comment = '';
            }

            $appShare = DB::table('app_share')
            ->where('app_share.id',$id)
            ->where('app_share.status','<>','9')
            ->first();
            $date =Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));


            if(!$appShare){

                DB::table('app_share')
               ->where('id', $id)
               ->update([
                   'status' => $status,
                   'comment' => $comment,
                   'validate' => 1,
                   'id_admin' => $request->session()->get('admin_id'),
                   'updated_at' => $date
                ]);
       
               $appShare = DB::table('app_share')
                ->leftJoin('status_catalog', 'app_share.status', '=', 'status_catalog.id')
                ->leftJoin('users', 'app_share.user_id', '=', 'users.id')
                ->leftJoin('periods', 'app_share.period_id', '=', 'periods.id')
                ->select(
                    'app_share.url',
                    'app_share.image',
                    'app_share.period_id',
                    'users.name',
                    'users.id as user_id',
                    'status_catalog.name as status',
                    'periods.id as period_id',
                    'periods.promo_id',
                    'app_share.id',
                    'app_share.status as statusId',
                    'app_share.comment',
                    'app_share.validate')
               ->where('app_share.id',$id)
               ->first();
       
               $statusDiv = "<label for='user' class='form-label me-4'>Estatus:</label>";
       
               
                if ($appShare->status == 'Revisando'){
                    $appShare = $statusDiv."<button type='button' class=' btn btn-warning rounded-pill text-white text-center'>".$appShare->status."</button>";
                }

                elseif ($appShare->status == 'Aprobado'){

    
                    DB::table('extra_points')->insert([
                        'user_id' => $appShare->user_id,
                        'period_id' => $appShare->period_id,
                        'type_id' => 3,
                        'points' => 20,
                        'status' => 6,
                        'refer_or_minigames_id' => $id,
                        'created_at' => $this->date,
                        'updated_at' => $this->date,
                    ]);

                    $wallet = DB::table('wallets')
                    ->where('user_id', $appShare->user_id)
                    ->first();

                    $walletPoints = 20 + $wallet->balance;

                    DB::table('wallets')
                    ->where('user_id', $appShare->user_id)
                    ->update([
                        'balance' => $walletPoints
                    ]);

                    DB::table('transactions')
                    ->insert([
                        'wallet_id' => $wallet->id,
                        'amount' =>  20,
                        'type_id' => 7,
                        'promo_id' => $appShare->promo_id,
                        'title' => 'Compartido en redes sociales',
                        'created_at' => $this->date,
                        'updated_at' => $this->date,
                    ]);
    
                    
                    $statusDiv = $statusDiv."<button type='button' class=' btn btn-success rounded-pill text-white text-center'>".$appShare->status."</button>";
                }
                else {
                    $statusDiv = $statusDiv."<button type='button' class=' btn btn-danger rounded-pill text-white text-center'>".$appShare->status."</button>";
                }
       
               $json = [
                   "message"=>'ok',
                   "result" => $statusDiv
               ];
               //echo json_encode($json);
               return $json;
            }
            $json = [
                "message"=>'invalid',
            ];
            //echo json_encode($json);
            return $json ;
        } catch (\Throwable $th) {
            $json = [
                "message"=>'invalid',
                "result" => $th
            ];
            //echo json_encode($json);
            return $json;
        }
        
    }

    public function validExistShare(Request $request){

        try {
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            $dateValid = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'))->format('m-d');
            $period = $this->periodsController->index($date,$request->promo_id);
            
            
            $user = DB::table('users')
            ->where("nickName",$request->nickName)
            ->first();
    
            $app_share = DB::table('app_share')
            ->leftJoin('status_catalog', 'app_share.status', '=', 'status_catalog.id')
            ->leftJoin('users', 'app_share.user_id', '=', 'users.id')
            ->leftJoin('periods', 'app_share.period_id', '=', 'periods.id')
            ->select(
                'users.name',
                'users.id as user_id', 
                'status_catalog.name as status',
                'periods.id as period_id',
                'app_share.url',
                'app_share.image',
                'app_share.period_id',
                'app_share.id',
                'app_share.status as statusId',
                'app_share.comment',
                'app_share.validate',
                DB::raw('DATE_FORMAT(app_share.created_at, "%m-%d") as created_at'),
                // 'app_share.created_at',
            )
            ->where([
                ['user_id', '=',$user->id],
                ['period_id', '=',$period->id],
            ])
            ->first();
    
            // $validDate = true;
    
            if($app_share){
                
                if( $dateValid > $app_share->created_at){
    
                    $json = [
                        "message"=>'nonExist'
                    ];
                }else{
                    $json = [
                        "message"=>'ok',
                        "result" => ['url' => $app_share->url,
                        'image' => $app_share->image,
                        'period_id' => $app_share->period_id,
                        'name' => $app_share->name,
                        'user_id' => $app_share->user_id, 
                        'status' => $app_share->status,
                        'period_id' => $app_share->period_id,
                        'id' => $app_share->id,
                        'statusId' => $app_share->statusId,
                        'comment' => $app_share->comment,
                        'validate' => $app_share->validate,]
                    ];
                }
            }else{
                $json = [
                    "message"=>'nonExist'
                ];
            }
    
            //echo json_encode($json);
            return $json;
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

    public function searchShare(Request $request){
        try {
            //code...
            $promo_id = $request->promo_id;
            $user_name = $request->user_name;
            $date_initial = $request->date_initial;
            $date_final = $request->date_final;
            $corteSelected = $request->corteSelected;
            $status = $request->status;

            // return [
            //     'promo_id' => $promo_id,
            //     'user_name' => $user_name,
            //     'date_initial' => $date_initial,
            //     'date_final' => $date_final,
            //     'corteSelected' => $corteSelected,
            //     'status' => $status,
            // ];


    
            $shares = DB::table('app_share')
            ->leftJoin('status_catalog', 'app_share.status', '=', 'status_catalog.id')
            ->leftJoin('users', 'app_share.user_id', '=', 'users.id')
            ->leftJoin('periods', 'app_share.period_id', '=', 'periods.id')
            ->select(
                'app_share.url',
                'app_share.created_at',
                'users.name',
                'periods.id as period_id',
                'periods.order',
                'status_catalog.name as status',
                'app_share.id',
            )
            ->where('app_share.status', $status)
            ->when($user_name, function ($query, $user_name) {
                return $query->where('users.name', 'like', '%'.$user_name.'%');
            })
            ->when($promo_id, function ($query, $promo_id) {
                return $query->where('periods.promo_id', $promo_id);
            })
            ->when($date_initial, function ($query, $date_initial) {
                return $query->where('app_share.created_at', '>=', $date_initial);
            })
            ->when($date_final, function ($query, $date_final) {
                return $query->where('app_share.created_at', '<=', $date_final);
            })
            ->when($corteSelected, function ($query, $corteSelected) {
                return $query->where('periods.id', '=', $corteSelected);
            })
            ->get();

            // return $shares;
            $dataTable = "";

            if( $status == 9){
                $textClass = 'text-warning';
            }elseif( $status == 8){
                $textClass = 'text-success';
            }else{
                $textClass = 'text-danger';
            }


            if(count($shares) > 0){

                foreach ($shares as $key => $share) {
                    $status_name = $share->status;
                    if( $status == 9){
                        $status_name = 'En revision';
                    }

                    $dataTable = $dataTable . "<tr>
                        <th scope='row'>$share->id </th>
                        <td>$share->name</td>
                        <td>$share->created_at</td>
                        <td>Corte  $share->order</td>
                        <td class='$textClass'>$status_name</td>
                        <td style='width: 15vw;' class='text-center'>
                            <a type='button' href='".route('shareAppUpdate',['id'=> $share->id])."' class='btn btn-success mb-2'>Editar</a>
                        </td>
                    </tr>";
                }
                
                $result = 'ok';
            }else{

                $dataTable = "<tr class='text-center'>
                    <th colspan='7'> Información no encontrada </th>
                </tr>";

                $result = 'non';
            }

            return [
                'result' => $result,
                'table' => $dataTable,
            ];
    
        } catch (\Throwable $th) {
            return [ 'result' => $th ];
        }
    }

}
 