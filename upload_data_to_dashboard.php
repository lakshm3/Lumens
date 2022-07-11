<?php

    if(count($argv) <=4) die("This script need 2 args: 1st -> path to directory to scan, 2nd -> repeat time scanning, 3rd -> url of the dashboard, 4rd");

    $dir = $argv[1];
    $dtime = $argv[2];
    $url = $argv[3];

    if($dtime<2) $dtime=2;

    if(count($argv)==5) $lastDateDone = $argv[4];
    else $lastDateDone = "2020-01-23 07h40m43";
    
    echo "Scanning json files<br/>";

    while(true)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        $localMaxDate = $lastDateDone;
        foreach($files as $f)
        {
            $ext = substr($f,strlen($f)-5);
            $date = substr($f,strlen($f)-24,19);

            echo "EXT=".$ext.", date=".$date,"<br/>";
            if($date>$lastDateDone && $ext ==".json")
            {
                if($localMaxDate>$date) $localMaxDate = $date;

                echo $date;
                $data = file_get_contents($dir."/".$f);
                /*$result = postData($data);
                $response = json_decode($result["response"],true);
                if($result["http_code"]<400 && isset($response["success"]) && $response["success"]) 
                {
                    echo "Data sent <br/>";
                }else{

                    $m = null;
                    if(isset($response["reason"])) $m = $response["reason"];
                    if(isset($response["message"])) $m = $response["message"];

                    if($m!=null) echo "Error while sending: ".$m." httpCode=".$response["http_code"]."<br/>";
                    else echo "Unknow error while sending: ".$response["http_code"]."<br/>";
                };*/
            }
        }

        sleep($dtime);

    }

    function postData($postContent){
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $postContent);

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $r = ["http_code"=>  curl_getinfo($ch, CURLINFO_HTTP_CODE), "response" => $server_output];
        curl_close ($ch);
        return $r;
    }