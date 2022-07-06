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

class presentationController extends Controller
{
    
    public $date;

    public function __construct(){ 
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }

    public function getPresentations(Request $request){

        if($request->promo != 1){

            $presentations = DB::table('presentation')
            ->whereIn('promo_id', [1, $request->promo])
            ->where('status_id', 41)
            ->get();

        }else{

            $presentations = DB::table('presentation')
            ->where('status_id', 41)
            ->get();

        }

        return $presentations;
    }

    public function adminPresentations(){

        $presentations = DB::table('presentation')
        ->leftJoin('promo', 'presentation.promo_id', '=', 'promo.id')
        ->select(
            'promo.title as promoName',
            'presentation.id as presentationId',
            'presentation.name as presentationName',
            'presentation.pointValue as points',
        )
        ->where('presentation.status_id', '<>', 43)
        ->orderBy('presentation.name','ASC')
        ->paginate(15);


        $presentationSelect = DB::table('presentation')
        ->leftJoin('promo', 'presentation.promo_id', '=', 'promo.id')
        ->select(
            'promo.title as promoName',
            'presentation.id as presentationId',
            'presentation.name as presentationName',
            'presentation.pointValue as points',
        )
        ->get();

        return view('admin.presentations.index',['presentations' => $presentations, 'presentationSelect' => $presentationSelect]);

    }

