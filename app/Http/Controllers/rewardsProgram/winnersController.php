<?php

namespace App\Http\Controllers\rewardsProgram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\routeGlobal;

class winnersController extends Controller
{
    protected $routeGlobal;
    public $date;

    public function __construct(routeGlobal $routeGlobal){ 
        $this->routeGlobal = $routeGlobal->index();
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }


    public function getWinnersPeriod($id, Request $request) {

        $top_winners = DB::table('top_winners')
        ->leftJoin('users', 'top_winners.user_id', '=', 'users.id')
        ->leftJoin('awards', 'top_winners.award_id', '=', 'awards.id')
        ->select(
            'top_winners.position',
            'users.name',
            'award_id as points',
            'awards.name as awardName',
        )
        ->where('period_id', $id)
        ->get();

        return $top_winners;
    }

    public function searchUsers(Request $request){

        try {

            $promo = DB::table('promo')
            ->leftJoin('periods', 'promo.id', '=', 'periods.promo_id')
            ->select(
                'promo.awards_qty',
                'promo.id as promoId',
                'periods.order as corte',
            )
            ->where('promo.id',$request->promoId)
            ->first();

            $corte = $promo->corte;
            $promoId = $promo->promoId;
            $usersScore = DB::table('periods_score as score')
            ->leftJoin('users', 'score.user_id', '=', 'users.id')
            ->leftJoin('top_winners as winners', function ($join) use($promoId) {
                $join->on('users.id', '=', 'winners.user_id')
                ->where('winners.promo_id', $promoId);
            })
            ->select(
                'users.name',
                'users.id as userId',
                'users.imageUrl',
                'winners.position',
            )
            ->where('score.period_id', $request->promoId)
            ->orderBy('winners.position', 'ASC')
            ->get();

            $table = "";
            
            $usersValid = array();
            foreach ($usersScore as $key => $user) {
                
                $checked = '';
                $awardName = '';
                $disabled = '';
                $disabledPosition = 'disabled';
                $positionsOptions = "<option value='0'>Selecciona la posición</option>";

                $updatePosition = "<button type='button' class='btn btn-success' onclick='enableUpdatePosition($user->userId)'>Habilitar</button>";

                if($user->position){
                    $checked = 'checked';
                    $disabled = 'disabled';

                    for ($i=1; $i < $promo->awards_qty + 1 ; $i++) {
                
                        $selectedPosition = '';
                        if($i == $user->position){
                            $selectedPosition = 'selected';
                        }

                        // Si a otro usuario ya tiene asignado la posición no se mostrara en el select 
                        $validPositionAssigned = DB::table('top_winners')
                        ->where([
                            ['top_winners.promo_id', '=', $promo->promoId],
                            ['top_winners.position', '=', $i],
                            ['top_winners.user_id', '<>', $user->userId],
                        ])
                        ->first();

                        if(!$validPositionAssigned){
                            $positionsOptions = $positionsOptions . "<option value='$i' $selectedPosition>$i</option>";
                        }

                        
                    }
                    
                    // En esta consulta se valida si ya existe el usurio (si ya existe se hace un push al arrat de usersValid y en Js se utilizar para el update)
                    $userWinner = DB::table('top_winners')
                    ->where([
                        ['top_winners.promo_id', '=', $promo->promoId],
                        ['top_winners.user_id', '=', $user->userId],
                    ])
                    ->first();

                    array_push($usersValid, [ 'userId' => $user->userId, 'position' => $userWinner->position]);

                    $award = DB::table('awards')
                    ->where([
                        ['awards.promo_id', '=', $request->promoId],
                        ['awards.position', '=', $user->position],
                    ])
                    ->first();

                    $awardName = $award->name;

                    // Para los usuarios que ya esta registrados como ganadores se les mostrara un boton en lugar de un check box para poder modificar la posición
                }

                
                $table= $table. "<tr>
                <td> $user->name </td>
                    <td style='width:390px;'> 
                
                        <select class='form-select ' id='positionSelectedWinner$user->userId' aria-label='Floating label select example' onchange='searchAwardPosition($user->userId )' $disabledPosition>
                            $positionsOptions
                        </select>
                        <div id='selectOptionsPosition' class='form-text'>Para poder habilitar este campo se necesita habilitar el campo al seleccionar que este usuario va a ser uno de los ganadores.</div>
                    </td>
                    <td>
                        <input type='text' class='form-control' id='awardWinner$user->userId' value='$awardName' disabled readonly>
                    </td>
                    <td>
                        <div>
                            $updatePosition
                        </div>                                                          
                    </td>
                </tr>";
            }


            return [
                'result' => 'ok',
                'table' => $table,
                'awards_qty' => $promo->awards_qty,
                'usersValid' => $usersValid,
            ];

        } catch (\Throwable $th) {
           return [
               'result' => $th,
           ];
        }


    }

