<?php

namespace App\Http\Controllers\rewardsProgram;

// cada vez que se cambia el directorio del controllador se necesita importar controller para poder ser llamado 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Session;


class bonusController extends Controller
{
    
    public function addBonus(Request $request){

        try {
            //code...
            $promo = DB::table('promo')
            ->where('promo.id', $request->promoId)
            ->first();

            // Carbon::parse($request->fecha, 'UTC');
            $begins_at = Carbon::parse($request->begins_at);
            $ends_at = Carbon::parse($request->ends_at);

            // return [
            //     gettype($request->bono3months)
            // ];

            if($request->bono3months == 'true'){
                for ($i=0; $i < 12 ; $i++) { 
                    
                    DB::table('bonus')
                    ->insert([
                        'title' => $request->bonusName,
                        'description' => $request->bonusDescriptión,
                        'multiplier' => $request->multiplier,
                        'begins_at' => $begins_at,
                        'ends_at' => $ends_at,
                        'promo_id' => $promo->id,
                        'status_id' => 30,
                    ]);
    
                    $begins_at->addWeek();
                    $ends_at->addWeek();
                }

            }else{
                DB::table('bonus')
                ->insert([
                    'title' => $request->bonusName,
                    'description' => $request->bonusDescriptión,
                    'multiplier' => $request->multiplier,
                    'begins_at' => $begins_at,
                    'ends_at' => $ends_at,
                    'promo_id' => $promo->id,
                    'status_id' => 30,
                ]);
            }

    

            $table = $this->drawTableBonus($promo->id);
    
            
    
            $result = 'ok';

            return [
                'result' => $result,
                'table' => $table,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }



    }

    public function getBonus($id, Request $request){

        try {
            //code...
            $bonus = DB::table('bonus')
            ->select(
                'bonus.id',
                'bonus.title',
                DB::raw('DATE_FORMAT(bonus.begins_at, "%Y-%m-%d %H:%i") as begins_at'),
                DB::raw('DATE_FORMAT(bonus.ends_at, "%Y-%m-%d %H:%i") as ends_at'),
                'bonus.description',
                'bonus.multiplier',
            )
            ->where([
                ['bonus.id', '=', $id],
                ['bonus.status_id', '=', 30],
            ])
            ->first();
    
            return [
                'bonus' => $bonus,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th
            ];
        }


    }

    public function updateBonus(Request $request){

        try {

            // return [$request->all()];

            if($request->begins_at < $request->ends_at){

                DB::table('bonus')
                ->where('bonus.id','=', $request->bonus_id)
                ->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'multiplier' => $request->multiplier,
                    'begins_at' => $request->begins_at,
                    'ends_at' => $request->ends_at,
                ]);

                $result = 'ok';
            }else{
                $result = 'invalidDates';
            }


            $bonus = DB::table('bonus')
            ->where('bonus.id','=', $request->bonus_id)
            ->first();
            
            $table = $this->drawTableBonus($bonus->promo_id);
            return[
                'result' => $result,
                'table' => $table,
            ];



        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }

    }

    public function downBonus( $id, Request $request ){
        // return [
        //     'bonus_id' => $id
        // ];

        try {

            DB::table('bonus')
            ->where('bonus.id', $id)
            ->update([
                'status_id' => 31
            ]);

            $result = 'ok';

            $bonus = DB::table('bonus')
            ->where('bonus.id','=', $id)
            ->first();

            $table = $this->drawTableBonus($bonus->promo_id);

            return [
                'result' => $result,
                'table' => $table,
            ];

        } catch (\Throwable $th) {
            return [
                'result' => $th
            ];
        }
    }

    public function drawTableBonus($promo_id){
        try {
            //code...
            
                    $bonus = DB::table('bonus')
                    ->select(
                        'bonus.id',
                        'bonus.title',
                        'bonus.begins_at',
                        'bonus.ends_at',
                        'bonus.description',
                        'bonus.multiplier',
                    )
                    ->where([
                        ['promo_id', '=', $promo_id],
                        ['bonus.status_id', '=', 30],    
                    ])
                    ->get();
            
                    $table = "";
            
                    $window = 'details';

                    foreach ($bonus as $key => $bono) {
                        $table = $table . "<tr>
                            <td>x $bono->multiplier </td>
                            <td>". Carbon::parse( $bono->begins_at)->format('d-m-Y h:i:s') ."</td>
                            <td>". Carbon::parse( $bono->ends_at)->format('d-m-Y h:i:s') ."</td>
                            <td>
                                <button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#viewDetailsBonus' onclick='getBonus( $bono->id )' >
                                    <i class='fas fa-eye me-2'></i>Ver detalles
                                </button>
            
                                <button type='button' class='btn btn-danger' onclick='deleteBonus( $bono->id ,".'"'.$window.'"'.")' >
                                    <i class='fas fa-trash me-2'></i>Dar de baja
                                </button>
                            </td>
                        </tr>";
                    }
            
                    return $table;
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

    }

}
