<?php

    namespace Models\Workers;
        
    class CURL_WORKER{

        //This method supports only GET and POST requests
        public static function sendRequest(string $url, string $method = "GET", array $data = [null]){
            $result = null;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HEADER, false);              
            if(strtoupper($method) == 'POST'){                
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);         
                $result = curl_exec($curl);
                curl_close($curl);  
                return $result;                             
            }
            elseif(strtoupper($method) == 'GET'){                
                curl_setopt($curl, CURLOPT_URL, "$url?" . http_build_query($data));              
                $result = curl_exec($curl);
                curl_close($curl);   
                return $result;                                           
            }
            else {  
                curl_close($curl);              
                return $result;
            }
        }
    }