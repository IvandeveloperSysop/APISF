<?php

namespace App\Http\Controllers\rewardsProgram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\rewardsProgram\promotionController;
use App\Http\Controllers\routeGlobal;

class awardController extends Controller
{
    //
    public $date;
    protected $promotionController;
    protected $routeGlobal;
    public function __construct(promotionController $promotionController, routeGlobal $routeGlobal)
    { 
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
        $this->promotionController = $promotionController;
        $this->routeGlobal = $routeGlobal->index();
    }

    public function addAward(Request $request){

        try {
            //code...
            // Get promo with admin selected
            $promo = DB::table('promo')
            ->where('id',$request->promoId)
            ->first();

          
            $description = $request->awardDescription;
            $name = $request->nameAward;
            
            if($request->image){
                $nameImage = str_replace(' ', '', $name);
                $data = explode( ',', $request->image );
                $pathP = 'img/award/promo'.$promo->id.'/'.$nameImage.'.'.$request->extension;
                $content = base64_decode($data[1]);
                $typeFile = Str::substr($data[0],0,10);
                if($typeFile == 'data:image' ){
                    Storage::disk('public')->put( $pathP, $content);
                }
            }

            // $pathP = 'Hola';

            foreach ($request->positionsArr as $key => $positions) {

                if($positions['check']){

                    DB::table('awards')
                    ->insert([
                        'promo_id' => $promo->id,
                        'image' => $pathP,
                        'name' => $name,
                        'description' => $description,
                        'redeem' => 0,
                        'position' => $key,
                        'status_id' => 26,
                        'created_at' => $this->date,
                        'updated_at' => $this->date,
                    ]);
                }

    
            }
            
            
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
            ->orderBy('awards.id','DESC')
            ->get();

            $tableAwards = $this->writeAwardTable($request->promoId, $request->window); 

            return [
                'result' => 'ok',
                'table' => $tableAwards,
            ];

        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }

    }

    public function updateAward(Request $request){

        try {
            //code...
            // Get promo with admin selected
            $promo = DB::table('promo')
            ->where('id',$request->promoId)
            ->first();

            // return [$request->all()];

            $name = $request->awardName;
            $description = $request->description;
            $position = $request->position;

            if($request->image){
                $nameImage = str_replace(' ', '', $name);
                $data = explode( ',', $request->image );
                $pathP = 'img/award/promo'.$promo->id.'/'.$nameImage.'.'.$request->extension;
                $content = base64_decode($data[1]);
                $typeFile = Str::substr($data[0],0,10);
                if($typeFile == 'data:image' ){
                    Storage::disk('public')->put( $pathP, $content);
                }

                DB::table('awards')
                ->where('awards.id', $request->awardId)
                ->update([
                    'image' => $pathP,
                ]);

            }



            DB::table('awards')
            ->where('awards.id', $request->awardId)
            ->update([
                'promo_id' => $promo->id,
                'name' => $name,
                'description' => $description,
                'position' => $position,
                'updated_at' => $this->date,
            ]);

            
            $tableAwards = $this->writeAwardTable($promo->id,'details'); 

            return [
                'result' => 'ok',
                'table' => $tableAwards,
            ];

        } catch (\Throwable $th) {

            $json = ['resp' => $th];
            return $json;
        }

    }

