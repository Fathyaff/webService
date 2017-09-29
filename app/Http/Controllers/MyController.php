<?php

namespace App\Http\Controllers;
use File;
use Carbon;
use App\Requests;
use App\PlusOne;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class MyController extends Controller
{
    public function plusOne(){
        $lastCount = PlusOne::latest()->first();
        if($lastCount != null){
            $count = $lastCount->plusoneret;
        }else{
            $count = 1;
        }

        $path = public_path()."/apiversion.txt";
        $version = File::get($path);
        MyController::incrementPO($count);
        return response()->json(array('apiversion'=>$version,'count'=>$count));

    }

    public static function greeting(){
        $client = new Client();
        $res = $client->request('GET', 'http://localhost:17088', [
            'form_params' => [
                
            ]
        ]);
        echo $res->getStatusCode();
        // "200"
        echo $res->getHeader('content-type');
        // 'application/json; charset=utf8'
        echo $res->getBody();
        // {"type":"User"...'
        return $res->getBody();
    }

    public function hello(Request $request){
        $name = $request->request;
        $greeting = MyController::greeting()->state;
        
        $lastRequest = Requests::where('name', $name)->first();
        if($lastRequest != null){
            $count = 1;
            $message = $greeting.", ".$name;
            MyController::updateVisit($name);
        }else{
            $count = $lastRequest->count;
            $message = $greeting.', '.$name;
            MyController::incrementVisit($name);
        }
        $currentVisit = Carbon\Carbon::now()->toDateTimeString();
        $version = File::get($path);
        
        return response()
            ->json(array('apiversion'=>$version,'count'=>$count, 
                'currentVisit'=>$currentVisit, 'response'=>$message));

    }

    public static function incrementVisit($name){
        $requests = Requests::where('name', $name)->first();
        $incCount = $requests->count;
        $requests->count = $incCount;
        $requests->update();
        return;
    }

    public static function updateVisit($name){
        $requests = new Requests();
        $requests->name = $name;
        $requests->count = 1;
        $requests->save();
        return;
    }

    public static function incrementPO($count){
        $newCount = new PlusOne();
        $newCount->plusoneret = $count + 1;
        $newCount->save();
        return;
    }
   
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
