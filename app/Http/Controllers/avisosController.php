<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Session;

class avisosController extends Controller
{
    //Mostrar a todos los avisos
    public function getAvisos(Request $request){

        $user = DB::table('users')
        ->where('token', $request->token)
        ->first();

        DB::table('users')
        ->where('id', $user->id)
        ->update(['notifications' => 0]);

        $user_id = $user->id;

        $avisos = DB::table('avisos')
        ->leftJoin('block_notices', function($join) use ($user_id)
        {
            $join->on('avisos.id', '=', 'block_notices.id_aviso');
            $join->on('block_notices.user_id','=',DB::raw("'".$user_id."'"));
        })
        ->select('avisos.id as idAviso', 
        'avisos.comment', 
        'avisos.title',
        DB::raw('DATE_FORMAT(avisos.date, "%d/%m/%Y") as date'), 
        'block_notices.id as idNoticesBlock', 
        'block_notices.user_id')
        ->orderBy('avisos.id', 'DESC')
        ->get();

        if(count($avisos) > 0){
            $json = [
                "message" => 'ok',
                "avisos" => $avisos
            ];
        }else{
            $json = [
                "message" => 'non',
            ];
        }

        return $json;
    }

    public function deleteAvisoUser(Request $request){
        try {
            //code...
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            $user = DB::table('users')
            ->where('token', $request->token)
            ->first();
    
            DB::table('block_notices')
            ->insert([
                'id_aviso' => $request->id_aviso,
                'user_id' => $user->id,
                'created_at' => $date,
                'updated_at' => $date
            ]);
            $json = [
                'message' => 'ok'
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
}
