<?php

namespace App\Http\Controllers\rewardsProgram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\routeGlobal;
use App\Http\Controllers\periodsController;
use App\Http\Controllers\rewardsProgram\bonusController;
use Illuminate\Support\Facades\Storage;
use Session;

class promotionController extends Controller
{
    //
    protected $periodsController;
    protected $routeGlobal;
    protected $walletController;
    protected $bonusController;
    public $date;

    public function __construct(periodsController $periodsController, routeGlobal $routeGlobal)
    { 
        $this->periodsController = $periodsController;
        $this->routeGlobal = $routeGlobal->index();
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }


    public function promociones(Request $request){ 

        if($request->session()->exists('admin_id')){

            $promos = DB::table('promo')
            ->leftJoin('status_catalog', 'promo.status_id', '=', 'status_catalog.id')
            ->select(
                'promo.id',
                'promo.title',
                DB::raw('DATE_FORMAT(promo.begin_date, "%d/%m/%Y") as begin_date'),
                DB::raw('DATE_FORMAT(promo.end_date, "%d/%m/%Y") as end_date'),
                'promo.awards_qty',
                'status_catalog.name',
                'status_catalog.id as statusId',
            )
            ->orderBy('promo.id','DESC')
            ->paginate(9);
    
            return view('admin.promociones.index',['promos' => $promos]);
        }else{
            // dd('hola');
            $promos = DB::table('promo')
            ->where('type_id', 4)
            ->get();
            // dd($promos, 'HOla');

            return view('authAdmin.loginAdmin', ['promos' => $promos]);
        }

    }

