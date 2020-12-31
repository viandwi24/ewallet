<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * Make api response
     *
     * @param [type] $data
     * @param string $message
     * @param boolean $status
     * @param string $error_code
     * @param array $errors
     * @param integer $responseCode
     * @param array $customMeta
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function response($data, string $message, bool $status, string $error_code = null, $errors = null, int $responseCode = null, array $customMeta = [], $headers = [])
    {
        // a data must extends JsonResource
        if (($data instanceof JsonResource)) $httpresource = $data;
        else $httpresource = new JsonResource($data);
    
        // clean data
        if ($data instanceof JsonResource) $data = $data->toArray(app('request'));
    
        // build meta
        $meta = [ 'status' => $status, 'message' => $message ];
        if ($error_code != null) { $meta['error_code'] = $error_code; $meta['errors'] = ($errors == null ? [] : $errors); }
        if (count($customMeta) > 0) $meta = array_merge($meta, $customMeta);
        
        $response = response()->json(array_merge(['data' => $data], $meta), ($responseCode == null ? 200 : $responseCode) )
                ->header('Content-Type', 'application/json')
                ->header('Accept', 'application/json')
                ->header('Access-Control-Expose-Headers', 'Authorization');
        foreach($headers as $key => $value) {
            $response->header($key, $value);
        }
        // return
        // if($error_code == null) {
        //     return ($responseCode == null)
        //         ? $httpresource->additional($meta)->response()
        //         : $httpresource->additional($meta)->response()->setStatusCode($responseCode)   
        // }else{
            return $response;
        // }
    }
}
