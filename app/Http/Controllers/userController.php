<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use File;
use Response;
use App\Http\Controllers\periodsController;
use App\Http\Controllers\routeGlobal;
use App\Http\Controllers\rewardsProgram\walletController;


class userController extends Controller
{
    //
    protected $periodsController;
    protected $routeGlobal;
    protected $walletController;
    public $date;

    public function __construct(periodsController $periodsController, routeGlobal $routeGlobal, walletController $walletController)
    { 
        $this->periodsController = $periodsController;
        $this->walletController = $walletController;
        $this->routeGlobal = $routeGlobal;
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }

    public function index() {

        $users = DB::table('users')
        ->first();
        // dd($users->name);
        //dd($users);
        $user = [
            'name' => $users->name,
            'email' => $users->email
        ];

        return $user;
    }

    public function login(Request $request) {

        try {
            //code...
            $user = DB::table('users')
            ->where('email',$request->correo)
            ->where('password', md5($request->pass))
            ->first();
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            
            if ($user){
                if($user->status == 18){
    
                    $json = [
                        "user" => $user,
                    ];
        
                    if($date >= $user->tokenExpiration){
        
                        DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'tokenExpiration' => $date->add(1, 'month'),
                        ]);
                    }
        
        
                    // llamar a otro controllador para traer el periodo dependiendo de la fecha
                    // $period = $this->periodsController->index($date, $request->promo_id);
                }else{
                    $json = [
                        "message" => 'userInactive',
                    ];
                }
    
            }else{
                $json = [
                    "message" => 'nada',
                ];
            }
            
