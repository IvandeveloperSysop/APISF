<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\routeGlobal;

class periodsController extends Controller
{
    //

    protected $routeGlobal;
    protected $date;
    public function __construct( routeGlobal $routeGlobal)
    {
        $this->routeGlobal = $routeGlobal->index();
        $this->date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }


    public function index($date,$promo_id,$pwa = false){
        try {

            if($date == 'non'){
                $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            }

            $promo = DB::table('promo')
            ->where([
                ['promo.id', '=', $promo_id],
            ])
            ->first();

         
            $period = DB::table('periods')
            ->where("inicial_date",'<=',$date->format('Y-m-d'))
            ->where("final_date",'>=',$date->format('Y-m-d'))
            ->where("promo_id",$promo_id)
            ->first();


            if(!$period){
                $period = DB::table('periods')
                ->orderBy('id', 'desc')
                ->first();
            }



            $date_initial = date('Y-m-d', strtotime($period->inicial_date));
            $final_date = date('Y-m-d', strtotime($period->final_date));
            if($date > $period->final_date){
                $date_initial = null;
                $final_date = null;
            }

            $promo_id = $period->promo_id;
            $period_id = $period->id;
                
            
    
            if($pwa){

                if ($promo->id == 1) {
                    $dateI = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'))->subDays(30);
                    $date_initial = $dateI->format('Y-m-d');
                    if($date > $period->final_date){
                        $date_initial = null;
                    }
                }

                $period = [
                    "inicial_date" =>  $date_initial,
                    "final_date" => $final_date,
                    "promo_id" => $promo_id,
                    "promo" => $promo,
                    'routeGlobal' => $this->routeGlobal,
                    "id" => $period_id,
                ];

                $json = [
                    "period" => $period,
                ];
    
                //echo json_encode($json);
                return $json;
    
            }
    
            return $period;

        } catch (\Throwable $th) {
            $json = [
                "message" => $th,
            ];

            //echo json_encode($json);

            return $json;
        }
    }

    public function create( $promotion ){

        try {
            //code...
            // return $request->all();
    
            $promo = DB::table('promo')
            ->select(
                'id',
                DB::raw('DATE_FORMAT(promo.begin_date, "%Y-%m-%d") as begin_date'),
                DB::raw('DATE_FORMAT(promo.end_date, "%Y-%m-%d") as end_date'),
            )
            ->where('promo.id', $promotion->id)
            ->first();
    

    
            
            $order = 1;

            
            if( $promo->begin_date < $promo->end_date  ){
                DB::table('periods')
                ->insert([
                    'promo_id' => $promo->id,
                    'order' => $order,
                    'inicial_date' => $promo->begin_date.' 00:00:00',
                    'final_date' => $promo->end_date.' 23:59:59',
                ]);
    
                // write the table in HTML
    
                $periods = DB::table('periods')
                ->where('periods.promo_id', $promo->id)
                ->get();
    
                $table = "";
    
                foreach ($periods as $key => $period) {
                    # code...
                    $table = $table . "<tr>
                        <td scope='row'>Corte $period->order </td>
                        <td> $period->inicial_date </td>
                        <td> $period->final_date </td>
                    </tr>";
                }
        
                return [
                    'result' => 'ok',
                    'table' => $table
                ];

            }else{
                return [
                    'result' => 'error',
                    'message' => 'La fecha final debe ser mayor a la fecha inicial',
                ];
            }
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th
            ];
        }

    }

    public function getAllPeriods(Request $request){
        try {
            $periods = DB::table('periods')
            ->where('promo_id',$request->promo_id)
            ->get();

            return $periods;
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }
    }

    public function getPeriods($promo){
        try {
            $periods = DB::table('periods')
            ->select('id')
            ->where('promo_id',$promo)
            ->get();

            $arrPeriods = array();
            foreach ($periods as $key => $period) {
                array_push($arrPeriods, $period->id);
            }

            return $arrPeriods;
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }
    }

    public function addPeriod(Request $request){

        try {
            //code...
            // return $request->all();
    
            $promo = DB::table('promo')
            ->where('promo.id', $request->promoId)
            ->first();
    

    
            $lastPeriod = DB::table('periods')
            ->select(
                DB::raw('DATE_FORMAT(periods.inicial_date, "%Y-%m-%d") as inicial_date'),
                DB::raw('DATE_FORMAT(periods.final_date, "%Y-%m-%d") as final_date'),
                'periods.order',
            )
            ->where('periods.promo_id', $promo->id)
            ->orderBy('periods.id', 'DESC')
            ->first();

            if($lastPeriod){
                $order = $lastPeriod->order + 1;
            }else{
                $order = 1;
            }

            
            if( $request->initial_date < $request->final_date  ){
                DB::table('periods')
                ->insert([
                    'promo_id' => $promo->id,
                    'order' => $order,
                    'inicial_date' => $request->initial_date,
                    'final_date' => $request->final_date.' 23:59:59',
                ]);
    
                // write the table in HTML
    
                $periods = DB::table('periods')
                ->where('periods.promo_id', $promo->id)
                ->get();
    
                $table = "";
    
                foreach ($periods as $key => $period) {
                    # code...
                    $table = $table . "<tr>
                        <td scope='row'>Corte $period->order </td>
                        <td> $period->inicial_date </td>
                        <td> $period->final_date </td>
                    </tr>";
                }
        
                return [
                    'result' => 'ok',
                    'table' => $table
                ];

            }else{
                return [
                    'result' => 'error',
                    'message' => 'La fecha final debe ser mayor a la fecha inicial',
                ];
            }
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th
            ];
        }


    }


}
