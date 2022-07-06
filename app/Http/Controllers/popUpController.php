<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class popUpController extends Controller
{ 
    //
    public $date;
    public function __construct(routeGlobal $routeGlobal)
    { 
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }


    public function popUpAdmin(){

        $popUps = DB::table('home_pop_up as popUp')
        ->leftJoin('status_catalog', 'popUp.status_id', '=', 'status_catalog.id')
        ->select(
            'status_catalog.name as statusName',
            'status_catalog.id as statusId',
            'popUp.id as popId',
            'popUp.title',
            'popUp.created_at',
        )
        ->paginate(9);

        // dd($popUp);
        return view('admin.popUps.index',['popUps' => $popUps, ]);

    }

    public function addPopUpAdmin( Request $request ){


        $this->downPopUp();

        if($request->image){
            $nameImage = str_replace(' ', '', $request->title);
            $data = explode( ',', $request->image );
            $pathP = 'img/popUps/'.$nameImage.'.'.$request->extension;
            $content = base64_decode($data[1]);
            $typeFile = Str::substr($data[0],0,10);
            if($typeFile == 'data:image' ){
                Storage::disk('public')->put( $pathP, $content);
            }
        }
        
        DB::table('home_pop_up')
        ->insert([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $pathP,
            'status_id' => 28,
            'created_at' => $this->date,
            'updated_at' => $this->date,
        ]);
            

        $table = $this->drawPopUp();

        return [
            'result' => 'ok',
            'table' => $table,
        ];

    }

    public function updatePopUpAdmin( Request $request ){

        try {
            //code...
            DB::table('home_pop_up as popUp')
            ->where('popUp.id', $request->popId)
            ->update([
                'title' => $request->title,
                'content' => $request->content,
                'updated_at' => $this->date,
            ]);
                
            if($request->image){
                $nameImage = str_replace(' ', '', $request->title);
                $data = explode( ',', $request->image );
                $pathP = 'img/popUps/'.$nameImage.'.'.$request->extension;
                $content = base64_decode($data[1]);
                $typeFile = Str::substr($data[0],0,10);
                if($typeFile == 'data:image' ){
                    Storage::disk('public')->put( $pathP, $content);
    
                    DB::table('home_pop_up as popUp')
                    ->where('popUp.id', $request->popId)
                    ->update([
                        'image' => $pathP,
                    ]);
    
                }
            }

            $table = $this->drawPopUp();

            return [
                'result' => 'ok',
                'table' => $table,
            ];
        
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th
            ];
        }
    }

    public function updateStatusPopUp(Request $request){

        try {


            if( $request->status_id == 28 ){
                $this->downPopUp();
            }

            DB::table('home_pop_up as popUp')
            ->where('popUp.id', $request->popId)
            ->update([
                'status_id' => $request->status_id,
            ]);

            $table = $this->drawPopUp();

            return [
                'result' => 'ok',
                'table' => $table,
            ];

        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }
   }

   public function downPopUp(){

        DB::table('home_pop_up')
        ->where('status_id', 28)
        ->update([
            'status_id' => 29
        ]);
        return [
            'result' => 'ok',
        ];

   }

    public function getInfoPopUpAdmin(Request $request){

        $popUp = DB::table('home_pop_up as popUp')
        ->leftJoin('status_catalog', 'popUp.status_id', '=', 'status_catalog.id')
        ->select(
            'status_catalog.name as statusName',
            'popUp.id as popId',
            'popUp.title',
            'popUp.created_at',
            'popUp.content',
        )
        ->where('popUp.id', '=', $request->popId )
        ->first();

        return [
            'popUp' => $popUp
        ];

    }

    public function drawPopUp(){

        try {
            //code...
            $popUps = DB::table('home_pop_up as popUp')
            ->leftJoin('status_catalog', 'popUp.status_id', '=', 'status_catalog.id')
            ->select(
                'status_catalog.name as statusName',
                'status_catalog.id as statusId',
                'popUp.id as popId',
                'popUp.title',
                'popUp.created_at',
            )
            ->get();
    
            $table = "";
            foreach ($popUps as $key => $popUp) {
    
                if( $popUp->statusId == 29 ){
                    $textStatus = 'text-danger';

                    $button = "<button type='button' class='btn btn-success' onclick='deleteAward( $popUp->popId, 28)' >
                    <i class='far fa-check-circle me-2'></i>Activar mensaje
                    </button>";
                }else{
                    $textStatus = 'text-success';
                    $button = "<button type='button' class='btn btn-danger' onclick='updateStatusPopUp($popUp->popId , 29)' >
                        <i class='fas fa-trash me-2'></i>Dar de baja
                    </button>";
                }

                $table = $table . "<tr>
                    <td> $popUp->popId </td>
                    <td> $popUp->title </td>
                    <td class='$textStatus'> $popUp->statusName </td>
                    <td> $popUp->created_at </td>
                    <td> 
                        <button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#viewDetailsPopUp' onclick='getDetailsPopUp( $popUp->popId )' >
                            <i class='fas fa-eye me-2'></i>Ver detalles
                        </button>
                
                        $button

                    </td>
                </tr>";
    
            }
    
            return  $table;

        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }


    }

    // PWA
    public function getPopUp(Request $request){
        try {

            $popUp = DB::table('home_pop_up as popUp')
            ->leftJoin('status_catalog', 'popUp.status_id', '=', 'status_catalog.id')
            ->select(
                'status_catalog.name as statusName',
                'popUp.id as popId',
                'popUp.title',
                'popUp.created_at',
                'popUp.content',
                'popUp.image',
            )
            ->where('popUp.status_id', '=', 28 )
            ->first();
    
            if($popUp){
    
               $popUser =  DB::table('popup_users')
                ->where([
                    ['user_id', '=', $request->user_id],
                    ['date' ,'=', $this->date->format('Y-m-d')],
                    ['popUp_id' ,'=', $popUp->popId],
                ])
                ->first();
    
                if( $popUser ){
                    $result = 'dontShowMessage';
                    $popUp = $popUser;
                }else{
                    $result = 'ok';
                }
    
            }else{
                $result = 'notSearchPopUp';
            }
            
    
            return [
                'result' => $result,
                'popUp' => $popUp,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }


    }

    public function disablePopUp(Request $request){

        try {
            $user = DB::table('users')
            ->where([
                ['token', '=', $request->user_token]
            ])
            ->first();
    
            $popUp = DB::table('home_pop_up')
            ->where([
                ['status_id', '=', 28]
            ])
            ->orderBy('id', 'DESC')
            ->first();
    
            DB::table('popup_users')
            ->insert([
                'popUp_id' => $popUp->id,
                'user_id' => $user->id,
                'date' => $this->date,
            ]);

            return [
                'result' => 'ok'
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th
            ];
        }

    }

    public function insertPopUpUser(Request $request){

        try {
            
            DB::table('popup_users')
            ->insert([
                'popUp_id' => $request->pop_id,
                'user_id' => $request->user_id,
                'date' => $this->date,
            ]);

        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

}
