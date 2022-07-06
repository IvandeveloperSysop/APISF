<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mail; //Importante incluir la clase Mail, que será la encargada del envío
use App\Http\Controllers\routeGlobal;

class emailController extends Controller
{

    public $date;
    protected $routeGlobal;
    public function __construct( routeGlobal $routeGlobal){
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
        $this->routeGlobal = $routeGlobal->index();
    }


    public function changePasswordUser(Request $request){

        try {
            $user = DB::table('users')
            ->where('email',$request->email)
            ->first();

            if($user){

                $var = Str::random(32);
                $date =Carbon::now(new \DateTimeZone('AMERICA/Monterrey'))->addMinutes(intval(60));

                $tokenUser = DB::table('token_users')
                ->where('user_id', $user->id)
                ->first();

                // Eliminar token anterior de usuario
                if($tokenUser){
                    DB::table('token_users')
                    ->where('user_id', $user->id)
                    ->delete();
                }

                DB::table('token_users')->insert([
                    ['user_id' => $user->id, 'token' => $var,'expiration_at' => $date, 'created_at' => $date, 'updated_at' => $date,]
                ]);

                $subject = "Cambio de contraseña";
                $for = $user->email;
                $from = env('MAIL_USERNAME');


                $data = [
                    'name'=>$user->name,
                    'email'=>$for,
                    'token'=>$var,
                    'user_id'=>$user->id,
                    'dateExpirate'=>$date->isoFormat('MMMM Do YYYY, h:mm:ss a'),
                ];

                Mail::send('emails.resetPassword',$data, function($msj) use($subject,$for,$from){
                    $msj->from($from,"Desafio Soccer FAN");
                    $msj->subject($subject);
                    $msj->to($for);
                });

                $json = [
                    "message" => 'ok'
                ];
            }else{
                $json = [
                    "message" => 'nonEmail'
                ];
            }

            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                "message" => $th,
                "error" => 'error'
            ];

            return $json;
        }

    }

    public function buyProductEmail($order){

        try {

            // return[$order->email];
            $subject = "Compra de producto";
            $for = $order->email;
            $from = env('MAIL_USERNAME');
            $data = [
                'routeGlobal' => $this->routeGlobal,
                'imageProduct' => $order->imageProduct,
                'productName' => $order->productName,
                'userName' => $order->userName,
                'email' => $order->email,
                'price' => $order->price,
                'statusName' => $order->statusName,
                'street' => $order->street,
                'city' => $order->city,
                'zip' => $order->zip,
                'suburb' => $order->suburb,
                'state' => $order->state,
                'cellphone' => $order->cellPhone,
            ];

            if ($order->statusId == 35) {
                $email = 'emails.buyProduct';
            } elseif( $order->statusId == 36 ) { // confirm
                $email = 'emails.orderConfirm';
            }elseif( $order->statusId == 37 ) { // cancel
                $email = 'emails.orderCancel';
            }elseif( $order->statusId == 39 ) { // in coming
                $email = 'emails.orderComing';
            }elseif( $order->statusId == 40 ) { // Delivery
                $email = 'emails.orderDelivery';
            }

            Mail::send($email,$data, function($msj) use($subject,$for,$from){
                $msj->from($from,"Desafio Soccer FAN");
                $msj->subject($subject);
                $msj->to($for);
            });


            return [
                'result' => 'ok'
            ];

        } catch (\Throwable $th) {

            return [
                'result' => $th
            ];

        }


    }

    public function productEmpty($award){

        try {
            $subject = "";
            $for = 'ivansysopdesarrollo@gmail.com';
            $from = env('MAIL_USERNAME');
            $data = [
                'routeGlobal' => $this->routeGlobal,
                'imageProduct' => $award->image,
                'productName' => $award->name,
            ];


            Mail::send('emails.productEmpty',$data, function($msj) use($subject,$for,$from){
                $msj->from($from,"Programa de recompensas");
                $msj->subject($subject);
                $msj->to($for);
            });


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
