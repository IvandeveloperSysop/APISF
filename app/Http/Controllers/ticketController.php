<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Session;
use PDF;
use App\Http\Controllers\miniGameController;
use App\Http\Controllers\periodsController;
use App\Http\Controllers\ticketVirtualController;
use App\Http\Controllers\emailController;
use App\Http\Controllers\rewardsProgram\walletController;


class ticketController extends Controller
{
    //

    protected $miniGameController;
    protected $periodsController;
    protected $ticketVirtualController;
    protected $emailController;
    protected $routeGlobal;
    protected $date;
    public function __construct(miniGameController $miniGameController, periodsController $periodsController,
        routeGlobal $routeGlobal, walletController $walletController,
        ticketVirtualController $ticketVirtualController, emailController $emailController)
    {
        $this->miniGameController = $miniGameController;
        $this->walletController = $walletController;
        $this->periodsController = $periodsController;
        $this->ticketVirtualController = $ticketVirtualController;
        $this->emailController = $emailController;
        $this->routeGlobal = $routeGlobal;
        $this->date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }

    public function createTicket(Request $request) {


        try {
            $validPromo = DB::table('promo')
            ->where('promo.id',$request->promo_id)
            ->first();

            if ($validPromo->status_id == 24) {
                # code...
                $user = DB::table('users')
                ->where("token",$request->token)
                ->first();

                // return [ $request->all() ];
                $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));

                $dateTicket = Carbon::parse($request->fecha, 'UTC');
                $period = $this->periodsController->index($date,$request->promo_id);
                $data = explode( ',', $request->images );
                // $pathP = 'img/tickets/'.'ticket-'.$date->format('Y-m-d_H-s').'.'.$request->extension;
                $pathP = 'img/tickets/corte'.$period->id.'/'.$date->format('Y-m-d').'/ticket-'.$user->id.'-'.$date->format('Y-m-d_H-s').'.'.$request->extension;
                $content = base64_decode($data[1]);
                // $validate = $this->validate($request, [ $content => ['image','mimes:jpeg,png,jpg']]);
                $typeFile = Str::substr($data[0],0,10);
                if($typeFile == 'data:image' ){

                    Storage::disk('public')->put( $pathP, $content);

                    $routeGlobal = $this->routeGlobal->index();
                    $path = $routeGlobal.$pathP;
                    // data:image
                    $points = 0;

                    $periodScore = DB::table('periods_score')
                    ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
                    ->select(
                        'periods_score.id',
                        'periods_score.period_id',
                        'periods_score.pending_score',
                        'periods.promo_id',
                    )
                    ->where([
                        ['periods_score.user_id', '=', $user->id],
                        ['periods_score.period_id', '=', $period->id],
                        ['periods.promo_id', '=', $request->promo_id],
                    ])
                    ->first();

                    // return [$period];

                    if(!$periodScore){

                        DB::table('periods_score')
                        ->insert([
                            'user_id' => $user->id,
                            'period_id' => $period->id,
                            'score' => 0,
                            'pending_score' => 0,
                            'created_at' => $this->date,
                            'updated_at' => $this->date,
                        ]);

                        $periodScore = DB::table('periods_score')
                        ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
                        ->select(
                            'periods_score.id',
                            'periods_score.period_id',
                            'periods_score.pending_score',
                            'periods.promo_id',
                        )
                        ->where([
                            ['periods_score.user_id', '=', $user->id],
                            ['periods_score.period_id', '=', $period->id],
                            ['periods.promo_id', '=', $request->promo_id],
                        ])
                        ->first();

                    }


                    DB::table('tickets')->insert([
                        'file_url' => $path,
                        'image' => $path,
                        'date_ticket' => $dateTicket,
                        'period_score_id' => $periodScore->id,
                        // 'store' => '7-Eleven',
                        'store' => $request->store,
                        'status' => 2,
                        'points' => $points,
                        'state_id' => $request->state_id,
                        'validate' => 0,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $ticket = DB::table('tickets')
                    ->where([
                        ['period_score_id', '=', $periodScore->id],
                    ])
                    ->orderBy('id','desc')
                    ->first();

                    $presentationValue = 0;
                    $presentationPoints = 0;

                    foreach ($request->presents as $key => $presentation) {
                        # code...
                        if(is_array($presentation)){
                            if($presentation['value'] > 0){
                                DB::table('ticket_presentation')->insert([
                                    'ticket_id' => $ticket->id,
                                    'presentation_id' => $presentation['id'],
                                    'quantity' => $presentation['value'],
                                    'created_at' => $date,
                                    'updated_at' => $date
                                ]);
                                $presentationValue = $presentationValue + $presentation['value'];
                                // Puntos totales
                                $presentationNow = DB::table('presentation')
                                ->where('id', $presentation['id'])
                                ->first();
                                $presentationPoints = $presentationPoints + ($presentationNow->pointValue * $presentation['value']);

                            }
                        }
                    }


                    DB::table('tickets')
                    ->where('id', $ticket->id)
                    ->update([
                        'points' => $presentationPoints
                    ]);

                    DB::table('minigame_score')->insert([
                        'ticket_id' => $ticket->id,
                        'level_game' => 0,
                        'points' => 0,
                        'refer_id' => 1,
                        'mini_game_id' => 1,
                        'status' => 22,
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);

                    $minigame_score = DB::table('minigame_score')
                    ->orderBy('id','desc')
                    ->first();

                    // Valid bonus and add tickets
                    $bonus = 1;

                    if($bonus != 1){

                        $bonus = DB::table('bonus')
                        ->where([
                            ['bonus.begins_at', '<=', $ticket->created_at],
                            ['bonus.ends_at', '>=', $ticket->created_at],
                            ['bonus.status_id', '=', 30],
                        ])
                        ->orderBy('bonus.id', 'DESC')
                        ->first();

                        if( $bonus ){
                            $bonusPoints = $presentationPoints * $bonus->multiplier;

                            DB::table('extra_points')
                            ->insert([
                                'user_id' => $user->id,
                                'period_id' => $periodScore->period_id,
                                'type_id' => 6,
                                'ticket_id' => $ticket->id,
                                'points' => $bonusPoints,
                                'refer_or_minigames_id' => $bonus->id,
                                'status' => 32,
                            ]);

                        }

                    }

                    $pending_score_points = $periodScore->pending_score + $presentationPoints;
                    DB::table('periods_score')
                    ->where('id', $periodScore->id)
                    ->update([
                        'pending_score' => $pending_score_points
                    ]);

                    $json = [
                        "result" => 'ok',
                        "minigame_score" => $minigame_score,
                        'presentationValue' => $presentationValue,
                    ];
                    return $json;
                }else{
                    $validarImage = false;
                    $json = [
                        "result" => 'error',
                    ];
                    return $json;
                }
            }else{
                return [
                    'result' => 'promoFinished',
                    'message' => 'Esta promoción ha finalizado, pero puedes participar en nuestras demás promociones y en nuestra tienda de recompensas',
                ];
            }

        } catch (\Throwable $th) {
            $json = [
                "result" => $th,
            ];
            //echo json_encode($json);

            return $json;
        }

    }

    public function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );

