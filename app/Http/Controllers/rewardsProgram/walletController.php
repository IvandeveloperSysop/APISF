<?php

namespace App\Http\Controllers\rewardsProgram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Session;

class walletController extends Controller
{
    //
    public function walletsAdmin(){

        $userWallets = DB::table('users')
        ->leftJoin('wallets', 'users.id', '=', 'wallets.user_id')
        ->select(
            'users.id as userId',
            'users.name',
            'users.nickName',
            'users.email',
            'wallets.balance',
        )
        ->paginate(10);
        
        // dd($userWallets);
        return view('admin.wallets.wallets',['wallets' => $userWallets]);

    }

    public function walletUserAdmin($user_id, Request $request){

        try {

            $transactions = DB::table('transactions')
            ->leftJoin('wallets', 'transactions.wallet_id', '=', 'wallets.user_id')
            ->select(
                DB::raw('DATE_FORMAT(transactions.created_at, "%d/%m/%Y") as date'),
                "transactions.title",
                "transactions.amount",
                "transactions.type_id",
                "transactions.wallet_id",
                "transactions.promo_id",
            )
            ->where('wallets.user_id', '=', $user_id)
            ->get();

            // return [
            //     $transactions
            // ];

            $tableTransaction = "";
            foreach ($transactions as $key => $transaction) {

                if( $transaction->type_id == 8){
                    $iconBalance = "<i style='font-size: 25px;' class='fas fa-angle-double-down text-danger'></i>";
                }else{
                    $iconBalance = "<i style='font-size: 25px;' class='fas fa-angle-double-up text-success'></i>";
                    
                }

                $tableTransaction = $tableTransaction."
                <tr>
                    <th scope='row'> $transaction->title </th>
                    <td> $transaction->amount pts </td>
                    <td> $transaction->date </td>
                    <td> $iconBalance </td>
                </tr>"; 
                
            }
            
            return [
                'result' => 'ok',
                'table' => $tableTransaction,
            ];

            
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th
            ];
        }

    }

    public function registerWallet($user_id, $date){

        try {
            //code...
            DB::table('wallets')
            ->insert([
                'user_id' => $user_id,
                'balance' => 0,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return ['result' => $th];
        }

    }

    public function getUserHistoryBalance(Request $request){
        try {
            //code...
            $balance = DB::table('users')
            ->leftJoin('wallets', 'users.id', '=', 'wallets.user_id')
            ->select([
                'users.id',
                'wallets.balance',
                'wallets.id as walletId',
            ])
            ->where('users.token',$request->token)
            ->first();

            
    
            $transactions = DB::table('transactions')
            ->select(
                DB::raw('DATE_FORMAT(transactions.created_at, "%d/%m/%Y") as date'),
                "transactions.title",
                "transactions.amount",
                "transactions.type_id",
                "transactions.wallet_id",
                "transactions.promo_id",
            )
            ->where('transactions.wallet_id', $balance->walletId)
            ->get();
    
    
            return [
                'balance' => $balance,
                'transactions' => $transactions,
                // 'balanceTotal' => $balanceTotal
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'result' => $th
            ];
        }
    }

    public function getUserBalance($token, $user_id = null){

        try {
            //code...
            $balance = DB::table('users')
            ->leftJoin('wallets', 'users.id', '=', 'wallets.user_id')
            ->select([
                'users.id',
                'wallets.balance',
            ])
            ->where(function($query) use ($token, $user_id)
            {
                if ($token) {
                    $query->where('users.token', '=',$token);
                }

                if ($user_id) {
                    $query->where('users.id', '=', $user_id);
                }
            })
            ->first();
    
    
            return [
                'balance' => $balance
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

}
