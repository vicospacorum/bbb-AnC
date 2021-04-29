<?php
    require_once 'connectDB.php';

    $today = date("Y-m-d H:i:s");
    echo "\n========== " . $today . "Z==========\n";

    $url="https://api.mynaparrot.com/bigbluebutton/hellatech/api/getMeetings?checksum=916fdcc09f4bf874086516b49e3a67ccf88731bc";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents

    $data = curl_exec($ch); // execute curl request
    curl_close($ch);
    
    $xml = simplexml_load_string($data);
    //print_r($data);
    //echo "\n\n";
    
    $message = $xml->messageKey;
    //echo $message;
    //echo "\n";

    if ($message == "noMeetings")
    {
        echo "Nenhuma Sessão em Andamento\n";
    }
    else
    {
        foreach($xml->meetings->meeting as $sala)
        {
            //$xml->meetings->meeting->meetingID;
            $Id = (string) $sala->meetingID;
            $IdInterno = (string) $sala->internalMeetingID;
            $DataHora = (int) $sala->startTime;

            //echo $DataHora;
            //echo "\n";
            $DataHora /= 1000;
            $DataHora = (int) $DataHora;
            //echo $DataHora;
            //echo "\n";

            
            if (!empty($IdInterno))
            {
                $sql = "INSERT IGNORE INTO novas (IdInterno, Id, Inicio) VALUES ('" . $IdInterno . "', '" . $Id . "', " . $DataHora . ");";
                echo "Query:\n" . $sql;
                echo "\n\n";
                
                try
                {
                    $resultado = $conecta->query($sql);
                    
                    echo "Dados de sessão em andamento adicionados com sucesso!\n";
                }
                catch(PDOException $e)
                {
                    echo 'ERRO!';
                    echo $e;
                }
            }
            else
            {
                echo "Erro Desconhecido\n";
            }
        }
    }
?>