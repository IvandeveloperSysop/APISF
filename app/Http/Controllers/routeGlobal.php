<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class routeGlobal extends Controller
{
    public function index(){

        // return "http://tabletizate.demo:8000/storage/app/public/";
        // return "http://api.sysop.info/xelha/storage/app/public/";
        return "https://api.desafiosoccerfans.com/storage/app/public/";
    }
}