    public function adminAddPresentation(Request $request){

        try {
            //code...
            DB::table('presentation')
            ->insert([
                'name' => $request->presentationName,
                'status_id' => 42,
                'promo_id' => $request->promoId,
                'pointValue' => $request->presentationPoints,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);

            $tablePresentations = $this->writePresentationTable();

            return [
                'result' => 'ok',
                'table' => $tablePresentations,
            ];

        } catch (\Throwable $th) {
            //throw $th;
            return [
                'return' => $th
            ];
        }
        return [
            'return' => $request->all()
        ];

    }

    public function presentationUpdate(Request $request){

        try {
            DB::table('presentation')
            ->where('presentation.id', $request->presentationId)
            ->update([
                'name' => $request->presentationName,
                'promo_id' => $request->promoId,
                'pointValue' => $request->points,
                'updated_at' => $this->date,
            ]);
    
            $table = $this->writePresentationTable();
    
            return [
                'result' => 'ok',
                'table' => $table,
            ];
            
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

    public function getDetailsPresentation($id){
        $presentation = DB::table('presentation')
        ->leftJoin('promo', 'presentation.promo_id', '=', 'promo.id')
        ->select(
            'promo.title as promoName',
            'promo.id as promoId',
            'presentation.id as presentationId',
            'presentation.name as presentationName',
            'presentation.pointValue as points',
        )
        ->where('presentation.id', $id)
        ->first();

        $promos = DB::table('promo')
        ->where('status_id', '<>',25)
        ->get();

        $selectedPromo = "";
        
        foreach ($promos as $key => $promo) {
            
            $selected = "";

            if( $promo->id == $presentation->promoId){
                $selected = "selected";
            }

            $selectedPromo = $selectedPromo . "<option value='$promo->id' $selected>$promo->title</option>";
        }
    
            
        return [
            'selected' => $selectedPromo,
            'presentation' => $presentation
        ];
    }

    public function downPresentation(Request $request){
        try {
            //code...
            DB::table('presentation')
            ->where('presentation.id', '=', $request->presentation_id)
            ->update([
                'status_id' => 43
            ]);
    
            $tablePresentation = $this->writePresentationTable();
    
            return [
                'result' => 'ok',
                'table' => $tablePresentation,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }
    }

    public function writePresentationTable(){

        try {
            //code...
            $presentations = DB::table('presentation')
            ->leftJoin('promo', 'presentation.promo_id', '=', 'promo.id')
            ->select(
                'promo.title as promoName',
                'presentation.id as presentationId',
                'presentation.name as presentationName',
                'presentation.pointValue as points',
            )
            ->where('presentation.status_id', '<>', 43)
            ->orderBy('presentation.name','ASC')
            ->get();
    
            $tablePresentation = "";
    
            foreach ($presentations as $key => $presentation) {
    
                $tablePresentation = $tablePresentation ."<tr>
                    <th>$presentation->presentationName</td>
                    <td>$presentation->promoName</td>
                    <td>$presentation->points</td>
                    <td>
                    <button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#viewDetailsPresentation' onclick='getPresentation($presentation->presentationId)' ><i class='fas fa-eye me-2'></i>Ver detalles</button>
                    <button type='button' class='btn btn-danger' onclick='downPresentation($presentation->presentationId)' >
                        <i class='fas fa-trash me-2'></i>Borrar presentación
                    </button>
                    </td>
                </tr>";
            }
    
            return $tablePresentation;
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

    }


    // ------------------------------------------------------
    // -------------------------Non ARCA---------------------
    // ------------------------------------------------------


    public function adminPresentationsNonArca(){

        $presentations = DB::table('presentation_not_arca as presentation')
        ->leftJoin('type_presentation', 'presentation.typePresentation_id', '=', 'type_presentation.id')
        ->select(
            'type_presentation.name as typePresentation',
            'presentation.id as presentationId',
            'presentation.name as presentationName',
        )
        ->where('presentation.status_id', '<>', 43)
        ->orderBy('presentation.name','ASC')
        ->paginate(15);

        return view('admin.presentations.nonArca.index',['presentations' => $presentations]);

    }

    public function downPresentationNonArca(Request $request){
        try {
            //code...
            DB::table('presentation_not_arca')
            ->where('id', '=', $request->presentation_id)
            ->update([
                'status_id' => 43
            ]);
    
            $tablePresentation = $this->writePresentationTableNonArca();
    
            return [
                'result' => 'ok',
                'table' => $tablePresentation,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }
    }

    public function presentationUpdateNonArca(Request $request){


        try {
            DB::table('presentation_not_arca as presentation')
            ->where('presentation.id', $request->presentationId)
            ->update([
                'name' => $request->presentationName,
                'typePresentation_id' => $request->typeId,
                'updated_at' => $this->date,
            ]);
    
            // $table = $this->writePresentationTableNonArca();
    
            return [
                'result' => 'ok',
                // 'table' => $table,
            ];
            
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

    public function getDetailsPresentationNonArca($id){
        try {
            $presentation = DB::table('presentation_not_arca as presentation')
            ->leftJoin('type_presentation', 'presentation.typePresentation_id', '=', 'type_presentation.id')
            ->select(
                'type_presentation.name as typePresentation',
                'type_presentation.id as typeId',
                'presentation.id as presentationId',
                'presentation.name as presentationName',
            )
            ->where('presentation.id', $id)
            ->first();

    
            $types = DB::table('type_presentation')
            ->get();
    
            $selected = "";
            
            foreach ($types as $key => $type) {
                
                $select = "";
    
                if( $presentation->typeId == $type->id){
                    $select = "selected";
                }
    
                $selected = $selected . "<option value='$type->id' $select>$type->name</option>";
            }
        
                
            return [
                'selected' => $selected,
                'presentation' => $presentation
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }
    }

    public function addPresentationNonArca(Request $request){

        try {
            DB::table('presentation_not_arca')->insert([
                [
                    'typePresentation_id' => $request->typeId,
                    'name' => $request->presentationName,
                    'status_id' => 33,
                    'promo_id' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ],
            ]);
    
            $table = $this->writePresentationTableNonArca();
    
            return [
                'result' => 'ok',
                'table' => $table,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th
            ];
        }

    }

    public function typesPresentationNonArca(Request $request){

        try {
            $types = DB::table('type_presentation')
            ->get();
    
            $selected = "";
            
            foreach ($types as $key => $type) {
                
    
                $selected = $selected . "<option value='$type->id'>$type->name</option>";
            }
    
            return [
                'selected' => $selected,
                'result' => 'ok',
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

    public function writePresentationTableNonArca(){

        try {
            //code...
            $presentations = DB::table('presentation_not_arca as presentation')
            ->leftJoin('type_presentation', 'presentation.typePresentation_id', '=', 'type_presentation.id')
            ->select(
                'type_presentation.name as typePresentation',
                'presentation.id as presentationId',
                'presentation.name as presentationName',
            )
            ->orderBy('presentation.name','ASC')
            ->get();
    
            $tablePresentation = "";
    
            foreach ($presentations as $key => $presentation) {
    
                $tablePresentation = $tablePresentation ."<tr>
                    <th>$presentation->presentationName</td>
                    <td>$presentation->typePresentation</td>
                    <td>
                    <button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#viewDetailsPresentation' onclick='getPresentationDetails($presentation->presentationId)' ><i class='fas fa-eye me-2'></i>Ver detalles</button>
                    <button type='button' class='btn btn-danger' onclick='downPresentation($presentation->presentationId)' >
                        <i class='fas fa-trash me-2'></i>Borrar premio
                    </button>
                    </td>
                </tr>";
            }
    
            return $tablePresentation;
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

    }

}
