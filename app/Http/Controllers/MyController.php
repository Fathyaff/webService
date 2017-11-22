<?php

namespace App\Http\Controllers;
use App\Clients;
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
        // $res = $client->request('GET', 'http://172.17.0.38/list.php', [
        //     'headers' => [
        //         'Accept' => 'application/json',
        //         'Content-type' => 'application/json'
        // ]]);
        
        // $bodyResp = $res->getBody();
        //$array = json_decode($bodyResp, true);
        
        $array = array(
            array(
                "ip" => "172.17.0.18",
                "npm" => "1406578278"
            ),
            array(
                "ip" => "172.17.0.51",
                "npm" => "1406543694"
            ),
            array(
                "ip" => "172.17.0.50",
                "npm" => "1406543712"
            ),
            array(
                "ip" => "172.17.0.38",
                "npm" => "1406559036"
            ),
            array(
                "ip" => "172.17.0.19",
                "npm" => "1406577386"
            ),
            array(
                "ip"=> "172.17.0.42",
                "npm"=> "1406543832"
            ),
        );

        for($i = 0; $i < 5 ; $i++){
            $activeIP = $array[$i]['ip'];
            $client2 = new Client();
            $resp = $client2->request('POST', $activeIP."/ewallet/ping", [
                'headers' => [
                    'Content-Type' => 'application/json'
            ]]);
            
            $quorumResponse = json_decode($resp->getBody(), true);
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
                    
                if(Clients::where('id', $user_id)->first() != null){
                    $status_transfer = 1;
                }                       
            }catch(\Illuminate\Database\QueryException $ex){
                $status_transfer = -4;
            }
        }else{
            //quorum tidak terpenuhi
            $status_transfer = -2;
        }
        return response()->json(array('status_transfer'=>$status_transfer));
    }

    public function getTotalSaldo(Request $request){
        $user_id = $request->user_id;
        $nilai_saldo = 0;
        //check if the client is from here
        //pemrosessan quorum
        $quorum = MyController::getQuorum();
        if($quorum >= 5){
            try{
                $client = Clients::where('id', $user_id)->first();
                $nilai_saldo = $nilai_saldo + $client->saldo;
                if($user_id == "1406543832"){
                    $array = array(
                        array(
                            "ip" => "172.17.0.51",
                            "npm" => "1406543694"
                        ),
                        array(
                            "ip" => "172.17.0.38",
                            "npm" => "1406559036"
                        ),
                        
                        array(
                            "ip" => "172.17.0.18",
                            "npm" => "1406578278"
                        ),
                        array(
                            "ip" => "172.17.0.19",
                            "npm" => "1406577386"
                        ),
                        array(
                            "ip" => "172.17.0.50",
                            "npm" => "1406543712"
                        ),
                    );

                    for($i = 0; $i < count($array) ; $i++){
                        $activeIP = $array[$i]['ip'];
                        $client2 = new Client();
                        $resp = $client2->request('POST', $activeIP."/ewallet/getSaldo", [
                            'headers' => [
                                'Content-Type' => 'application/json',
                            ],'form_params' => [
                                'user_id' => $user_id,
                            ],
                        ]);
                        
                        $saldoResponse = json_decode($resp->getBody(), true);
                        $nilaiSaldo = $saldoResponse['nilai_saldo'];
                        if($nilaiSaldo >= 0){
                            $nilai_saldo = $nilai_saldo + $nilaiSaldo;
                        }
                    }

                }else{
                    $ipHomebased = MyController::findDomisili($user_id);
                    $client3 = new Client();
                    $resp = $client3->request('POST', $ipHomebased."/ewallet/getTotalSaldo", [
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],'form_params' => [
                            'user_id' => $user_id,
                        ],
                    ]);
                    $totalSaldoResponse = json_decode($resp->getBody(), true);
                    $nilai_saldo = $totalSaldoResponse['nilai_saldo'];
                }
                
                
            }catch(\Illuminate\Database\QueryException $ex){
                $nilai_saldo = -4;
            }
        }else{
            //quorum tidak terpenuhi
            $nilai_saldo = -2;
        }

        return response()->json(array('nilai_saldo'=>$nilai_saldo, 'saldo_response'=>$nilaiSaldo, 'ip_response'=>$activeIP));

    }

    private static function findDomisili($user_id){
        $client = new Client();
        $res = $client->request('GET', 'http://152.118.31.2/list.php', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json'
        ]]);
        
        $bodyResp = $res->getBody();
        $array = json_decode($bodyResp, true);

        for($i = 0; $i < 8 ; $i++){
            $ip = $array[$i]['ip'];
            $npm = $array[$i]['npm'];
            if($npm == $user_id){
                return $ip;
            }else{
                //if no ip correspondent with user_id
                return 0;
            }
        }
    }

    public function getSaldo(Request $request){
        $user_id = $request->user_id;
        $nilai_saldo = -99;
        $quorum = 0;
        
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
        $user_id = $request->user_id;
        $nama = $request->nama;
        $status_register = -99;

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
