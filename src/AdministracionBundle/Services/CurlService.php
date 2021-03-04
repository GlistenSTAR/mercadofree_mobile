<?php

namespace AdministracionBundle\Services;

/**
 * Description of CurlService
 *
 * @author Vadino
 */
class CurlService {
    
    /**
     * @param string $url
     * @param string $method ("GET", "POST", "PUT", "DELETE")
     * @param array $headers
     * @param array $data
     * @param array $curl_options
     * @return type
     */
    public function curlRequest($url, $method, $headers = [], $data = [], $curl_options = []){
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        //--- If any headers set add them to curl request
        if(!empty($headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        //--- If any data is supposed to be send along with request add it to curl request
        if($data){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        //--- Any extra curl options to add in curl object
        if($curl_options){
            foreach($curl_options as $option_key => $option_value){
                curl_setopt($curl, $option_key, $option_value);
            }
        }

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        //--- If curl request returned any error return the error
        if ($error) {
            return "CURL Error: $error";
        }
        //--- Return response received from call
        return $response;
    }
}
