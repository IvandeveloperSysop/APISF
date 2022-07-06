<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\routeGlobal;
use Carbon\Carbon;


class filtersController extends Controller
{

    protected $routeGlobal;
    public $date;
    public function __construct( routeGlobal $routeGlobal){ 
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
        $this->routeGlobal = $routeGlobal->index();
    }

    
    function searchPromotion(Request $request){

        try {
        
            $promo_id = $request->promo_id;
            $date_initial = $request->date_initial;
            $date_final = $request->date_final;


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
            ->when($promo_id, function ($query, $promo_id) {
                return $query->where('promo.id', $promo_id);
            })
            ->when($date_initial, function ($query, $date_initial) {
                return $query->where('promo.begin_date', '>=', $date_initial);
            })
            ->when($date_final, function ($query, $date_final) {
                return $query->where('promo.end_date', '<=', $date_final);
            })
            ->get();


            $dataTable = "";

            if(count($promos) > 0){

                foreach ($promos as $key => $promo) {
                    if( $promo->statusId == 24){
                        $classStatus = "text-success";
                    }elseif($promo->statusId == 25){
                        $classStatus = "text-danger";
                    }else{
                        $classStatus = "text-warning";
                    }

                    $dataTable = $dataTable . "<tr>
                        <td scope='row'> $promo->title </td>
                        <td class='$classStatus'>$promo->name</td>
                        <td>$promo->awards_qty</td>
                        <td>$promo->begin_date</td>
                        <td>$promo->end_date</td>
                        <td style='width: 16vw;' >
                            <a type='button' href='".route('promoDetails',['id'=> $promo->id])."' class='btn btn-success '>Editar</a>
                            <a type='button' onclick='downPromo($promo->id)' class='btn btn-danger'>Dar de baja</a>
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

    function searchAwards(Request $request){

        try {
        
            $promo_id = $request->promo_id;
            // $date_initial = $request->date_initial;
            // $date_final = $request->date_final;


            $awards = DB::table('awards')
            ->leftJoin('status_catalog as status', 'awards.status_id', '=', 'status.id')
            ->leftJoin('promo', 'awards.promo_id', '=', 'promo.id')
            ->select(
                'awards.id as awardId',
                'awards.promo_id as promoId',
                'awards.name as awardName',
                'awards.image',
                'awards.stock',
                'awards.position',
                'awards.redeem',
                'promo.title as promoTitle',
                'status.name as statusName',
                'status.id as statusId',
            )
            ->where('awards.promo_id', '<>', '1')
            ->when($promo_id, function ($query, $promo_id) {
                return $query->where('awards.promo_id', $promo_id);
            })
            ->orderBy('awards.position','ASC')
            ->get();


            $dataTable = "";

            if(count($awards) > 0){

                foreach ($awards as $key => $award) {
                    $textClass = 'text-danger';
                    if ( $award->statusId == 26 ){
                        $textClass = 'text-success';
                    }

                    $dataTable = $dataTable . "<tr>
                        <td scope='row' style='width: 20%;'>
                            <img src=' ".$this->routeGlobal.$award->image."' style='width: 100px;' alt=' $award->awardName ' />
                        </th>
                        <th> $award->awardName </td>
                        <th> $award->promoTitle </td>
                        <td> $award->position </td>
                        <td class=' $textClass '>
                            $award->statusName 
                        </td>
                        <td>
                            <button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#viewDetailsAwards' onclick='getDetailsAwards( $award->awardId ,  $award->promoId )' ><i class='fas fa-eye me-2'></i>Ver detalles</button>
                            <button type='button' class='btn btn-danger' onclick='deleteAward( $award->awardId )' >
                                <i class='fas fa-trash me-2'></i>Borrar premio
                            </button>
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

    function searchValidators(Request $request){

        try {
        
            $nameUser = $request->nameUser;
            $email = $request->email;
            $status = $request->status;


            $users = DB::table('admin_users')
            ->leftJoin('status_catalog as status', 'admin_users.status_id', '=', 'status.id')
            ->select(
                'admin_users.name',
                'admin_users.id',
                'admin_users.email',
                'status.name as statusName',
                'status.id as status_id',
            )
            ->where('type_id',2)
            ->when($nameUser, function ($query, $nameUser) {
                return $query->where('admin_users.name', 'like', '%'.$nameUser.'%');
            })
            ->when($email, function ($query, $email) {
                return $query->where('admin_users.email', 'like', '%'.$email.'%');
            })
            ->when($status, function ($query, $status) {
                return $query->where('admin_users.status_id', 'like', '%'.$status.'%');
            })
            ->orderBy('admin_users.name','ASC')
            ->get();


            $dataTable = "";

            if(count($users) > 0){

                foreach ($users as $key => $user) {


                    if ($user->status_id == 44){

                        $textClass = 'text-success';
                        $statusName = 'Activo';
                        $buttons = "<button type='button' class='btn btn-danger' onclick='downAdminUser( $user->id , 45)' >
                            <i class='fas fa-trash me-2'></i>Quitar acceso
                        </button>";

                    }else{
                        $textClass = 'text-danger';
                        $statusName = 'Inactivo';
                        $buttons = "<button type='button' class='btn btn-success' onclick='downAdminUser( $user->id , 44)' >
                            Dar acceso
                        </button>";

                    }
                    
                    $dataTable = $dataTable . "<tr>
                        <th> $user->name </th>
                        <td> $user->email </td>
                        <td class='$textClass'>$statusName</td>
                        <td>
                            <button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#viewDetailsValidator' onclick='getValidatorDetails( $user->id )' ><i class='fas fa-eye me-2'></i>Ver detalles</button>
                            $buttons
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

    function searchPresentations(Request $request){

        try {
        
            $presentation_id = $request->presentation_id;
            // $date_initial = $request->date_initial;
            // $date_final = $request->date_final;


            $presentations = DB::table('presentation')
            ->leftJoin('promo', 'presentation.promo_id', '=', 'promo.id')
            ->select(
                'promo.title as promoName',
                'presentation.id as presentationId',
                'presentation.name as presentationName',
                'presentation.pointValue as points',
            )
            ->where('presentation.id', '<>', '1')
            ->when($presentation_id, function ($query, $presentation_id) {
                return $query->where('presentation.id', $presentation_id);
            })
            ->orderBy('presentation.name','ASC')
            ->get();


            $dataTable = "";

            if(count($presentations) > 0){

                foreach ($presentations as $key => $presentation) {

                    $dataTable = $dataTable . "<tr>
                        <th> $presentation->presentationName </th>
                        <td> $presentation->promoName </td>
                        <td> $presentation->points </td>
                        <td>
                            <button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#viewDetailsAwards' onclick='getDetailsAwards( $presentation->presentationId )' ><i class='fas fa-eye me-2'></i>Ver detalles</button>
                            <button type='button' class='btn btn-danger' onclick='deleteAward( $presentation->presentationId )' >
                                <i class='fas fa-trash me-2'></i>Borrar premio
                            </button>
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

}
