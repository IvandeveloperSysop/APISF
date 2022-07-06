<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class imageGlobalController extends Controller
{
    //
    public function getImageG(Request $request){

        try {
            //code...
            // $imageAward = DB::table('image_global')
            // ->where('type', $request->type)
            // ->where('promo_id', $request->promo)
            // ->first();

            $videoAward = DB::table('video_global')
            ->where('type', $request->type)
            ->first();
    
            $json = [
                // 'imagePath' => $imageAward->pathImage,
                'videoPath' => $videoAward->pathVideo,
            ];
    
            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                'result' => $th
            ];
            return $json;
        }
    }

    public function getImageGCampaign(Request $request){
        try {
            //code...
            $imageAward = DB::table('image_global')
            ->where('type', 'award')
            ->where('promo_id', $request->promo)
            ->first();

            $videoAward = DB::table('video_global')
            ->where('type', $request->type)
            ->where('promo_id', $request->promo)
            ->first();
    
            $json = [
                'imagePath' => $imageAward->pathImage,
                'videoPath' => $videoAward->pathVideo,
            ];
    
            return $json;
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                'result' => $th
            ];
            return $json;
        }
    }
    
}
