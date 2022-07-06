<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class campaignController extends Controller
{
    //
    public function getCampaign(Request $request){


        $campaign = DB::table('campaign')
        ->where('slug', $request->slug)
        ->first();
        
        $messages = DB::table('messages')
        ->where('section', $request->section)
        ->orderBy("id",'DESC')
        ->get();

        DB::table('campaign')
        ->where('id', $campaign->id)
        ->update(['visitors' => $campaign->visitors + 1]);

        $json = [];
        foreach ($messages as $key => $message) {
            $json[$message->title] = $message->content;
        }

        // $json = [
        //     'messages' => $messages
        // ];
        return $json;
    }
}
