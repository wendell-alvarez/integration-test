<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiKeys;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\MailerLiteController;

class ApiKeysController extends Controller
{
    private $url = "https://api.mailerlite.com/api/v2";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session()->has('apiKeySession')){
            return redirect()->action([MailerLiteController::class, 'index']);
        }
        return view('validate');
    }

    /**
     * Validate API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validateApiKey(Request $request)
    {
        
        $key = $request->api_key;

        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_URL,$this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            "X-MailerLite-ApiKey: $key",
            "Content-Type: application/json",
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        $jsonResponse = json_decode($resp);
        if(property_exists($jsonResponse, 'error')){
            return Redirect::to("/")->withFail($jsonResponse->error->message);
        }else{
            
            if($this->keyExistDb($key) > 0){
                session()->put('apiKeySession', $key);
                return Redirect::to("/list");
            }else{
                $this->store($key);
                session()->put('apiKeySession', $key);
                return Redirect::to("/list");
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $key
     */
    public function store($key)
    {
        $save = ApiKeys::create([
            'api_key' => $key,
        ]);
    }

    public function keyExistDb($key){
        $ifExist = ApiKeys::where('api_key', $key)->count();
        return $ifExist;
    }

    public function logout(Request $request){
        $request->session()->forget('apiKeySession');
        $request->session()->flush();
        return Redirect::to("/");
    }
}