    public function deleteAward(Request $request){

        try {
            //code...
            DB::table('awards')
            ->where('awards.id', '=', $request->awardId)
            ->delete();
    
            $tableAwards = $this->writeAwardTable($request->promoId, $request->window);
    
            return [
                'result' => 'ok',
                'table' => $tableAwards,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

        
    }

    public function awardsAdmin(){
        try {
            //code...
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
            ->orderBy('awards.position', 'ASC')
            ->paginate(10);

            $promos = DB::table('promo')
            ->where('type_id', 5)
            ->get();
            // dd($awards);
            
            return view('admin.awards.index',['awards' => $awards, 'routeGlobal' => $this->routeGlobal, 'promos' => $promos ]);
            
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }

    public function awardsAdminDetails($id){

        try {
            //code...
            $award = DB::table('awards')
            ->leftJoin('status_catalog as status', 'awards.status_id', '=', 'status.id')
            ->leftJoin('promo', 'awards.promo_id', '=', 'promo.id')
            ->select(
                'awards.id as awardId',
                'awards.name as awardName',
                'awards.description',
                'awards.promo_id as promoAward',
                'awards.image',
                'awards.position',
                'awards.redeem',
                'promo.title',
                'status.name as statusName',
                'status.id as statusId',
            )
            ->where('awards.id',$id)
            ->orderBy('awards.position', 'ASC')
            ->first();

            $selectedPromotions = $this->promotionController->selectedAwards($award->promoAward );

            return [
                'result' => 'ok',
                'award' => $award,
                'selectedPromotions' => $selectedPromotions,
                'imgAward' => "<img src='".$this->routeGlobal.$award->image."' style='width: 100%;max-height: 280px;' alt='$award->awardName' >",
            ];

        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th,
            ];
        }


    }

    public function awardsRedeemedAdmin(){

    }

    // Drawing table in HTML
    public function writeAwardTable($promo_id = null, $window = 'index', $pwa = null){

        try {
            //code...
            $awards = DB::table('awards')
            ->leftJoin('status_catalog as status', 'awards.status_id', '=', 'status.id')
            ->leftJoin('promo', 'awards.promo_id', '=', 'promo.id')
            ->select(
                'awards.id as awardId',
                'awards.name as awardName',
                'awards.image',
                'awards.position',
                'awards.redeem',
                'promo.title as promoTitle',
                'status.name as statusName',
                'status.id as statusId',
            )
            ->where('awards.promo_id', '<>', '1')
            ->where(function($query) use ($window, $promo_id)
            {
                if ( $window == 'details' ) {
                    $query->where('awards.promo_id', $promo_id);
                }

            })
            ->orderBy('awards.position','ASC')
            ->get();
    
            $tableAwards = "";

            if(!$pwa){

                foreach ($awards as $key => $award) {
                    $textClass = 'text-danger';
                    if ( $award->statusId == 26 ){
                        $textClass = 'text-success';
                    }

                    $promoTable = "";
                    if ($window == 'details' ) {
                        $promoTable = "<td>$award->promoTitle </td>";
                    }
        
                    $tableAwards = $tableAwards ."<tr>
                        <td scope='row' style='width: 20%;'>
                        <img src='".$this->routeGlobal.$award->image."' style='width: 40%;' alt='$award->awardName' />
                        </th>
                        <th>$award->awardName</th>
                        <td>$award->position</td>
                        $promoTable
                        <td class='$textClass'>$award->statusName</td>
                        <td>
                            <button type='button' data-bs-toggle='modal' data-bs-target='#viewDetailsAwards' onclick='getDetailsAwards($award->awardId)' class='btn btn-success'><i class='fas fa-eye me-2'></i>Ver detalles</button>
        
                            <button type='button' class='btn btn-danger' onclick='deleteAward($award->awardId, ".'"'.$window.'"'.")' >
                                <i class='fas fa-trash me-2'></i>Borrar premio
                            </button>
                        </td>
                    </tr>";
                }
        
                return $tableAwards;

            }else{
                return [
                    'awards' => $awards,
                    'routeGlobal' => $this->routeGlobal,
                ];
            }
    
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

    }

    public function getPositionsAward(Request $request){

        try {

            $promo = DB::table('promo')
            ->where('id',$request->promoId)
            ->first();

            // return [$request->all()];

    
            $positionsCheckBox = "<label for='user' class='form-label'>Posiciones: </label> <br>";
            for ($i=1; $i < $promo->awards_qty + 1 ; $i++) {

                $validAwards = DB::table('awards')
                ->where([
                    [ 'position', '=' , $i],
                    [ 'promo_id', '=' , $promo->id],
                ])
                ->first();
                
                if( !$validAwards ){
                    $positionsCheckBox = $positionsCheckBox."
                    <div class='form-check form-check-inline text-dark'>
                        <input class='form-check-input' type='checkbox' id='positionCheck$i' name='position[$i]' onchange='checkedPosition(this.checked,$i)' value='true'>
                        <label class='form-check-label' for='inlineCheckbox1'>$i</label>
                    </div>";
                }else{
                    $positionsCheckBox = $positionsCheckBox."
                    <div class='form-check form-check-inline'>
                        <input class='form-check-input' type='checkbox' id='positionCheck$i'  disabled>
                        <label class='form-check-label' for='inlineCheckbox3'>$i</label>
                    </div>";
                }
    
            }

    
            return [
                'result' => $positionsCheckBox,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                $th,
            ];
        }

    }

    public function getPositionsAwardUpdates(Request $request){

        try {

            $promo = DB::table('promo')
            ->where('id',$request->promoId)
            ->first();

           
            // return [$request->all()];
    
            $positionsCheckBox = "<label for='user' class='form-label'>Posiciones: </label> <br>";
            for ($i=1; $i < $promo->awards_qty + 1 ; $i++) {

                $validAwards = DB::table('awards')
                ->where([
                    [ 'position', '=' , $i],
                    [ 'id', '=', $request->awardId ]
                ])
                ->first();

                $validOtherAwards = DB::table('awards')
                ->where([
                    [ 'position', '=', $i],
                    [ 'id', '<>', $request->awardId],
                    [ 'promo_id', '=', $promo->id],

                ])
                ->first();

                // $positionSelected = "";
                if( !$validOtherAwards ){

                    $checked = "";
                    if($validAwards){
                        $checked = "checked";
                        $positionSelected = $i;
                    }

                    $positionsCheckBox = $positionsCheckBox."
                    <div class='form-check form-check-inline text-dark'>
                        <input class='form-check-input' type='radio' name='flexRadioDefault' id='flexRadioDefault$i' $checked  onchange='changePosition($i)'>
                        <label class='form-check-label' for='flexRadioDefault$i'>
                            $i
                        </label>
                    </div>";

                }else{
                    $positionsCheckBox = $positionsCheckBox."
                    <div class='form-check form-check-inline'>
                        <input class='form-check-input' type='radio' name='flexRadioDefault' id='flexRadioDefault$i' disabled >
                        <label class='form-check-label' for='flexRadioDefault$i'>
                        $i
                        </label>
                    </div>";
                }
    
            }

    
            return [
                'result' => $positionsCheckBox,
                'positionSelected' => $positionSelected,
            ];

        } catch (\Throwable $th) {
            //throw $th;
            return [
                $th,
            ];
        }

    }

}
