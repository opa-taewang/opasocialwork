<?php
namespace App\Http\Controllers\Admin;

use App\User;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;





class CashController extends Controller
{





    public function index()
    {
     
     $results = array(); 
      
      
       
      $data = DB::table('apis')->join('api_request_params', 'apis.id', '=', 'api_request_params.api_id')->select('apis.name','apis.order_end_point','api_request_params.param_value','api_request_params.param_key')->where('api_request_params.param_key', '=','api_token')->orWhere('api_request_params.param_key', '=','key')->where('api_request_params.api_type', '=','order' )->groupBy('apis.name')->get();
      $datas = $data->toArray();
      
             \Log::error($datas);

     
      
     
        
       foreach ($datas as $sku){ 
           
        $API_URL = $sku->order_end_point;
        $API_TOKEN = $sku->param_value;
       if ($sku->param_key == 'key') {
			$apitk = 'key';
		}
		else $apitk = 'api_token';
       
$data1 = [
    
    $apitk => $API_TOKEN,
    'action' => 'balance',
];
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $API_URL,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_POSTFIELDS => http_build_query($data1),
    CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
   
));
$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);


$result = json_decode($response);
  array_push($results, $result);




      

}
       
       
 

$balance = $results; 

$c = count($results);


//var_dump($balance);die;
       
 
        
        
        return view('admin.cash', compact( 'balance','c','datas'));
       
        
      
        
        
        
        
        
        
    }




    
        

  

}
