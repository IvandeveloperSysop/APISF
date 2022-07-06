<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\periodsController;
use Carbon\Carbon;
use Session;

class adminController extends Controller
{

    protected $periodsController;
    public function __construct(periodsController $periodsController)
    {
        $this->periodsController = $periodsController;
    }

    public function index(Request $request) {

        try {
            //code...
            // dd('Hola');
            if($request->session()->exists('admin_id')){
                
                $status_id = 2;
                
                // dd($periods);
                $tickets = DB::table('tickets')
                ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
                ->leftJoin('users', 'periods_score.user_id', '=', 'users.id')
                ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
                ->leftJoin('promo', 'periods.promo_id', '=', 'promo.id')
                ->leftJoin('status_catalog', 'tickets.status', '=', 'status_catalog.id')
                ->select(
                    'users.name',
                    'tickets.id', 
                    DB::raw('DATE_FORMAT(tickets.date_ticket, "%d/%m/%Y") as date_ticket'),
                    'periods.inicial_date',
                    'periods.final_date',
                    'periods.id as period_id',
                    'periods.order as orderNum',
                    'status_catalog.name as status',
                    'status_catalog.id as status_id',
                    'promo.title as promo_name',
                    'periods.promo_id',
                )
                ->where('tickets.status', $status_id)
                ->where('status_catalog.table', 'tickets')
                ->orderBy('tickets.created_at', 'DESC')
                ->paginate(9);

                $promos = DB::table('promo')
                ->where('type_id', 5)
                ->get();

                // dd($promos);

                
                
                return view('admin.index',['tickets' => $tickets, 'status_id' => $status_id, 'promos' => $promos]);
            }else{
                // dd('hola');
                $promos = DB::table('promo')
                ->where('id', 1)
                ->get();
                // dd($promos, 'HOla');

                return view('authAdmin.loginAdmin', ['promos' => $promos]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
        
    }

    public function login(Request $request){
        $email = $request->email;
        $password = md5($request->password);
        $promo = $request->promocionSelect;
        // dd($promo);

        $user = DB::table('admin_users')
        ->where('email', $email)
        ->where('password', $password )
        ->where('status_id', 44)
        ->first();

        
        $promoInfo = DB::table('promo')
        ->where('id',$promo)
        ->first();
        // dd($user);
        if($user){

            session(['admin_id' => $user->id,
            'promo' => $promo,
            'nameAdmin' => $user->name,
            'promoName' => $promoInfo->title,
            'type_id' => $user->type_id,
            'status_id' => $user->status_id,
            'email' => $user->email ]);
            return redirect()->route('admin');

        }
        else{
            return redirect()->route('admin')->with('message','error');
        }
        
    }

    public function logout(Request $request){

        $request->session()->flush();


        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/admin');
    }

    public function ticketAprove(Request $request) {

        if($request->session()->exists('admin_id')){

            
            $status_id = 1;

            $tickets = DB::table('tickets')
            ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
            ->leftJoin('users', 'periods_score.user_id', '=', 'users.id')
            ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
            ->leftJoin('promo', 'periods.promo_id', '=', 'promo.id')
            ->leftJoin('status_catalog', 'tickets.status', '=', 'status_catalog.id')
            ->select(
                'users.name',
                'tickets.id', 
                DB::raw('DATE_FORMAT(tickets.date_ticket, "%d/%m/%Y") as date_ticket'),
                'periods.inicial_date',
                'periods.final_date',
                'periods.id as period_id',
                'periods.order as orderNum',
                'status_catalog.name as status',
                'status_catalog.id as status_id',
                'promo.title as promo_name',
                'periods.promo_id',
            )
            ->where('tickets.status', $status_id)
            ->where('status_catalog.table', 'tickets')
            ->orderBy('tickets.created_at', 'DESC')
            ->paginate(9);
            
            // dd($tickets);
            // Get data para agregar el filtro de promociones, el type 5 = a las promociones que son carreras
            $promos = DB::table('promo')
            ->get();
    
            return view('admin.tickets.ticketsAprove',['tickets' => $tickets, 'promos' => $promos, 'status_id' => $status_id]);

        }else{
            // dd('hola');
            return redirect()->route('admin');
        }

        
    }

    public function ticketCancel(Request $request) {

        if($request->session()->exists('admin_id')){

            $status_id = 0;

            $tickets = DB::table('tickets')
            ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
            ->leftJoin('users', 'periods_score.user_id', '=', 'users.id')
            ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
            ->leftJoin('promo', 'periods.promo_id', '=', 'promo.id')
            ->leftJoin('status_catalog', 'tickets.status', '=', 'status_catalog.id')
            ->select(
                'users.name',
                'tickets.id', 
                DB::raw('DATE_FORMAT(tickets.date_ticket, "%d/%m/%Y") as date_ticket'),
                'periods.inicial_date',
                'periods.final_date',
                'periods.id as period_id',
                'periods.order as orderNum',
                'status_catalog.name as status',
                'status_catalog.id as status_id',
                'promo.title as promo_name',
                'periods.promo_id',
            )
            ->whereIn('tickets.status', [0,46])
            ->where('status_catalog.table', 'tickets')
            ->orderBy('tickets.created_at', 'DESC')
            ->paginate(9);

            // Get data para agregar el filtro de promociones, el type 5 = a las promociones que son carreras
            $promos = DB::table('promo')
            ->where('type_id', 5)
            ->get();
    
            
            return view('admin.tickets.ticketsCancel',['tickets' => $tickets,'promos' => $promos, 'status_id' => $status_id]);

        }else{
            // dd('hola');
            return view('authAdmin.loginAdmin');
        }
        
    }

    public function getCortes(Request $request){

        try {

            $cortes = [];
            if($request->session()->exists('admin_id')){

                $cortes = DB::table('periods')
                ->where('promo_id', $request->promo_id)
                ->get();

                $dataCortes = "";
                foreach ($cortes as $key => $corte) {
                    $dataCortes = $dataCortes."<option value='".$corte->id."'> Corte".$corte->order."</option>";
                }
    
                $result = 'ok';
            }else{
                $result = 'Error';
            }

            $json = [
                'result' => $result,
                'cortes' => $dataCortes,
            ];
            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            return [ 'result' => $th ];
        }



    }

    public function valadmin_id($user){



    }

}
