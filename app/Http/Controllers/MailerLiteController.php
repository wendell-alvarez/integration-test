<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\DataTables;

class MailerLiteController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Session::has('apiKeySession')){ 
            return Redirect::to("/logout");
        }
        return view('home');
    }

    /**
     * Display a listing of subscribers.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSubscribers(Request $request)
    {
        $url = "https://api.mailerlite.com/api/v2/subscribers/";

        $columns = [ 
            0   => 'email', 
            1   => 'name',
            2   => 'country',
            3   => 'datetime',
            4   => 'datetime'
        ];

        $params = [
            'offset'  => $request->input('start'),
            // 'limit'   => $request->input('length')
        ];

        $url = $url . '?' . http_build_query($params);

        $key = Session::get('apiKeySession');
        $search = $request->input('search.value'); 
        
        if(!empty($search)){
            $url = "https://api.mailerlite.com/api/v2/subscribers/search?query=$search";
        }

        

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            "X-MailerLite-ApiKey: $key",
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $resp = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($resp);

        $totalData = count((array) $result);
        $totalFiltered = $totalData; 


        $data = [];

        if(!empty($result))
        {
            if(gettype($result) == "array"){

                foreach ($result as $res)
                {
                    $fields = $res->fields;
                    $country = "";
                    $datetime =  empty($res->date_subscribe) ? $res->date_created : $res->date_subscribe; //check if date_subscribe has value 

                    for ($i=0; $i < count($fields); $i++) { 
                        if($fields[$i]->key == "country"){
                            $country = $fields[$i]->value;
                        }
                    }
                    
                    $nestedData= [
                        'email'    => "<a href='/subscriber/$res->id'>$res->email</a>",
                        'name'     => $res->name,
                        'country'  => $country,
                        'date'     => date('d/M/Y',strtotime($datetime)),
                        'time'     => date(' H:i:s',strtotime($datetime)),
                        'delete'   => "<button class='btn btn-danger delete' data-id='$res->id'>Delete</button>",
                        'datetime' => strtotime($datetime)
                    ];
    
                    $data[] = $nestedData;

                    $order = $columns[$request->input('order.0.column')];
                    $dir   = $request->input('order.0.dir');

                    usort($data, function ($item1, $item2) use ($order,$dir) {
                        if($dir == "asc"){
                            return $item1[$order] <=> $item2[$order];
                        }else{
                            return $item2[$order] <=> $item1[$order];
                        }
                    });
                }
            }else{
                
                $fields = $result->fields;
                $country = "";
                for ($i=0; $i < count($fields); $i++) { 
                    if($fields[$i]->key == "country"){
                        $country = $fields[$i]->value;
                    }
                }
                
                //check if date_subscribe has value 
                $datetime =  empty($result->date_subscribe) ? $result->date_created : $result->date_subscribe;

                $nestedData= [
                    'email'    => "<a href='/subscriber/$result->id'>$result->email</a>",
                    'name'     => $result->name,
                    'country'  => $country,
                    'date'     => date('d/M/Y',strtotime($datetime)),
                    'time'     => date(' H:i:s',strtotime($datetime)),
                    'delete'   => "<button class='btn btn-danger delete' data-id='$result->id'>Delete</button>"
                ];

                $data[] = $nestedData;

            }
          
        }
        
        return Datatables::of($data)
        ->addIndexColumn()
        ->rawColumns(['delete','email'])
        ->make(true);
    }

    public function subscribe()
    {
        if(!Session::has('apiKeySession')){ 
            return Redirect::to("/logout");
        }
        return view('subscribe');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $exist = $this->checkIfExist($request->email);
        
        if(property_exists($exist, 'id')){
            return Redirect::to("/subscribe")->withFail("Subscriber Already exist.");
        }

        $key = Session::get('apiKeySession');
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.mailerlite.com/api/v2/subscribers",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"email\":\"$request->email\", \"name\": \"$request->name\", \"fields\": {\"country\": \"$request->country\"}}",
            CURLOPT_HTTPHEADER => [
                "content-type: application/json",
                "x-mailerlite-apikey: $key"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $jsonResponse = json_decode($response);

        if(property_exists($jsonResponse, 'error')){
            return Redirect::to("/subscribe")->withFail($jsonResponse->error->message);
        }
        return Redirect::to("/subscribe")->withSuccess("New Subscriber Saved.");
    }

    public function checkIfExist($val){
        
        $key = Session::get('apiKeySession');

        $url = "https://api.mailerlite.com/api/v2/subscribers/$val";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            "X-MailerLite-ApiKey: $key",
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $resp = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($resp);

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $exist = $this->checkIfExist($id);

        if(!property_exists($exist, 'id')){
            abort(404);
        }

        $fields = $exist->fields;
        $country = "";
        for ($i=0; $i < count($fields); $i++) { 
            if($fields[$i]->key == "country"){
                $country = $fields[$i]->value;
            }
        }

        $data = [
            'id'=> $exist->id,
            'name' => $exist->name,
            'email' => $exist->email,
            'country' =>  $country
        ];


        return view("update", compact("data"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $key = Session::get('apiKeySession');
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.mailerlite.com/api/v2/subscribers/'.$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"fields\": {\"name\": \"$request->name\", \"country\": \"$request->country\"}}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        
        $headers = [
            'Content-Type: application/json',
            'X-Mailerlite-Apikey: '.$key
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close ($ch);
        return redirect('/list');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $key = Session::get('apiKeySession');
        $url = "https://api.mailerlite.com/api/v2/subscribers/$id";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_DELETE, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            "X-MailerLite-ApiKey: $key",
        ];

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $resp = curl_exec($curl);
        curl_close($curl);
    }
}
