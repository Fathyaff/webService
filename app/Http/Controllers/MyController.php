<?php

namespace App\Http\Controllers;
use File;
use Carbon;
use App\Clients;
use Storage;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class MyController extends Controller
{
    public function ping(){
        $pong = 1;
        return response()->json(array('pong'=>$pong));
    }

    public static function getQuorum(){
        $totalQuorum = 0;
        $client = new Client();
        $res = $client->request('GET', 'http://152.118.31.2/list.php', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json'
        ]]);
        
        $bodyResp = $res->getBody();
        $array = json_decode($bodyResp, true);
        for($i = 0; $i < 10 ; $i++){
            $activeIP = $array[$i]['ip'];
            $client2 = new Client();
            $resp = $client2->request('POST', $activeIP."/ewallet/ping", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
            ]]);
            $quorumResponse = json_decode($resp->getBody());
            $pong = $quorumResponse['pong'];
            if($pong == 1){
                $totalQuorum += 1;
            }
        }
        return $totalQuorum;
    }

    public function transfer(Request $request){
        $user_id = $request->user_id;
        $nilai = $request->nilai;

        if($nilai < 0 || $nilai >= 1000000000){
            $status_transfer = -5;
        }

        $quorum = MyController::getQuorum();
        if($quorum >= 5){
            try{
                $client = Clients::where('id', $user_id)->first();
                if($client == null){
                    //client belum terdaftar
                    $status_transfer = -1;
                }else{
                    //check nilai transfer
                    $saldo = $client->saldo;
                    $newSaldo = $saldo + $nilai;
                    $update = $client;
                    $update->saldo = $newSaldo;
                    $update->update();
                    
                    $status_transfer = 1;
                }                
                
                if(Client::where('id', $user_id)->first() != null){
                    $status_transfer = 1;
                }
            }catch(\Illuminate\Database\QueryException $ex){
                $status_transfer = -4;
                dd($ex);
            }
        }else{
            //quorum tidak terpenuhi
            $status_transfer = -2;
        }

        return response()->json(array('status_transfer'=>$status_transfer));
    }

    public function getTotalSaldo(Request $request){
        $user_id = $request->user_id;
        //check if the client is from here

    }

    public function getSaldo(Request $request){
        $user_id = $request->user_id;

        //pemrosessan quorum
        $quorum = MyController::getQuorum();
        if($quorum >= 5){
            try{
                $client = Clients::where('id', $user_id)->first();
                if($client == null){
                    $nilai_saldo = -1;
                }else{
                    $nilai_saldo = $client->saldo;
                }
            }catch(\Illuminate\Database\QueryException $ex){
                $nilai_saldo = -4;
            }
        }else{
            //quorum tidak terpenuhi
            $nilai_saldo = -2;
        }

        return response()->json(array('nilai_saldo'=>$nilai_saldo));
        
    }

    public function register(Request $request){
        $user_id = $request->input('user_id');
        $nama = $request->input('nama');

        $quorum = MyController::getQuorum();
        if($quorum >= 5){
            try{
                $new = new Clients();
                $new->id = $user_id;
                $new->nama = $nama;
                $new->saldo = 0;
                $new->save();
                
                if(Clients::where('id', $user_id)->first() != null){
                    $status_register = 1;
                }
           }catch(\Illuminate\Database\QueryException $ex){
                
                $status_register = -4;
            }
        }else{
            //quorum tidak terpenuhi
            $status_register = -2;
        }

        return response()->json(array('status_register'=>$status_register));
    }

}
