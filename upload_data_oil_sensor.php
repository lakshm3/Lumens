<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $sensors = ['inlettemp-s', 'inlethum-s'];

    $nameFilter =  "";
    foreach($sensors as $s){
        $nameFilter.="value='".$s."' or ";
    }
    $nameFilter = substr($nameFilter,0,count($nameFilter)-4);
    
    $line = "SELECT * FROM ConfigurationData WHERE ".$nameFilter;
    try{
        $myPDO = new PDO('sqlite:'.$argv[1]) or die("cannot open the database");
        $result = $myPDO->query($line);

    }catch(Exception $e)
    {
        die($e->getMessage());
    }

    $instances = [];

    foreach($result as $row){
        $instances["k".$row["instance"]] = [
            "name"=>$row["value"],
            "instance"=>$row["instance"]

        ];
    }


    echo json_encode($instances,true);
    $final = [];

    $lastTs=0;
    if(file_exists(__DIR__."/lastDate")){
        $lastTs = file_get_contents(__DIR__."/lastDate");
    }

    $instanceFilter =  "";
    foreach($instances as $s){
        $instanceFilter.="instance='".$s["instance"]."' OR ";
    }
    $instanceFilter = substr($instanceFilter,0,count($instanceFilter)-4);
    $instanceFilter.= " AND 'timestamp'>=".$lastTs;

    try{
        $line = "SELECT * FROM MeasurementData WHERE ".$instanceFilter;
        echo $line;
    $result = $myPDO->query($line);
     }catch(Exception $e)
    {
        die($e->getMessage());
    }
    $final = [];
    foreach($result as $row){
        $name = $instances["k".$row["instance"]]["name"];
        $datetime = date("Y-m-d H:i:s",$row["timestamp"]/1000);
        $final[$name][$datetime] = [
            "Engine" =>[
                "Id" => $name,
                "Name" => $name
            ],
            "value" => $row["value"],
            "value_raw" => $row["value_raw"]
        ];
        $lastTs = max($lastTs, $row["timestamp"]);

    }
    
    $payload = json_encode($final);
    echo "\nfinal payload: ".$payload;
    $headers = ["Content-Type: application/json"];
    $endpoint = "http://10.104.2.130/api/storeData?type=sensor";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,$endpoint) ;
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, true);   
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $output = curl_exec($ch);
    curl_close($ch);

    $r = json_decode($output,true);

    if($r["success"]){
        echo "lastTs = ".$lastTs." \n";
        shell_exec("echo '".$lastTs."' >> lastDate");
    }
    die($output);

