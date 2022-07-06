<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Store;
use Illuminate\Support\Facades\DB;

class miniGameController extends Controller
{

    public function index(Request $request){

        try {
            //code...
            $miniGame = DB::table('minigame_score')
            ->leftJoin('tickets', 'minigame_score.ticket_id', '=', 'tickets.id')
            ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
            ->leftJoin('users', 'periods_score.user_id', '=', 'users.id')
            ->leftJoin('status_catalog', 'minigame_score.status', '=', 'status_catalog.id')
            ->select('minigame_score.id as id_minigame', 'users.name as userName', 'tickets.id as id_ticket', 'users.id as user_id',
            'minigame_score.status as statusMinigame', 'status_catalog.name as statusName')
            ->where('minigame_score.id', $request->idMiniGame)
            ->where('minigame_score.status', 22)
            ->where('users.token', $request->token)
            ->first();

            if($miniGame){
                $json = [
                    'message' => 'ok',
                    'minigame' => $miniGame,
                ];
                //echo json_encode($json);

                return $json;
            }else{
                $json = [
                    'message' => 'error',
                    'idMiniGame' => $request->idMiniGame,
                    'token' => $request->token,
                ];
                //echo json_encode($json);

                return $json;
            }
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

    public function getPoints(Request $request){
        try{
            $date =Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
            $id_minigame = $request->id_minigame;
            $level_game = $request->level_score;
            // $points = $request->points;

            if( $level_game < 3){
                $points = 0;
            }elseif($level_game >= 3 && $level_game < 6){
                $points = 1;
            }elseif($level_game >= 6 && $level_game < 9){
                $points = 2;
            }else{
                $points = 3;
            }

            DB::table('minigame_score')
            ->where('id', $id_minigame)
            ->where('status', 22)
            ->update(['status' => 23, 'level_game' => $level_game, 'points' => $points, 'updated_at' => $date]);


            $miniGame = DB::table('minigame_score')
            ->where('minigame_score.id', $id_minigame)
            ->first();

            // return $miniGame;

            $json = $this->extraPointsminigame($miniGame->ticket_id);

            // $json = [
            //     "message" => 'ok'
            // ];
            return $json;
        }catch(\Throwable $th){
            $json = [
                "message" => $th
            ];
            return $json;
        }


    }

    public function notGame(Request $request){
        try {
            //code...
            $date =Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));

            DB::table('minigame_score')
            ->where('id', $request->idMiniGame)
            ->update(['status' => 24, 'updated_at' => $date]);
            $json = [
                "message" => 'ok'
            ];
            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                "message" => $th
            ];
            return $json;
        }
    }

    public function extraPointsminigame($id_ticket, $ps_id){

        try {
            //code...
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));

            $ticket = DB::table('tickets')
            ->leftJoin('periods_score','tickets.period_score_id', '=', 'periods_score.id')
            ->leftJoin('users', 'periods_score.user_id', '=', 'users.id')
            ->leftJoin('periods', 'periods_score.period_id', '=', 'periods.id')
            ->join('minigame_score', 'tickets.id', '=', 'minigame_score.ticket_id')
            ->join('status_catalog', 'minigame_score.status', '=', 'status_catalog.id')
            ->select(
                'users.name as userName',
                'users.id as user_id',
                'tickets.id as id_ticket',
                'tickets.points',
                'tickets.status as statusTicket',
                'periods_score.period_id',
                'periods_score.id as ps_id',
                'minigame_score.id as id_minigame',
                'minigame_score.status as statusMinigame',
                'minigame_score.points as minigamePoints',
                'status_catalog.name as statusName',
            )
            ->where('tickets.id', $id_ticket)
            ->first();

            // return [$ticket];

            // Si el Administrador aprobo el ticket y tiene un minigame ya jugado se trae los puntos y los suma, solo si el ticket es mayor a los puntos que acordemos como minimo
            if ($ticket->statusTicket == 1 && $ticket->statusMinigame == 23 && $ticket->points > 20){

                DB::table('extra_points')->insert([
                    'user_id' => $ticket->user_id,
                    'period_id' => $ticket->period_id,
                    'ticket_id' => $id_ticket,
                    'type_id' => 2,
                    'points' => $ticket->minigamePoints,
                    'status' => 6,
                    'refer_or_minigames_id' => $ticket->id_minigame,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);

                $score = DB::table('periods_score')
                ->where('id', $ticket->ps_id)
                ->first();

                $extra_points = DB::table('extra_points')
                ->where([
                    ['user_id', '=', $ticket->user_id],
                    ['period_id', '=', $ticket->period_id],
                    ['ticket_id', '=', $ticket->id_ticket],
                ])
                ->orderBy('id', 'DESC')
                ->first();

                $pointsScore = $score->score + $ticket->minigamePoints;

                DB::table('periods_score')
                ->where('id', $ticket->ps_id)
                ->update(['score' => $pointsScore, 'updated_at' => $date]);


                DB::table('minigame_score')
                ->where('id', $ticket->id_minigame)
                ->update([
                    'status' => 21,
                    'updated_at' => $date,
                    'extra_point_id' => $extra_points->id
                ]);

                DB::table('tickets')
                ->where('id', $ticket->id_ticket)
                ->update([
                    'total_points' => $pointsScore,
                ]);

                $wallet = DB::table('wallets')
                ->where('wallets.user_id', $ticket->user_id)
                ->first();

                DB::table('wallets')
                ->where('wallets.id', $wallet->id)
                ->update([
                    'wallets.balance' => $wallet->balance + $ticket->minigamePoints
                ]);

                $json = [
                    "message" => 'changeOk',
                    'extra_points' => $extra_points,
                ];

            }elseif($ticket->statusTicket == 0){
                DB::table('minigame_score')
                ->where('id', $ticket->id_minigame)
                ->update(['status' => 20 , 'updated_at' => $date]);

                $json = [
                    "message" => 'changecancel'
                ];
            }else{
                $json = [
                    "message" => 'nothing'
                ];
            }
            return $json;
        } catch (\Exception $e) {
            //throw $th;
            $json = [
                "message" => $e->getMessage(),
            ];

            return $json;
        }



    }

}
