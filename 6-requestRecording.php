<?php
    require_once 'connectDB.php';

    $today = date("Y-m-d H:i:s");
    echo "\n========== " . $today . "Z==========\n";

    $nodes = array();
    $meetingID = array();

    $metodo = 'processMP4';
    $query1 = 'recordID=';
    $query2 = '&mp4ReadyUrl=https%3A%2F%2Fhellatech.com.br%2Flisting%2Ffm5rMjyV4Aff%2Fmp4callback.php';
    $salt = 'FpuoiloGsMtpeqDiGrOw';
    
    $sql = "SELECT IdInterno FROM tutorias WHERE Atualizado = 1 AND Gravacao = 0 GROUP BY `IdInterno` LIMIT 2;";
    echo $sql;
    echo "\n";
    
    try
    {
        $resultado = $conecta->query($sql);
        
        if($resultado != null) 
        {
            $i = 0;
            foreach($resultado as $linha) 
            {
                $recordID = $linha['IdInterno'];

                $checksum= sha1($metodo . $query1 . $recordID . $query2 . $salt);

                $url = "https://api.mynaparrot.com/bigbluebutton/hellatech/api/";
                $url .= $metodo . "?"; 
                $url .= $query1 . $recordID . $query2 . "&checksum=" . $checksum;
                $nodes[$i] = $url;
                $meetingID[$i] = $recordID;
                $i++;

                echo "\nID: " . $recordID;
                //echo "\nChecksum: " . $checksum;
                echo "\nURL: " . $url;
                echo "\n-----\n";
            }
        }
    }
    catch(PDOException $e)
    {
        echo 'ERRO!';
        echo $e;
    }

    /*
    echo "Nodes: \n";
    print_r($nodes);
    echo "\n";
    */

    $curl_arr = array();
    $master = curl_multi_init();
    
    for($i = 0; $i < 2; $i++)
    {
        $callUrl = $nodes[$i];
        $curl_arr[$i] = curl_init($callUrl);
        curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
        curl_multi_add_handle($master, $curl_arr[$i]);
    }
    
    do 
    {
        curl_multi_exec($master,$running);
    } while($running > 0);

    echo "\n---------\nResultados:\n";
    for($i = 0; $i < 2; $i++)
    {
        $results = curl_multi_getcontent  ( $curl_arr[$i]  );
        $xml = simplexml_load_string($results);
        echo $i . "\n"; 
        echo $nodes[$i] . "\n";
        echo $results . "\n";
        print_r($xml) . "\n";

        // Atualizar Planilha Tutorias mudando Gravação de 0 (Não Solicitado) para 1 (Processando)
        $returncode = $xml->returncode;
        echo "Saída: " . $returncode . "\n";
        
        if ($returncode == "SUCCESS")
        {
            $sql2 = "UPDATE  tutorias SET `Gravacao` = 1 WHERE `IdInterno` = '" . $recordID . "';";
            echo $sql2;
            echo "\n";

            try
            {
                $resultado2 = $conecta->query($sql2);
                
                echo "Operação realizada com sucesso!\n";
            }
            catch(PDOException $e)
            {
                echo 'ERRO!';
                echo $e;
            }
        }
    }
    echo "\n";
?>