            // echo json_encode($json);
            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            return ['result' => $th];
        }

    }

    public function loginSocial(Request $request) {

        $user = DB::table('users')
        ->where('email',$request->correo)
        ->where('providerSocial', $request->providerSocial)
        ->where('idSocial', $request->idSocial)
        ->first();
		
        if ($user){
            
            if($user->status == 18){

                $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
                // llamar a otro controllador para traer el periodo dependiendo de la fecha
                $period = $this->periodsController->index($date, $request->promo_id);
    
    
                if(!$user->imageUrl){
                    $image = "https:".str_replace(array('*', '$', 'questionFa'), array('=', '/', '?'), $request->image);
        
                    DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'imageUrl' => $image,
                        'updated_at' => $date,
                    ]);
                }

                $user = DB::table('users')
                ->where('id', $user->id)
                ->first();
                
                $json = [
                    "user" => $user,
                ];
            }else{
                $json = [
                    "message" => 'userInactive',
                ];
            }

           

        }else{
            $json = [
                "message" => 'nada',
            ];
        }
        
        //echo json_encode($json);
        return $json;

    }

    public function register(Request $request) {

    
        try {

            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            $dateValid = Carbon::createFromFormat('Y-m-d H:i:s', '2021-02-01 00:00:00');
            $validDate = $this->validDate($date, $dateValid);
            $tokenExpiration = $date->add(2, 'month');
            // $validDate = true;

            if(!$validDate){
                $json = [
                    "message" => 'La fecha para participar en la app es apartir del ' . $dateValid->isoFormat('DD/MM/Y'),
                    "result" => 'dateInvalid'
                ];

                return $json;
            }else{
                
                $user = DB::table('users')
                ->where('email', $request->email)
                ->first();
    
                if(!$user){
                    $user = DB::table('users')
                    ->where('nickName', $request->nickName)
                    ->first();
                }
                
                if (!$user){
    
                    // return 'if';
                    $token = Str::random(30);
                    $validToken = true;
    
                    while (!$validToken) {
                        $user = DB::table('users')
                        ->where('token', $token)
                        ->first();
    
                        if($user){
                            $token = Str::random(30);
                        }else{
                            $validToken = false;
                        }
                    }
    
                    $birthdate = Carbon::parse($request->birthdate, 'UTC');
                    
                    $notifications = DB::table('avisos')
                    ->first();

                    if($notifications){
                        $validExistNotifications = 1;
                    }else{
                        $validExistNotifications = 0;
                    }

                    DB::table('users')
                    ->insert([
                        'name' => $request->name ,
                        'email' => $request->email, 
                        'password' => md5($request->password),
                        'token' => $token,
                        // 'country' => $request->country,
                        // 'cp' => $request->cp,
                        // 'street' => $request->street,
                        // 'suburb' => $request->suburb,
                        // 'city' => $request->city,
                        // 'state' => $request->state,
                        'cellPhone' => $request->cellPhone,
                        'birthdate' => $birthdate,
                        'nickName' => $request->nickName,
                        'state_id' => 1,
                        'status' => 18,
                        'version_id' => 0,
                        'notifications' => $validExistNotifications,
                        'tokenExpiration' => $tokenExpiration,
                        'validFriends' => 1,
                        'created_at' => $this->date,
                        'updated_at' => $this->date,
                    ]);
                    
                    $user = DB::table('users')
                    ->where('email', $request->email)
                    ->first();

                    $this->walletController->registerWallet($user->id, $this->date);
                    
                    if($request->img){
                        
                        $data = explode( ',', $request->img );                    
                        $pathP = 'img/profile/'.'profile-'.$token.'.'.$request->extension;
                        
                        $content = base64_decode($data[1]);
                        $typeFile = Str::substr($data[0],0,10);
                        if($typeFile == 'data:image' ){

                            $pathP = 'img/profile/'.'profile-'.$request->token.'.'.$request->extension;
                            $pathResize = 'img/profileResize/'.'profile-'.$request->token.'.'.$request->extension;

                            // Guardar Imagen normal
                            $content = base64_decode($data[1]);
                            Storage::disk('public')->put( $pathP, $content);
        
                            // Guardar Imagen mas pequeña
                            $resized_image = Image::make($content)->resize(300, 200)->stream($request->extension, 100);
                            Storage::disk('public')->put( $pathResize, $resized_image);
        
                            // Guarda imagen en la carpeta public
                            // Storage::disk('assets')->put( $pathP, $content);
        
                            $routeGlobal = $this->routeGlobal->index();
                            $path = $routeGlobal.$pathP;
                            $pathRe = $routeGlobal.$pathResize;

                            // $path = "http://trivia.demo:8000/storage/app/public/".$pathP;
                            DB::table('users')
                            ->where('users.id', $user->id)
                            ->update([
                                'image' => $pathP,
                                'imageResize' => $pathResize,
                                'imageUrl' => $path,
                                'imageResizeUrl' => $pathRe,
                            ]);
                        }else{
                            $json = [
                                "message" => 'Error',
                                "result" => 'imageNonValid'
                            ];
                        }
                    }
                    

                    // return $request->campaign;
                    if($request->campaign){
                        $campaign = DB::table('campaign')
                        ->where('slug', $request->campaign)
                        ->first();

                        DB::table('campaign')
                        ->where('id', $campaign->id)
                        ->update(['registers' => $campaign->registers + 1]);
                    }

                    
    
                    $json = [
                        "user" => $user
                    ];
                }else{
                    if($user->email == $request->email){
                        $message = "El correo que desea registrar ya se encuentra dado de alta en nuestro sistema";
                        $result = 'email-novalid';
                    } else{
                        $message = "El nombre de usuario que desea registrar ya se encuentra dado de alta en nuestro sistema";
                        $result = 'nickname-novalid';
                    }
                    $json = [
                        "message" => $message,
                        "result" => $result
                    ];
                }
                return $json;
            }
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                "message" => $th,
            ];
            return $json;
        }

    }

    public function registerSocial(Request $request) {

    
        try {

            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            $dateValid = Carbon::createFromFormat('Y-m-d H:i:s', '2021-02-01 00:00:00');
            $validDate = $this->validDate($date, $dateValid);

            if(!$validDate){
                $json = [
                    "message" => 'La fecha para participar en la app es el '.$dateValid,
                    "result" => 'dateInvalid'
                ];

                return $json;
            }else{

                $user = DB::table('users')
                    ->where('email', $request->email)
                    ->first();
    
                if(!$user){
                    $user = DB::table('users')
                    ->where('nickName', $request->nickName)
                    ->first();
                }

                if (!$user){
    
                    $token = Str::random(30);
                    $provider = $request->providerSocial;
                    $validToken = true;
    
                    while (!$validToken) {
                        $user = DB::table('users')
                        ->where('token', $token)
                        ->first();
    
                        if($user){
                            $token = Str::random(30);
                        }else{
                            $validToken = false;
                        }
                    }
    
                    $birthdate = Carbon::parse($request->birthdate, 'UTC');
                    
                    // if($request->providerSocial == 'GOOGLE'){
                    //     $image = "https:".str_replace(array('*', ' '), array('=', '/'), $request->image);
                    // }else{
                    //     $image = $request->image;
                    // }
                    $image = "https:".str_replace(array('*', '$', 'questionFa'), array('=', '/', '?'), $request->image);
                    $tokenExpiration = $date->add(2, 'month');


                    DB::table('users')
                    ->insert([
                        [   'name'=> $request->name ,
                            'email' => $request->email, 
                            'token' => $token,
                            // 'country' => $request->country,
                            // 'cp' => $request->cp,
                            // 'street' => $request->street,
                            // 'suburb' => $request->suburb,
                            // 'city' => $request->city,
                            // 'state' => $request->state,
                            'cellPhone' => $request->cellPhone,
                            'birthdate' => $birthdate,
                            'nickName' => $request->nickName,
                            'image' => $image,
                            'imageResize' => $image,
                            'imageUrl' => $image,
                            'imageResizeUrl' => $image,
                            'providerSocial' => $request->providerSocial,
                            'idSocial' => $request->idSocial,
                            'state_id' => $request->state_id,
                            'notifications' => 0,
                            'status' => 18,
                            'created_at' => $this->date,
                            'updated_at' => $this->date,
                            'tokenExpiration' => $tokenExpiration ,
                            'validFriends' => 1,
                        ],
                    ]);
                    
                    $user = DB::table('users')
                    ->where('email', $request->email)
                    ->first();
        
                    $period = DB::table('periods')
                    ->where("inicial_date",'<=',$this->date->format('Y-m-d'))
                    ->where("final_date",'>=',$this->date->format('Y-m-d'))
                    ->first();
    
                    if (!$period){
                        $period = DB::table('periods')
                        ->orderBy('id', 'desc')
                        ->first();
                    }

                    $this->walletController->registerWallet($user->id, $this->date);
                    
                    DB::table('periods_score')
                    ->insert([
                        [   
                            'user_id'=> $user->id,
                            'period_id'=> $period->id,
                            'pending_score' => 0,
                            'score' => 0,
                            'created_at' => $this->date,
                            'updated_at' => $this->date,
                        ],
                    ]);
    
                    $json = [
                        "user" => $user
                    ];
                }else{

                    if($user->email == $request->email){
                        $message = "El correo que desea registrar ya se encuentra dado de alta en nuestro sistema";
                        $result = 'email-novalid';
                    } else{
                        $message = "El nombre de usuario que desea registrar ya se encuentra dado de alta en nuestro sistema";
                        $result = 'nickname-novalid';
                    }
                    $json = [
                        "message" => $message,
                        "result" => $result
                    ];
                    
                }
            }

            
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                "message" => $th,
            ];
        }
        
        //echo json_encode($json);

        return $json;
        

    }

    public function updateProfile(Request $request) {

    
        try {
            $user = DB::table('users')
                ->where('email', $request->email)
                ->where('token','<>', $request->token)
                ->first();

           if (!$user){
                
                $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
                $birthdate = Carbon::parse($request->birthdate, 'UTC');
                // echo json_encode($birthdate);
                
                // return;
                if($request->img){

                    
					$user = DB::table('users')
					->where('token', $request->token)
					->first();
					
                    $data = explode( ',', $request->img );   
                    $typeFile = Str::substr($data[0],0,10);
                    if($typeFile == 'data:image' ){
                        
                        $pathP = 'img/profile/'.'profile-'.$request->token.'.'.$request->extension;
                        $pathResize = 'img/profileResize/'.'profile-'.$request->token.'.'.$request->extension;
                        
    
                        if (Storage::disk('public')->exists($user->imageResize)){
                            Storage::disk('public')->delete($user->imageResize);
                        }
    
                        if (Storage::disk('public')->exists($user->image)){
                            Storage::disk('public')->delete($user->image);
                        }
                        
                        $content = base64_decode($data[1]);
                        Storage::disk('public')->put( $pathP, $content);
    
    
                        $resized_image = Image::make($content)->resize(300, 200)->stream($request->extension, 100);
    
                        Storage::disk('public')->put( $pathResize, $resized_image);
    
                        // Guarda imagen en la carpeta public
                        // Storage::disk('assets')->put( $pathP, $content);
    
                        $routeGlobal = $this->routeGlobal->index();
                        $path = $routeGlobal.$pathP;
                        $pathRe = $routeGlobal.$pathResize;
    
                        DB::table('users')
                        ->where('token', $request->token)
                        ->update(['name'=> $request->name,
                            // 'email' => $request->email, 
                            'country' => $request->country,
                            'cp' => $request->cp,
                            'cellPhone' => $request->cellPhone,
                            'birthdate' => $birthdate,
                            'state_id' => $request->state_id,
                            // 'nickName' => $request->nickName,
                            'updated_at' => $date,
                            'image' => $pathP,
                            'imageResize' => $pathResize,
                            'imageUrl' => $path,
                            'imageResizeUrl' => $pathRe,
                            'updated_at' => $date
                        ]);
                    }else{
                        $json = [
                            "message" => 'Error',
                        ];
                        return $json;
                    }

                }else{

                    DB::table('users')
                    ->where('token', $request->token)
                    ->update(
                        [   'name'=> $request->name ,
                            'email' => $request->email, 
                            'country' => $request->country,
                            'cp' => $request->cp,
                            'cellPhone' => $request->cellPhone,
                            'state_id' => $request->state_id,
                            'birthdate' => $birthdate,
                            // 'nickName' => $request->nickName,
                            'updated_at' => $date,
                        ]);
                }

                $user = DB::table('users')
                ->where('token', $request->token)
                ->first();
    

                $json = [
                    "user" => 1,
                    "image" => $user->imageUrl,
                    "name" => $user->name,
                    "nickName" => $user->nickName,
                    "cellPhone" => $user->cellPhone,
                    "email" => $user->email,
                    "stateUser" => $user->state_id,
                    "country" => $user->country,
                    "cp" => $user->cp,
                    "birthdate" => $user->birthdate,
                ];
            }else{
                $json = [
                    "message" => 'El correo que desea registrar ya se encuentra dado de alta en nuesto sistema',
                ];
            }

            return $json;
        } catch (\Throwable $th) {
            $json = [
                "message" => $th,
            ];

            return $json;
        }

    }

    public function validExistsUserSocial(Request $request){
        try {

            $user = DB::table('users')
                ->where('email', $request->emailSocial)
                ->first();

            if (!$user){

                $message = "register";

            }else{

                if($user->providerSocial == $request->providerSocial){
                    $message = "login";
                }else{
                    $message = "El correo con el que desea ingresar, ya se encuentra dado de alta en nuestro sistema, Favor de intertar con otra opción de logueo";
                }
            }
            $json = [
                "message" => $message
            ];

            //echo json_encode($json);
    
            return $json;
            
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                "message" => $th,
            ];
            //echo json_encode($json);
    
            return $json;
        }
        
    }

    public function referUser(Request $request){

        $userRefer = DB::table('users')
        ->where("nickName",$request->nickName)
        ->first();

        if($userRefer){
            $json = [
                "user" => $userRefer,
                'message' => 'ok'
            ];
        }else{
            $json = [
                'message' => 'invalidUser'
            ];
        }
        //echo json_encode($json);

        return $json;
    }

    public function referUserFriends(Request $request){

        try {
            $userRefer = DB::table('users')
            ->where("users.nickName",$request->nickName)
            ->first();

            if($userRefer){
                $refersFriends = DB::table('refers')
                ->leftJoin('status_catalog', 'refers.status', '=', 'status_catalog.id')
                ->leftJoin('users', 'refers.user_id', '=', 'users.id')
                ->select(
                    'status_catalog.name as statusName',
                    'status_catalog.id as statusId', 
                    'users.nickName as nickName', 
                    'users.imageUrl as image',
                    'users.id as user_id'
                )
                ->where("refers.id_refer",$userRefer->id)
                ->where("refers.promo_id",$request->promo_id)
                ->get();

                $arrUsers = [];
                foreach ($refersFriends as $key => $refersFriend) {
        
                    $arrUser = [
                        "image" => $refersFriend->image,
                        'nickName' => $refersFriend->nickName,
                        'user_id' => $refersFriend->user_id, 
                        'status' => $refersFriend->statusName,
						'statusId' => $refersFriend->statusId
                    ];
        
                    array_push($arrUsers, $arrUser);
                    // $arrTickets.push($arrTicket);
                    
                }
        
                $json = [
                    "refers" => $arrUsers,
                    'message' => 'ok'
                ];

            }else{
                $json = [
                    'message' => 'invalidUser'
                ];
            }
            //echo json_encode($json);
    
            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                'message' => $th
            ];
            return $json;
        }
        return $json;

    }

    public function profile(Request $request){

        $user = DB::table('users')
            ->where('token',$request->token)
            ->first();

        $states = DB::table('states')
                ->where('promo_id', $request->promo_id)
                ->get();

        $base64 = $user->imageUrl;

        $json = [
            "user" => 1,
            "image" => $base64,
            "name" => $user->name,
            "nickName" => $user->nickName,
            "cellPhone" => $user->cellPhone,
            "email" => $user->email,
            "country" => $user->country,
            "state" => $user->state_id,
            "cp" => $user->cp,
            "stateUser" => $user->state_id,
            "birthdate" => $user->birthdate,
            "states" => $states,
        ];

        return $json;
    }

    public function insertRefer(Request $request){

        try {
            //code...
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));

            DB::table('refers')->insert([
                'user_id' => $request->user,
                'id_refer' => $request->refer,
                'status' => 3,
                'created_at' => $date,
                'updated_at' => $date,
                'promo_id' => $request->promo_id,
            ]);
    
            $refer = DB::table('refers')
            ->where('user_id', $request->user)
            ->orderBy('id', 'DESC')
            ->first();
    
            $period = DB::table('periods')
            ->where("inicial_date",'<=',$this->date->format('Y-m-d'))
            ->where("final_date",'>=',$this->date->format('Y-m-d'))
            ->first();
    
            // $scoreRefere = DB::table('periods_score')
            // ->where('user_id', $refer->id_refer)
            // ->where('period_id', $period->id)
            // ->first();
    
            // if($scoreRefere){
    
            //     $pointsScoreRefere = $scoreRefere->score + 1;
    
            //     DB::table('periods_score')
            //     ->where('id', $scoreRefere->id)
            //     ->update(['score' => $pointsScoreRefere , 'updated_at' => $this->date]);
            // }else{
                
            //     DB::table('periods_score')
            //     ->insert([
            //         [   'user_id'=> $refer->id_refer,
            //             'period_id'=> $period->id,
            //             'score' => 1,
            //             'created_at' => $date,
            //             'updated_at' => $date
            //         ],
            //     ]);
    
            //     $scoreRefere = DB::table('periods_score')
            //     ->where('user_id', $refer->id_refer)
            //     ->where('period_id', $period->id)
            //     ->first();
            // }
    
            DB::table('extra_points')
            ->insert([
                'user_id' => $refer->id_refer,
                'period_id' => $period->id,
                'type_id' => 1,
                'status'  => 6,
                'points' => 1,
                'refer_or_minigames_id' => $refer->id,
            ]);
    
            // DB::table('refers')
            // ->where('user_id', $request->user)
            // ->where('status',3)
            // ->update(['status' => 5, 'updated_at' => $date]);

            return [
                'message' => 'ok'
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'message' => $th
            ];
        }


    }

    public function getUserInfoScore(Request $request){
        try {
            //code...
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    
            // llamar a otro controllador para traer el periodo dependiendo de la fecha
            $period = $this->periodsController->index($date,$request->promo_id);

            $user = DB::table('users')
            ->where('token',$request->token)
            ->first();

            if($user){
                if($user->tokenExpiration <= $date){
                    
                    $json = [
                        'message' => 'tokenExpired'
                    ];

                }else{
                    
                    $score = DB::table('periods_score')
                    ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
                    ->where('user_id', $user->id)
                    ->where('period_id', $period->id)
                    ->first();
        
                    if(!$score){
                        DB::table('periods_score')
                        ->insert([
                            [   
                                'user_id'=> $user->id ,
                                'period_id'=> $period->id,
                                'score' => 0,
                                'pending_score' => 0,
                                'created_at' => $date,
                                'updated_at' => $date,
                            ],
                        ]);
        
                        $score = DB::table('periods_score')
                        ->where('user_id', $user->id)
                        ->where('period_id', $period->id)
                        ->first();
                    }
            
                    $user = DB::table('users')
                    ->join('periods_score', 'users.id', '=', 'periods_score.user_id')
                    ->select(
                        'users.id', 
                        'users.nickName as nickName', 
                        'users.name as userName' , 
                        'users.state_id as stateUser', 
                        'users.imageUrl as image' , 
                        'periods_score.score as periodScore', 
                        'users.notifications',
                        'users.validFriends',
                    )
                    ->where('token',$request->token)
                    ->where('period_id',$period->id)
                    ->first();
                    
        
                    $refers = DB::table('refers')
                    ->select(DB::raw('COUNT(id) as refers'))
                    ->where('id_refer',$user->id)
                    // ->where('promo_id',$request->promo_id)
                    ->first();
                    
                    if($refers){
                        $referScore = $refers->refers;
                    }else{
                        $referScore = 0;
                    }

                    $orders = DB::table('orders')
                    ->select(DB::raw('COUNT(id) as orders'))
                    ->where('user_id',$user->id)
                    // ->where('promo_id',$request->promo_id)
                    ->first();

                    if ($orders) {
                        $totalOrders = $orders->orders;
                    }else{
                        $totalOrders = 0;
                    }
        
        
                    $base64 = $user->image;
        
                    $states = DB::table('states')
                    ->where('promo_id', $request->promo_id)
                    ->get();

                    $promos = DB::table('promo')
                    ->where('type_id', '=', 5)
                    ->whereIn('status_id', [24, 38])
                    ->orderBy('promo.id', 'DESC')
                    ->get();

                    $balance = $this->walletController->getUserBalance($request->token);
            
                    $routeGlobal = $this->routeGlobal->index();
            
                    $json = [
                        "userId" => $user->id,
                        "image" => $base64,
                        "nickName" => $user->nickName,
                        "userName" => $user->userName,
                        "refers" => $referScore,
                        'totalOrders' => $totalOrders,
                        "score" => $score->score,
                        "stateUser" => $user->stateUser,
                        "notifications" => $user->notifications,
                        'validFriends' => $user->validFriends,
                        "states" => $states,
                        "pending_score" =>  $score->pending_score,
                        'promos' => $promos,
                        'routeGlobal' => $routeGlobal,
                        'balance' => $balance['balance']->balance,
                    ];

                }
            }else{
                $json = [
                    "message" => 'nonUser',
                ];
            }

            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                "message" => $th,
            ];
            //echo json_encode($json);
            return $json;
        }

    }

    public function topList(Request $request) {
        try {
            
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            $period = $this->periodsController->index($date, $request->promo_id);

            $users = DB::table('periods_score')
            ->LeftJoin('users', 'periods_score.user_id', '=', 'users.id')
            ->LeftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
            ->LeftJoin('promo', 'periods.promo_id', '=', 'promo.id')
            ->select(
                'users.nickName as nickName',
                'users.id as user_id',
                'users.imageResizeUrl as userImage'
            )
            ->where([
                ['periods_score.period_id', '=',$period->id],
                // ['promo.status_id', '=',24],
            ])
            ->orderBy('periods_score.score','desc')
            ->limit(20)
            ->get();
            // dd($period->id);
                
            $arrUsers = [];
            foreach ($users as $key => $user) {
                
    
                $arrUser = [
                    "image" => $user->userImage,
                    'nickName' => $user->nickName,
                    'user_id' => $user->user_id, 
                ];
    
                array_push($arrUsers, $arrUser);

            }

            $valperiod_id = true;
            if($date > $period->final_date){
                $valperiod_id = null;
            }
    
            $json = [
                "users" => $arrUsers,
                'periodId' => $period->id,
                "numPeriod" => $period->order,
                "inicial_date" => date('d/m/Y', strtotime($period->inicial_date)),
                "final_date" => date('d/m/Y', strtotime($period->final_date)),
                "valperiod_id" => $valperiod_id,
            ];
    
            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                "err" => $th,
            ];
    
            // echo json_encode($json);
            return $json;
        }
    }

    public function activate($id,$token){
        //dd($token.$id);
        $token_user = DB::table('token_user')->where('user_id',$id)->get()->first();
        $dateNow = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
        $user = DB::table('users')->where('id',$id)->get()->first();
        //dd($user);
        if(!empty($token_user)){
            if($token_user->token === $token and $dateNow < $token_user->expiration_at){
                //dd('si');
                DB::table('users')
                  ->where('id', $token_user->user_id)
                  ->update(['status' => 1, 'updated_at' => $dateNow]
                );
    
                DB::table('token_user')
                ->where('id', $token_user->id)
                ->delete();
    
            
                return redirect()->route('congrats-userActivate');
            }else{
                return redirect()->route('user-nonActivate',['message'=>'invalidToken','name' => $user->name,
                'email' => $user->email,'id'=>$id]);
            }
        }else{
            return redirect()->route('user-nonActivate',['message'=>'invalidToken','name' => $user->name,
            'email' => $user->email,'id'=>$id]);
        }
    }

    public function validDate($date, $dateValid ){

        if($dateValid > $date){
            return false;
        }
        return true;
    }

    public function resetPassword($id, $token){

        try {
            $user = DB::table('users')
            ->where('id',$id)
            ->first();
            // dd($user);
    
            if($user){
                $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    
                $token_user = DB::table('token_users')
                ->where('user_id',$user->id)
                ->where('token',$token)
                ->first();
    
                if($token_user){
                    if($date > $token_user->expiration_at){
                        // dd('fecha pasada');
                        $json = [
                            'message' => 'dateInvalid'
                        ];
                    }else{
                        $json = [
                            'message' => 'ok',
                            'userId' => $user->id,
                            'userEmail' => $user->email,
                            'tokenUser' => $user->token,
                            'tokenResetPass' => $token_user->token
                        ];
                        // return view('user.resetPass',['userName' => $user->name, 'userToken' => $user->token]);
                    }
                }else{
                    // dd('Token incorrect');
                    $json = [
                        'message' => 'tokeninvalid'
                    ];
                }
                return $json;
            }
        } catch (\Throwable $th) {
            $json = [
                'message' => $th
            ];
            return $json;
        }

    }

    public function changePassword(Request $request){
        
        $token = $request->token;
        $password = md5($request->password);
        $token_user = DB::table('token_users')
        ->where('token',$token)
        ->first();

        if($token_user){
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            if($date < $token_user->expiration_at){

                DB::table('users')
                ->where('id', $token_user->user_id)
                ->update(['password'=> $password ,
                'updated_at' => $date
                ]);
                $message = 'ok';
            }else{
                $message = 'dateInvalid';
            }
        }else{
            $message = 'tokenInvalid';
        }
        $json = [
            'message' => $message
        ];
        return $json;

        // return redirect()->route('success-Password');
    }
    
    public function successPass(){
        return view('user.successPass');

    }

    public function validVersion(Request $request){
        try {
            //code...
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    
            $user = DB::table('users')
            ->where('token',$request->token)
            ->first();

            // print_r($user->version_id);
            // die();

            $version =  DB::table('versions')
            ->orderBy('id', 'desc')
            ->first();

            if($user->version_id and ($user->version_id == $version->id)){
                $message = 'ok';
            }else{
                DB::table('users')
                ->where('id', $user->id)
                ->update(
                    ['version_id' => $version->id, 'updated_at' => $date]
                );
                $message = 'change';
            }
            $json = [
                'message' => $message
            ];
            return $json;

        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                'message' => $th
            ];
            return $json;
        }
    }

    public function validFriend(Request $request){
        try {
            //code...
            DB::table('users')
            ->where('id', $request->userId)
            ->update([
                'validFriends' => 0
            ]);

            $json = [
                'result' => $request->userId,
            ];
            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                'result' => $th,
            ];
            return $json;
        }
    }

    public function getAddressUser($user_id){
        $user = DB::table('users')
        ->where('id',$user_id)
        ->first();

        return [
            'country' => $user->country,
            'zip' => $user->cp,
            'street' => $user->street,
            'suburb' => $user->suburb,
            'city' => $user->city,
            'state' => $user->state,
        ];

    }
    
}
