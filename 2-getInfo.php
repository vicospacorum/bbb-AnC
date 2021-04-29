<?php
    require_once 'connectDB.php';

    $today = date("Y-m-d H:i:s");
    echo "\n========== " . $today . "Z==========\n";

    $sessoes = array();
    $metodo = 'processMynaReport';
    $query1 = 'internalMeetingID=';
    $query2 = '&reportCallbackUrl=https%3A%2F%2Fhellatech.com.br%2Flisting%2Ffm5rMjyV4Aff%2Fcallback.php';
    $salt = 'FpuoiloGsMtpeqDiGrOw';

    // Cria um Array com as IDs das novas Sessões
    $sql = "SELECT IdInterno FROM novas WHERE 1";

    try
    {
        $resultado = $conecta->query($sql);
        
        if($resultado != null) 
        {
            $i = 0;
            foreach($resultado as $linha) 
            {
                $meetingID = $linha['IdInterno'];
                echo "ID: " . $meetingID;
                echo "\n";
                $checksum= sha1($metodo . $query1 . $meetingID . $query2 . $salt);
                //echo "Checksum: " . $checksum;
                //echo "\n";
                $url = "https://api.mynaparrot.com/bigbluebutton/hellatech/api/";
                $url .= $metodo . "?"; 
                $url .= $query1 . $meetingID . $query2 . "&checksum=" . $checksum;
                $sessoes[$i] = $url;
                $i++;
            }
        }
    }
    catch(PDOException $e)
    {
        echo 'ERRO!';
        echo $e;
    }
    //print_r($sessoes);
    $numSessoes = count($sessoes);
    
    // Faz as Requisições de Relatório à API do BBB
    $curl_arr = array();
    $master = curl_multi_init();
    
    for($i = 0; $i < $numSessoes; $i++)
    {
        $url = $sessoes[$i];
        $curl_arr[$i] = curl_init($url);
        curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
        curl_multi_add_handle($master, $curl_arr[$i]);
    }
    do 
    {
        curl_multi_exec($master,$running);
    } while($running > 0);
    
    echo "Results: ";
    for($i = 0; $i < $numSessoes; $i++)
    {
        $results = curl_multi_getcontent  ( $curl_arr[$i]  );
        $xml = simplexml_load_string($results);
        echo "\n";
        print_r($xml);
        //echo( $i . "\n" . $sessoes[$i] . $results . "\n");
        $returncode = $xml->returncode;
        echo "Saída: " . $returncode . "\n";
        
        if ($returncode == "SUCCESS")
        {
            //echo $sessoes[$i];
            //echo "\n";
            $startPos = strpos($sessoes[$i], "MeetingID=") + 10;
            $endPos = strpos($sessoes[$i], "&reportCallbackUrl=");
            $tamanho = $endPos - $startPos;
            $Id = substr($sessoes[$i], $startPos, $tamanho);
            echo "\nID: " . $Id;
            echo "\n";

            // Insere os dados na tabela "Tutorias"
            $sql2 = "SELECT * FROM novas WHERE `IdInterno` = '" . $Id . "';";
            echo $sql2;
            echo "\n";
            
            try
            {
                $resultado2 = $conecta->query($sql2);
                
                if($resultado2 != null) 
                {
                    foreach($resultado2 as $linha) 
                    {
                        $nova_meetingID = $linha['IdInterno'];
                        $nova_Id = $linha['Id'];
                        $nova_Inicio = $linha['Inicio'];
                        echo "Id Interno: " . $nova_meetingID;
                        echo "\n";
                        echo "Id: " . $nova_Id;
                        echo "\n";
                        echo "Inicio: " . $nova_Inicio;
                        echo "\n";

                        $sql3 = "INSERT IGNORE INTO tutorias (IdInterno, Id, Inicio) VALUES ('" . $nova_meetingID . "', '" . $nova_Id . "', " . $nova_Inicio . ");";
                        try
                        {
                            $resultado3 = $conecta->query($sql3);

                            echo "Dados Inseridos em TUTORIAS com sucesso!";
                            echo "\n";
                        }
                        catch(PDOException $e)
                        {
                            echo 'ERRO!';
                            echo $e;
                        }

                        // Exclui a entrada da tabela NOVAS
                        $sql4 = "DELETE FROM novas WHERE `IdInterno` = '" . $nova_meetingID . "';";
                        echo "\n" . $sql4;
                        echo "\n";
                        
                        try
                        {
                            $resultado4 = $conecta->query($sql4);

                            echo "Dados removidos de NOVAS com sucesso!";
                            echo "\n";
                        }
                        catch(PDOException $e)
                        {
                            echo 'ERRO!';
                            echo $e;
                        }
                    }
                }
            }
            catch(PDOException $e)
            {
                echo 'ERRO!';
                echo $e;
            }
        }
    }
?>