    public function addPromo(Request $request){

        try {
            
            DB::table('promo')
            ->insert([
                'promo.title' => $request->promoTitle,
                'promo.begin_date' => Carbon::parse($request->begin_date.' 00:00:00')->format('Y-m-d h:m:s'),
                'promo.end_date' => Carbon::parse($request->end_date.' 00:00:00')->format('Y-m-d h:m:s'),
                'promo.awards_qty' => $request->awards_qty,
                'type_id' => 5, 
                'status_id' => 24,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $promoCreate = DB::table('promo')
            ->orderBy('promo.id','DESC')
            ->first();


            if($request->image){
                $namePromo = str_replace(' ', '', $request->promoTitle);
                $data = explode( ',', $request->image );
                $path = 'img/promo/promo'.$promoCreate->id.'/'.$namePromo.'.'.$request->extension;
                $content = base64_decode($data[1]);
                $typeFile = Str::substr($data[0],0,10);
                if($typeFile == 'data:image' ){
                    Storage::disk('public')->put( $path, $content);
                }
            }

            if ( $request->imageBanner ) {
                $nameImageBanner = str_replace(' ', '', $request->promoTitle);
                $data = explode( ',', $request->imageBanner );
                $pathBanner = 'img/promo/promo'.$promoCreate->id.'/'.$nameImageBanner.'Banner.'.$request->extensionBanner;
                $content = base64_decode($data[1]);
                $typeFile = Str::substr($data[0],0,10);
                if($typeFile == 'data:image' ){
                    Storage::disk('public')->put( $pathBanner, $content);
                }
            }

            DB::table('promo')
            ->where('promo.id', '=', $promoCreate->id)
            ->update([
                'promo.imageBanner' => $pathBanner,
                'promo.image' => $path,
            ]);


            $periodController = $this->periodsController->create( $promoCreate );


            $promos = DB::table('promo')
            ->select(
                'promo.id',
                'promo.title',
                DB::raw('DATE_FORMAT(promo.begin_date, "%d/%m/%Y") as begin_date'),
                DB::raw('DATE_FORMAT(promo.end_date, "%d/%m/%Y") as end_date'),
                'promo.awards_qty',
            )
            ->orderBy('promo.id','DESC')
            // ->where('type_id', 5)
            ->get();

            $tablePromo = "";

            foreach ($promos as $key => $promo) {
                $tablePromo = $tablePromo."<tr>
                    <th scope='row'> $promo->id </th>
                    <td> $promo->title </td>
                    <td> $promo->begin_date </td>
                    <td> $promo->end_date </td>
                    <td> $promo->awards_qty </td>
                    <td style='width: 15vw;' >
                        <a type='button' href='".route('promoDetails',['id'=> $promo->id])."' class='btn btn-success'>Editar</a>
                        <a type='button' onclick='downPromo($promo->id)' class='btn btn-danger'>Dar de baja</a>
                    </td>
                </tr>";
            }

            return [
                'table' => $tablePromo,
                'result' => 'ok',
            ];

        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }

    }

    public function updatePromo(Request $request){

        try {
            //code...
            DB::table('promo')
            ->where('promo.id', $request->promo_id)
            ->update([
                'promo.title' => $request->promoTitle,
                'promo.begin_date' => Carbon::parse($request->begin_date.' 00:00:00')->format('Y-m-d h:m:s'),
                'promo.end_date' => Carbon::parse($request->end_date.' 00:00:00')->format('Y-m-d h:m:s'),
                'promo.awards_qty' => $request->awards_qty,
            ]);

            DB::table('periods')
            ->where('promo_id', $request->promo_id)
            ->update([
                'inicial_date' => Carbon::parse($request->begin_date.' 00:00:00')->format('Y-m-d h:m:s'),
                'final_date' => Carbon::parse($request->end_date.' 00:00:00')->format('Y-m-d h:m:s'),
            ]);


            if($request->image){
                $namePromo = str_replace(' ', '', $request->promoTitle);
                $data = explode( ',', $request->image );
                $path = 'img/promo/promo'.$request->promo_id.'/'.$namePromo.'.'.$request->extension;
                $content = base64_decode($data[1]);
                $typeFile = Str::substr($data[0],0,10);

                if($typeFile == 'data:image' ){
                    Storage::disk('public')->put( $path, $content);
                }

                DB::table('promo')
                ->where('promo.id', '=', $request->promo_id)
                ->update([
                    'promo.image' => $path,
                ]);
            }

            if ( $request->imageBanner ) {

                $nameImageBanner = str_replace(' ', '', $request->promoTitle);
                $data = explode( ',', $request->imageBanner );
                $pathBanner = 'img/promo/promo'.$request->promo_id.'/'.$nameImageBanner.'Banner.'.$request->extensionBanner;
                $content = base64_decode($data[1]);
                $typeFile = Str::substr($data[0],0,10);

                if($typeFile == 'data:image' ){
                    Storage::disk('public')->put( $pathBanner, $content);
                }

                DB::table('promo')
                ->where('promo.id', '=', $request->promo_id)
                ->update([
                    'promo.imageBanner' => $pathBanner,
                ]);

            }

            return [
                'result' => 'ok',
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }


    }

    public function promoDetails($id, Request $request){

        if($request->session()->exists('admin_id')){

            $promo = DB::table('promo')
            ->leftJoin('status_catalog as status', 'promo.status_id', '=', 'status.id')
            ->select(
                'promo.title',
                'promo.id as promo_id',
                'promo.begin_date',
                'promo.end_date',
                'promo.awards_qty',
                'promo.image',
                'promo.imageBanner',
                'status.name as statusName',
                'status.id as statusId',
            )
            ->where('promo.id', $id)
            ->first();
    
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
                ['bonus.promo_id', '=', $promo->promo_id],
                ['bonus.status_id', '=', 30],
            ])
            ->get();
    
            $lastPeriod = DB::table('periods')
            ->select(
                DB::raw('DATE_FORMAT(periods.final_date, "%Y-%m-%d") as final_date'),
                'order',
            )
            ->where('periods.promo_id', $promo->promo_id)
            ->orderBy('periods.id', 'DESC')
            ->first();
    
            if($lastPeriod){
                $final_date = new Carbon($lastPeriod->final_date, 'AMERICA/Monterrey');
                $final_date = $final_date->addDay()->format('Y-m-d');
                $order = $lastPeriod->order + 1;
            }else{
                $final_date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'))->format('Y-m-d');
                $order = 1;
            }
    
            $lastPeriod = [
                'final_date' => $final_date,
                'order' => $order,
            ];
    
            $periods = DB::table('periods')
            ->where('promo_id', $id)
            ->orderBy('order','ASC')
            ->get();
    
            $awards = DB::table('awards')
            ->leftJoin('status_catalog as status', 'awards.status_id', '=', 'status.id')
            ->select(
                'awards.id as awardId',
                'awards.name as awardName',
                'awards.image',
                'awards.position',
                'awards.redeem',
                'status.name as statusName',
                'status.id as statusId',
            )
            ->where('awards.promo_id', $promo->promo_id)
            ->orderBy('awards.position', 'ASC')
            ->get();
    
            $winners = DB::table('top_winners as winners')
            ->leftJoin('users', 'winners.user_id', '=', 'users.id')
            ->leftJoin('periods', 'winners.period_id', '=', 'periods.id')
            ->leftJoin('awards', 'winners.award_id', '=', 'awards.id')
            ->select(
                'winners.id',
                'winners.position',
                'users.name',
                'users.imageUrl',
                'awards.name as award',
                'awards.image',
                'periods.order',
            )
            ->where('winners.promo_id', $promo->promo_id)
            ->orderBy('winners.period_id', 'ASC')
            ->orderBy('winners.position','ASC')
            ->get();
    
            $users = DB::table('users')
            ->get();
    
            // dd($awards, $this->routeGlobal);
    
    
            return view('admin.promociones.detailsPromo',['promo' => $promo, 'periods' => $periods, 'awards' => $awards, 'lastPeriod' => $lastPeriod,
            'lastPeriod' => $lastPeriod, 'bonus' => $bonus, 'winners' => $winners, 'routeGlobal' => $this->routeGlobal]);
        }else{
            // dd('hola');
            $promos = DB::table('promo')
            ->where('type_id', 4)
            ->get();
            // dd($promos, 'HOla');

            return view('authAdmin.loginAdmin', ['promos' => $promos]);
        }
    }

    public function selectedAwards($promo_id = null){

        try {
            //code...

            $promos = DB::table('promo')
            ->where('status_id', '<>',25)
            ->get();
    
            $selectedPromo = "";
            
            foreach ($promos as $key => $promo) {
                
                $selected = "";

                if($promo_id && $promo->id == $promo_id){
                    $selected = "selected";
                }
    
                $selectedPromo = $selectedPromo . "<option value='$promo->id' $selected>$promo->title</option>";
            }
    
            return[
                'selected' => $selectedPromo,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th
            ];
        }

    }

    public function downPromo(Request $request){

        try {
            //code...
            DB::table('promo')
            ->where('promo.id', $request->promo_id)
            ->update([
                'status_id' => 25,
            ]);

            return [
                'result' => 'ok',
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }

    }

    public function endPromo(Request $request){

        try {
            
            $period_score = DB::table('periods_score')
            ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
            ->leftJoin('promo', 'periods.promo_id', '=', 'promo.id')
            ->leftJoin('top_winners', 'periods_score.user_id', '=', 'top_winners.user_id')
            ->select(
                'top_winners.id as winner_id',
                'periods_score.user_id',
                'periods_score.score',
            )
            ->where('promo.id', '=',$request->promo_id)
            ->whereNull('top_winners.id')
            ->get();

            // return [$period_score];

            foreach ($period_score as $key => $pointScore) {

                $wallet = DB::table('wallets')
                ->where('user_id', $pointScore->user_id)
                ->first();

                
                DB::table('transactions')
                ->insert([
                    'wallet_id' => $wallet->id,
                    'amount' => $pointScore->score,
                    'type_id' => 7,
                    'promo_id' => $request->promo_id,
                    'title' => 'Mini concurso',
                    'created_at' => $this->date,
                    'updated_at' => $this->date,
                ]);

                DB::table('wallets')
                ->where('id', $wallet->id)
                ->update([
                    'wallets.balance' => $pointScore->score
                ]);

            }

            DB::table('promo')
            ->where('promo.id', $request->promo_id)
            ->update([
                'status_id' => 38,
            ]);


            return [
                'result' => 'ok',
            ];

        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }

    }
    
}
