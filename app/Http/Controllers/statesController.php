<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\periodsController;
use App\Http\Controllers\routeGlobal;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use File;
use Response;

class statesController extends Controller
{
    //
    public function getStates(Request $request){
        $states = DB::table('states')
        ->where('promo_id', $request->promo_id)
        ->get();

        return $states;
    }
}