        // we could add validation here with ensuring count( $data ) > 1
        // fwrite( $ifp, base64_decode( $data[ 1 ] ) );
        $content = base64_decode($data[1]);
        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }

    public function tickets(Request $request){

        try {
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));


            $tickets = DB::table('tickets')
            ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
            ->leftJoin('users', 'periods_score.user_id', '=', 'users.id')
            ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
            ->leftJoin('promo', 'periods.promo_id', '=', 'promo.id')
            ->leftJoin('status_catalog', 'tickets.status', '=', 'status_catalog.id')
            ->leftJoin('minigame_score', 'tickets.id', '=', 'minigame_score.ticket_id')
            ->select(
                'users.name',
                'tickets.id as ticket_id',
                'tickets.date_ticket',
                'tickets.comment',
                'tickets.image',
                'tickets.points as ticketPoints',
                'periods.inicial_date',
                'periods.final_date',
                'promo.title as namePromo',
                'status_catalog.name as status',
                'status_catalog.id as status_id',
                'minigame_score.points as extrapoints'
            )
            ->where('users.token',$request->token)
            // ->where('periods.promo_id', $request->promo_id)
            // ->where('status_catalog.table', 'tickets')
            ->orderBy('tickets.created_at', 'DESC')
            ->paginate(10);

            // return [$request->all()];

            $arrTickets  = [];
            foreach ($tickets as $key => $ticket) {
                # code..

                $arrTicket = [
                    "image" => $ticket->image,
                    'name' => $ticket->name,
                    'ticket_id' => $ticket->ticket_id,
                    'date_ticket' => $ticket->date_ticket,
                    'comment' => $ticket->comment,
                    'namePromo' => $ticket->namePromo,
                    'ticketPoints' => $ticket->ticketPoints,
                    'inicial_date' => $ticket->inicial_date,
                    'final_date' => $ticket->final_date,
                    'extrapoints' => $ticket->extrapoints,
                    'status' => $ticket->status,
                    'status_id' => $ticket->status_id
                ];

                array_push($arrTickets, $arrTicket);
                // $arrTickets.push($arrTicket);
            }

            $json = [
                "data" => $arrTickets,
                "tickets" => $tickets,
            ];
            return $json;

        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                'message' => $th
            ];
            // echo json_encode($json);

            return $json;
        }
    }

    public function updateTicket($id,$view,Request $request){

        if($request->session()->exists('admin_id')){

            $tickets = DB::table('tickets')
            ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
            ->leftJoin('users', 'periods_score.user_id', '=', 'users.id')
            ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
            ->leftJoin('promo', 'periods.promo_id', '=', 'promo.id')
            ->leftJoin('status_catalog', 'tickets.status', '=', 'status_catalog.id')
            ->leftJoin('admin_users','tickets.id_admin', '=', 'admin_users.id')
            ->select(
                'users.name as userName',
                'users.id as userId',
                'tickets.id',
                'tickets.date_ticket',
                'tickets.numTicket',
                'tickets.points',
                'tickets.store',
                'tickets.period_score_id',
                'tickets.state_id',
                'tickets.image as image',
                'tickets.comment',
                'tickets.validate',
                'tickets.created_at',
                'tickets.hour_ticket',
                'tickets.total_points',
                'admin_users.name as adminName',
                'periods_score.score',
                'periods.inicial_date',
                'periods.final_date',
                'periods.promo_id',
                'promo.title as namePromo',
                'status_catalog.name as nameSate',
                'status_catalog.name as status',
                'status_catalog.id as statusId',
            )
            ->where('tickets.id',$id)
            ->first();

            // dd($tickets);
            $id_ticket = $tickets->id;
            // die();

            $status = DB::table('status_catalog')
            ->where('table','tickets')
            ->get();

            // dd($status);

            $promo_id = $tickets->promo_id;

            $presentations = DB::table('presentation')
            ->leftJoin('ticket_presentation', function($join) use ($id_ticket)
            {
                $join->on('presentation.id', '=', 'ticket_presentation.presentation_id');
                $join->on('ticket_presentation.ticket_id','=',DB::raw("'".$id_ticket."'"));
            })
            ->select(
                'ticket_presentation.presentation_id',
                'ticket_presentation.quantity',
                'ticket_presentation.price',
                'presentation.name',
                'presentation.id'
            )
            ->where(function($query) use ($promo_id){
                if ( $promo_id != 1) $query->whereIn('promo_id', [1, $promo_id]);
            })
            ->where('presentation.status_id', '<>', 43)
            ->orderBy('ticket_presentation.quantity', 'DESC')
            ->orderBy('presentation.id', 'ASC')
            ->get();

            $presentationsNotArca = DB::table('presentation_not_arca')
            // ->leftJoin('ticket_presentation','presentation.id', '=', 'ticket_presentation.presentation_id')
            ->leftJoin('ticket_presentation_not_arca', function($join) use ($id_ticket)
            {
                $join->on('presentation_not_arca.id', '=', 'ticket_presentation_not_arca.presentation_id');
                $join->on('ticket_presentation_not_arca.ticket_id','=',DB::raw("'".$id_ticket."'"));
            })
            ->select(
                'ticket_presentation_not_arca.presentation_id',
                'ticket_presentation_not_arca.quantity',
                'ticket_presentation_not_arca.price',
                'presentation_not_arca.name',
                'presentation_not_arca.id'
            )
            ->orderBy('ticket_presentation_not_arca.quantity', 'DESC')
            ->orderBy('presentation_not_arca.name', 'ASC')
            ->get();

            $totalPresentations = DB::table('ticket_presentation')
            ->select(
                DB::raw('SUM(ticket_presentation.price) as total')
            )
            ->where('ticket_presentation.ticket_id', $id_ticket)
            ->first();

            // dd($totalPresentations);

            $totalPresentationsNonArca = DB::table('ticket_presentation_not_arca')
            ->select(
                DB::raw('SUM(ticket_presentation_not_arca.price) as total')
            )
            ->where('ticket_presentation_not_arca.ticket_id', $id_ticket)
            ->First();

            $score = $tickets->total_points;

            $ticket = [
                'image' => $tickets->image,
                'userName' => $tickets->userName,
                'id' => $tickets->id,
                'date_ticket' => $tickets->date_ticket,
                'numTicket' => $tickets->numTicket,
                'store' => $tickets->store,
                'userId'=> $tickets->userId,
                'hour_ticket' => $tickets->hour_ticket,
                // 'products' => $tickets->products,
                'score' => $score,
                'namePromo' => $tickets->namePromo,
                'inicial_date' => $tickets->inicial_date,
                'final_date' => $tickets->final_date,
                'state_id' => $tickets->state_id,
                'nameSate' => $tickets->nameSate,
                'status' => $tickets->status,
                'statusId' => $tickets->statusId,
                'comment' => $tickets->comment,
                'validate' => $tickets->validate,
                'adminName' => $tickets->adminName,
                'created_at' => date('d/m/Y', strtotime($tickets->created_at))
            ];
            // dd($tickets);

            return view('admin.tickets.update',['ticket' => $ticket, 'status' => $status, 'presentations' => $presentations,
            'totalPresentations' => $totalPresentations, 'totalPresentationsNonArca' => $totalPresentationsNonArca ,'presentationsNotArca' => $presentationsNotArca, 'view' => $view ]);

        }else{
            // dd('hola');
            return view('authAdmin.loginAdmin');
        }


    }

    public function updateTicketConf($id,$status,$comment,$numTicket, Request $request){

        try {

            if($comment == 'nada'){
                $comment = '';
            }else{
                $comment = str_replace("_", " ",$comment);
            }

            if($status == 46){
                $comment = str_replace("_", " ",$request->commentTInvalid);
            }

            $buttonSave = "";
            if($status == 0){
                $numTicket = '';
                $ticket = null;
            }else{
                $promo = Session::get('promo');
                $ticket = DB::table('tickets')
                ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
                ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
                ->where('tickets.numTicket', $numTicket)
                ->where('tickets.id','<>',$id)
                ->where('periods.promo_id',$promo)
                ->first();
            }

            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));


            if(!$ticket){

                DB::table('tickets')
                ->where('id', $id)
                ->update([
                    'status' => $status,
                    'comment' => $comment,
                    'numTicket' => $numTicket,
                    'hour_ticket' => $request->hour_ticket,
                    'validate' => 1,
                    'id_admin' => $request->session()->get('admin_id'),
                    'updated_at' => $date,
                ]);

                $ticket =  DB::table('tickets')
                ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
                ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
                ->leftJoin('status_catalog', 'tickets.status', '=', 'status_catalog.id')
                ->select(
                        'status_catalog.name as status',
                        'periods_score.user_id',
                        'periods_score.period_id',
                        'periods_score.id as ps_id',
                        'periods_score.score as ps_score',
                        'periods.promo_id',
                        'tickets.points',
                        'tickets.id as ticket_id',
                        'tickets.created_at',
                        'tickets.status as status_id',
                    )
                ->where('tickets.id',$id)
                ->first();
                $statusDiv = "<label for='user' class='form-label me-4'>Estatus:</label>";


                if ($ticket->status == 'Revisando'){
                    $statusDiv = $statusDiv."<button type='button' class=' btn btn-warning rounded-pill text-white text-center'>".$ticket->status."</button>";
                }elseif ($ticket->status == 'Aprobado' || $ticket->status == 'invalid'){

                    if($request->arrPresentation){

                        $scoreModified = DB::table('periods_score')
                        ->where('id', $ticket->ps_id)
                        ->first();

                        foreach ($request->arrPresentation as $key => $presentationArr) {
                            // return "Entro al foreach";
                            if(isset($presentationArr['idPresentation'])){

                                $ticket_presentation = DB::table('ticket_presentation')
                                ->where('ticket_presentation.ticket_id',$id)
                                ->where('ticket_presentation.presentation_id',$presentationArr['idPresentation'])
                                ->first();

                                if($ticket_presentation){
                                    if($presentationArr['value'] == 0 || !$presentationArr['value']){

                                        // return $recalculate;
                                        DB::table('ticket_presentation')
                                        ->where('ticket_presentation.ticket_id',$id)
                                        ->where('ticket_presentation.presentation_id',$presentationArr['idPresentation'])
                                        ->delete();

                                    }else{
                                        DB::table('ticket_presentation')
                                        ->where('ticket_presentation.ticket_id',$id)
                                        ->where('ticket_presentation.presentation_id',$presentationArr['idPresentation'])
                                        ->update([
                                            'ticket_presentation.quantity' => $presentationArr['value'],
                                            'price' => $presentationArr['price'],
                                        ]);
                                    }
                                }else{
                                    if($presentationArr['value'] != 0){

                                        // return [$recalculate];

                                        DB::table('ticket_presentation')->insert([
                                            'ticket_id' => $id,
                                            'presentation_id' => $presentationArr['idPresentation'],
                                            'quantity' => $presentationArr['value'],
                                            'price' => $presentationArr['price'],
                                            'created_at' => $date,
                                            'updated_at' => $date
                                        ]);

                                    }
                                }
                            }
                        }
                    }

                    if($request->arrOtherPresentation){

                        foreach ($request->arrOtherPresentation as $key => $presentationOtherArr) {

                            if(isset($presentationOtherArr['idPresentation']) && $presentationOtherArr['value'] != 0){

                                DB::table('ticket_presentation_not_arca')->insert([
                                    'ticket_id' => $id,
                                    'presentation_id' => $presentationOtherArr['idPresentation'],
                                    'quantity' => $presentationOtherArr['value'],
                                    'price' => $presentationOtherArr['price'],
                                    'created_at' => $date,
                                    'updated_at' => $date
                                ]);
                            }

                        }

                    }

                    $points = 0;
                    // Puntos para productos topochico
                    // $pointsHS = 0;


                    $presentationTicket = DB::table('ticket_presentation')
                    ->leftJoin('presentation', 'ticket_presentation.presentation_id', '=', 'presentation.id')
                    ->select(
                        'presentation.pointValue',
                        'presentation.id as presentationId',
                        'ticket_presentation.id',
                        'ticket_presentation.quantity',
                        'ticket_presentation.price',
                    )
                    ->where('ticket_presentation.ticket_id',$id)
                    ->get();

                    foreach ($presentationTicket as $key => $presentation) {
                        // if (in_array($presentation->presentationId, [1,2,3,152,153])) {
                        //     $pointsHS = $pointsHS + $presentation->price;
                        //     $points = $points + ($presentation->price * 3);
                        // }else{
                            $points = $points + $presentation->price;
                        // }
                        // } else {
                        //     $points = $points + $presentation->quantity * $presentation->pointValue;
                        // }

                    }

                    // Validar si existen puntos de bonus
                    // --------------------------------------------------

                        $bonusExtra = DB::table('bonus')
                        ->where([
                            ['bonus.begins_at', '<=', $ticket->created_at],
                            ['bonus.ends_at', '>=', $ticket->created_at],
                            ['bonus.promo_id', '=', $ticket->promo_id],
                            ['bonus.status_id', '=', 30],
                        ])
                        ->orderBy('bonus.id', 'DESC')
                        ->first();

                        if( $bonusExtra ){
                            $bonusPoints = $points * $bonusExtra->multiplier;

                            DB::table('extra_points')
                            ->insert([
                                'user_id' => $ticket->user_id,
                                'period_id' => $ticket->period_id,
                                'type_id' => 6,
                                'ticket_id' => $id,
                                'points' => $bonusPoints,
                                'refer_or_minigames_id' => $bonusExtra->id,
                                'status' => 32,
                                'created_at' => $this->date,
                                'updated_at' => $this->date,
                            ]);

                        }

                        $bonus = DB::table('extra_points')
                        ->leftJoin('bonus','extra_points.refer_or_minigames_id', '=', 'bonus.id')
                        ->select(
                            'bonus.multiplier',
                            'extra_points.id'
                        )
                        ->where([
                            ['extra_points.ticket_id', '=', $id],
                            ['extra_points.type_id', '=', 6],
                        ])
                        ->first();

                        if( $bonus ){
                            $points = $points * $bonus->multiplier;

                            DB::table('extra_points')
                            ->where('extra_points.id', '=', $bonus->id)
                            ->update([
                                'status' => 6,
                                'points' => $points,
                            ]);
                        }

                    // --------------------------------------------------
                    // End puntos de bonus


                    //  valid Refer
                        // $validRefer = $this->validRefer($ticket,$pointsHS);
                        //  Gift card regalado por referido
                    // End valid Refer

                    if($ticket->status_id != 46){

                        DB::table('tickets')
                        ->where('id', $id)
                        ->update([
                            'points' =>  $points,
                            'store' => $request->store,
                            'total_points' => $points,
                            'updated_at' => $date
                        ]);

                        // Validación  si es ticket directo de tienda

                        $pendingPoints = $this->recalculatePeriodScore($ticket->ps_id,2);

                        DB::table('periods_score')
                        ->where('id', $ticket->ps_id)
                        ->update([
                            'score' => $ticket->ps_score + $points,
                            'pending_score' => $pendingPoints,
                        ]);

                        //  Mini carrera
                        if ($ticket->promo_id != 1) {

                            $validPs = DB::table('periods_score')
                            ->leftJoin('periods','periods_score.period_id', '=', 'periods.id')
                            ->select(
                                'periods.promo_id',
                            )
                            ->where([
                                ['periods_score.id', '=', $ticket->ps_id]
                            ])
                            ->first();

                        }else{

                            $wallet = DB::table('wallets')
                            ->where('wallets.user_id', $ticket->user_id)
                            ->first();

                            DB::table('wallets')
                            ->where('wallets.id', $wallet->id)
                            ->update([
                                'wallets.balance' => $wallet->balance + $points
                            ]);

                            DB::table('transactions')
                            ->insert([
                                'wallet_id' => $wallet->id,
                                'amount' =>  $points,
                                'type_id' => 7,
                                'promo_id' => $ticket->promo_id,
                                'title' => 'Puntos por ticket aprobado',
                                'created_at' => $this->date,
                                'updated_at' => $this->date,
                            ]);

                            if($points > 99){
                                $voucher = $this->ticketVirtualController->createVoucher($points,$ticket);
                            }

                        }

                    }


                    $statusDiv = $statusDiv."<button type='button' class='btn btn-success rounded-pill text-white
                    text-center'>".$ticket->status."</button>";

                }else {

                    //Aca se asignana los puntos de la trivia si tiene mas de x tickets
                    $pendingPoints = $this->recalculatePeriodScore($ticket->ps_id,2);


                    DB::table('periods_score')
                    ->where('id', $ticket->ps_id)
                    ->update([
                        'pending_score' => $pendingPoints,
                    ]);


                    $statusDiv = $statusDiv."<button type='button' class=' btn btn-danger rounded-pill text-white text-center'>".$ticket->status."</button>";

                }

                $extraResult = $this->miniGameController->extraPointsminigame($id, $ticket->ps_id);

                // return [$extraResult];

                $buttonSave = "<div class='text-end'>
                    <button type='button' class='btn btn-primary' disabled>Guardar</button>
                </div>";

                $json = [
                    "message"=>'ok',
                    'extraResult' => $extraResult,
                    "result" => $statusDiv,
                    "buttonSave" => $buttonSave
                ];
            }else{
                $json = [
                    'message' => "numTicketInvalid"
                ];
            }

            return $json;
            //code...
        } catch (\Throwable $th) {
            $json = [
                "message"=>'invalid',
                "result" => $th
            ];
            //echo json_encode($json);
            return $json;
        }

    }

    public function validRefer($ticket,$points){

        try {

            $refer = DB::table('refers')
            ->where('user_id', $ticket->user_id)
            ->first();

            if( $refer && $refer->status == 3 ){

                DB::table('refers')
                ->where('id',$refer->id)
                ->update([
                    'status' => 5,
                    'updated_at' => $this->date,
                ]);

                $ticketCount = DB::table('tickets')
                ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
                ->select(
                    'tickets.id',
                    'periods_score.user_id',
                )
                ->where([
                    ['tickets.validate','=',1],
                    ['tickets.id','<>',$ticket->ticket_id],
                    ['periods_score.user_id','=',$ticket->user_id],
                ])
                ->first();

                // return [$ticketCount];

                $giftCard = DB::table('awards')
                ->where('id',1)
                ->first();

                // return [$giftCard];

                if ($ticketCount < 1 && $points >= 199 && $giftCard->stock > 0) {

                    DB::table('orders')
                    ->insert([
                        'price' => $giftCard->price,
                        'user_id' => $refer->id_refer,
                        'status_id' => 36,
                        'notes' => '',
                        'created_at' =>$this->date,
                        'updated_at' => $this->date,
                    ]);

                    $order = DB::table('orders')
                    ->where('orders.user_id',$refer->id_refer)
                    ->orderBy('orders.id', 'DESC')
                    ->first();

                    DB::table('order_products')
                    ->insert([
                        'price' => $giftCard->price,
                        'product_id' => $giftCard->id,
                        'order_id' => $order->id,
                        'created_at' =>$this->date,
                        'updated_at' => $this->date,
                    ]);

                    $user = DB::table('users')
                    ->where('id', $refer->id_refer,)
                    ->first();

                    DB::table('order_address')
                    ->insert([
                        'street' => $user->street,
                        'suburb' => $user->suburb,
                        'city' => $user->city,
                        'state' => $user->state,
                        'zip' => $user->cp,
                        'order_id' => $order->id,
                        'created_at' => $this->date,
                        'updated_at' => $this->date,
                    ]);

                    DB::table('awards')
                    ->where('id',$giftCard->id)
                    ->update([
                        'stock' => $giftCard->stock - 1,
                        'redeem' => $giftCard->redeem + 1,
                    ]);

                    $giftCard = DB::table('awards')
                    ->where('id',1)
                    ->first();

                    // return [$giftCard];

                    if($giftCard->stock == 0){
                        $sendEmail = $this->emailController->productEmpty($giftCard);
                    }

                }

            }

            return [
                'result' => 'ok'
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th
            ];
        }

    }

    public function searchTickets(Request $request){

        try {

            $promo_id = $request->promo_id;
            $user_name = $request->user_name;
            $date_initial = $request->date_initial;
            $date_final = $request->date_final;
            $corteSelected = $request->corteSelected;
            $status = $request->status;



            $tickets = DB::table('tickets')
            ->leftJoin('periods_score', 'tickets.period_score_id', '=', 'periods_score.id')
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
                // 'promo.id as promo_id'
            )
            ->where('tickets.status', $status)
            ->when($user_name, function ($query, $user_name) {
                return $query->where('users.name', 'like', '%'.$user_name.'%');
            })
            ->when($promo_id, function ($query, $promo_id) {
                return $query->where('periods.promo_id', $promo_id);
            })
            ->when($date_initial, function ($query, $date_initial) {
                return $query->where('tickets.date_ticket', '>=', $date_initial);
            })
            ->when($date_final, function ($query, $date_final) {
                return $query->where('tickets.date_ticket', '<=', $date_final);
            })
            ->when($corteSelected, function ($query, $corteSelected) {
                return $query->where('periods.id', '=', $corteSelected);
            })
            ->get();

            // return $tickets;
            $dataTable = "";

            if( $status == 2){
                $textClass = 'text-warning';
            }elseif( $status == 1){
                $textClass = 'text-success';
            }else{
                $textClass = 'text-danger';
            }

            if(count($tickets) > 0){

                foreach ($tickets as $key => $ticket) {

                    $dataTable = $dataTable . "<tr>
                        <th scope='row'>$ticket->id </th>
                        <td>$ticket->name</td>
                        <td>$ticket->promo_name</td>
                        <td>$ticket->date_ticket</td>
                        <td>Corte  $ticket->orderNum</td>
                        <td class='$textClass'>$ticket->status</td>
                        <td style='width: 15vw;' >
                            <a type='button' href='".route('ticketUpdate',['id'=> $ticket->id])."' class='btn btn-success mb-2'>Editar</a>
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

    public function createPDF(){
        /*$users = DB::table('users')
        ->get();

        $data = [];
        foreach ($users as $key => $user) {
            # code...

            $tickets = DB::table('tickets')
            ->leftJoin('users', 'tickets.user_id', '=', 'users.id')
            ->leftJoin('status_catalog', 'tickets.status', '=', 'status_catalog.id')
            ->leftJoin('periods', 'tickets.period_id', '=', 'periods.id')
            ->select('users.name', 'tickets.id', 'tickets.date_ticket',
            'tickets.present1','tickets.present2','tickets.present3',
            'periods.inicial_date','periods.final_date','status_catalog.name as status')
            ->where('tickets.status', 1)
            ->where('tickets.user_id', $user->id)
            ->where('status_catalog.table', 'tickets')
            ->orderBy('tickets.id', 'asc')
            ->get();

            // foreach ($tickets as $key => $value) {
            # code...
            // $arrayTickets = [
            //     $key => [
            //         'name' => $user->name,
            //         $tickets
            //     ]
            // ];
            // }

            $arrUsers = [
                $key =>[
                    'name' => $user->name,
                    $tickets
                ]
            ];

            array_push($data, $arrUsers);
            dd($arrayTickets);
        }

      // share data to view
        //   view()->share('employee',$data);
      $pdf = PDF::loadView('pdf_view', [ 'data' => $data]);

      // download PDF file with download method
      return $pdf->download('pdf_file.pdf');**/
    }

    public function recalculatePeriodScore($pd_id, $status_id){

        try {
            $points = DB::table('tickets')
            ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
            ->select(
                DB::raw('SUM(points) as score')
            )
            ->where([
                ['periods_score.id' ,'=', $pd_id],
                ['tickets.status', '=', $status_id],
            ])
            ->first();

            $score = 0;
            if(isset($points->score)){
                $score = $points->score;
            }

            return $score;
        } catch (\Throwable $th) {
            //throw $th;
            return ['result' => $th];
        }

    }

}
