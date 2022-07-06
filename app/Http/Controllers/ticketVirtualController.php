<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Store;
use Illuminate\Support\Facades\DB;

class ticketVirtualController extends Controller
{

    protected $date;
    public function __construct()
    {
        $this->date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }

    public function createVoucher($points,$ticket){

        try {
            
            $ticketsTotals = intval($points / 100);
    
            $voucher_no = DB::table('vouchers')->max('voucher_no');
    
            for ($i=0; $i < $ticketsTotals; $i++) {

                $voucher_no = $voucher_no + 1;

                // $promo_id = $ticket->promo_id;
                $promo_id = 2;

                DB::table('vouchers')
                ->insert([
                    'promo_id' => $promo_id,
                    'ticket_id' => $ticket->ticket_id,
                    'user_id' => $ticket->user_id,
                    'voucher_no' => $voucher_no,
                    'created_at' => $this->date,
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

    public function getVouchers(Request $request){

        try {
            $date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));

                
            $vouchers = DB::table('vouchers')
            ->leftJoin('users', 'vouchers.user_id', '=', 'users.id')
            ->select(
                'users.name',
                'vouchers.id',
                'vouchers.voucher_no',
                'vouchers.created_at',
            )
            ->where('users.token',$request->token)
            // ->where('periods.promo_id', $request->promo_id)
            // ->where('status_catalog.table', 'tickets')
            ->orderBy('vouchers.voucher_no', 'ASC')
            ->paginate(10);

            // return [$request->all()];

            $arrVouchers  = [];
            foreach ($vouchers as $key => $voucher) {
                
                $arrVoucher = [
                    "voucher_no" => $voucher->voucher_no,
                    'name' => $voucher->name, 
                    'voucher_id' => $voucher->id,
                    'date' => $voucher->created_at,
                ];

                array_push($arrVouchers, $arrVoucher);
            }

            $json = [
                "data" => $arrVouchers,
                "vouchers" => $vouchers,
            ];
            return $json;
        
        } catch (\Throwable $th) {
            //throw $th;
            $json = [
                'message' => $th
            ];
            // echo json_encode($json);
        
            return $json;
        }
    }
}
