<?php

namespace App\Http\Controllers\rewardsProgram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class adminValidators extends Controller
{
    public $date;
    public function __construct(){ 
        $this->date = $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }

    public function getUserValidators(Request $request){

        if($request->session()->exists('admin_id')){

            $validators = DB::table('admin_users')
            ->where('admin_users.type_id', '2')
            ->orderBy('admin_users.name', 'ASC')
            ->paginate(10);

            $status = DB::table('status_catalog')
            ->where('table', 'adminUsers')
            ->get();
    
            // dd($validators);
    
            return view('admin.validators.index',['validators' => $validators, 'status' => $status]);
        }else{
            // dd('hola');
            return redirect()->route('admin');
        }

    }

    public function addUserValidators(Request $request){

        try {
            //code...
            DB::table('admin_users')
            ->insert([
                'name' => $request->nameUser,
                'email' => $request->email,
                'password' => md5($request->password),
                'type_id' => 2,
                'status_id' => 44,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);

            return ['result' => 'ok'];
        } catch (\Throwable $th) {
            return ['result' => $th];
        }

    }

    public function getUserValidatorDetails($id){

        try {

            $user = DB::table('admin_users')
            ->where('id', $id)
            ->first();
    
            return [
                'user' => $user,
                'result' => 'ok'
            ];

        } catch (\Throwable $th) {

            return [
                'result' => $th
            ];

        }

    }

    public function downValidators(Request $request){

        try {
            
            DB::table('admin_users')
            ->where('id',$request->id)
            ->update([
                'status_id' => $request->status,
            ]);

            return [
                'result' => 'ok',
            ];

        } catch (\Throwable $th) {
            
            return [
                'result' => $th,
            ];

        }

    }

    public function updateValidators(Request $request){

        try {

            DB::table('admin_users')
            ->where('id',$request->userId)
            ->update([
                'name' => $request->nameUser,
                'email' => $request->email,
                'updated_at' => $this->date,
            ]);
    
            if($request->password){
                DB::table('admin_users')
                ->where('id',$request->userId)
                ->update([
                    'password' => md5($request->password),
                    'updated_at' => $this->date,
                ]);
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
}