    public function insertWinnersAdmin(Request $request){

        try {

            $promo = DB::table('promo')
            ->leftJoin('periods', 'promo.id', '=', 'periods.promo_id')
            ->select(
                'promo.awards_qty',
                'promo.id as promoId',
                'periods.order as corte',
                'periods.id as periodId',
            )
            ->where('promo.id',$request->promoId)
            ->first();

            foreach ($request->awardQtyArr as $position => $winner) {


                if($winner){
                    
                    if($promo){
                        $award = DB::table('awards')
                        ->where([
                            ['awards.promo_id', '=', $promo->promoId],
                            ['awards.position', '=', $position],
                        ])
                        ->first();
    
                        // Valida   que el usuario ya esta registrado: si el usuario esta registrado hace el update si no hace el insert
                        $validWinner = DB::table('top_winners')
                        ->where([
                            ['user_id', '=', $winner],
                        ])
                        ->first();

                        if($validWinner){

                            if($position != $validWinner->position){
                                DB::table('top_winners')
                                ->where('user_id', $winner)
                                ->update([
                                    'position' => $position,
                                    'award_id' => $award->id,
                                ]);

                            }

                        }else{

                            DB::table('top_winners')
                            ->insert([
                                'user_id' => $winner, 
                                'position' => $position,
                                'award_id' => $award->id, 
                                'period_id' => $promo->periodId,
                                'promo_id' => $promo->promoId,
                            ]);
                        }
    
                    }
                }

    
            }

            $tableWinners = $this->writeWinnersTable($promo->promoId);
    
            return [
                'result' => 'ok',
                'table' => $tableWinners,
                'message' => "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Ganadores cargados exitosamente
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>",
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }

    }
    
    public function getAwardWinner(Request $request){

        try {

            $award = DB::table('awards')
            ->where([
                ['awards.promo_id', '=', $request->promoId],
                ['awards.position', '=', $request->positionUser],
            ])
            ->first();
    
            if( $award ){
                return [
                    'result' => 'ok',
                    'name' => $award->name,
                ];
            }else{
                return [
                    'result' => 'nonAward',
                    'message' => "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        El poducto de esta posición aún no ha sido cargado
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>",
                ];
            }

        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }

    }

    public function deleteWinner(Request $request){

        try {
            
            $winner = DB::table('top_winners')
            ->where('top_winners.id', '=', $request->winnerId)
            ->first();

            DB::table('top_winners')
            ->where('top_winners.id', '=', $request->winnerId)
            ->delete();
    
            $tableWinners = $this->writeWinnersTable($winner->promo_id);
    
            return [
                'result' => 'ok',
                'table' => $tableWinners,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }


    }

    public function writeWinnersTable($promoId){

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
        ->where('winners.promo_id', $promoId)
        ->orderBy('winners.period_id', 'ASC')
        ->orderBy('winners.position','ASC')
        ->get();

        $tableWinner = "";

        foreach ($winners as $key => $winner) {
            $tableWinner = $tableWinner . "
            <tr>
                <td scope='row'>  $winner->position </td>
                <td scope='row'>  $winner->name </td>
                <td scope='row'>  $winner->award </td>
                <td scope='row' style='width: 20%;'>
                    <img src=' ".$this->routeGlobal.$winner->image."' style='width: 100px;' alt=' $winner->award ' />
                </th>
                <td>
                    <button type='button' class='btn btn-danger' onclick='deleteWinner( $winner->id )' >
                        <i class='fas fa-trash me-2'></i>Borrar premio
                    </button>
                </td>
            </tr>";
        }

        return $tableWinner;

    }

}
