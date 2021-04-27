<?php    
    require_once 'connectDB.php';

    
    $today = date("Y-m-d H:i:s");
    echo "\n========== " . $today . "==========\n";

    $sql = "SELECT recordingId, videoURL FROM `Videos` WHERE `Baixado` = 0;";
    echo $sql;
    echo "\n";
    
    try
    {
        $resultado = $conecta->query($sql);
        
        if($resultado != null) 
        {
            foreach($resultado as $linha) 
            {
                // Get Session Id
                $id = $linha['recordingId'];
                
                // Initialize a file URL to the variable
                $url = rawurldecode($linha['videoURL']);
                
                echo "Id: " . $id . "\n";
                echo "URL: " . $url . "\n";
                
                // Marca a Gravação como baixada
                $sql2 = "UPDATE `Videos` SET `Baixado` = 1, `videoURL` = '" . $id . ".mp4' WHERE `recordingId` = '" . $id . "';";
                echo $sql2;
                echo "\n";
                /*
                try
                {
                    $resultado2 = $conecta->query($sql2);
                    echo "\nTabela Videos atualizada!\n";
                }
                catch(PDOException $e)
                {
                    echo 'ERRO!';
                    echo $e;
                }
                */
                $sql3 = "UPDATE `tutorias` SET `Gravacao` = 2 WHERE `IdInterno` = '" . $id . "';";
                echo $sql3;
                echo "\n";
                /*
                try
                {
                    $resultado3 = $conecta->query($sql3);
                    echo "\nTabela tutorias atualizada!\n";
                }
                catch(PDOException $e)
                {
                    echo 'ERRO!';
                    echo $e;
                }
                */
                file_put_contents('/home/scripts/videos/' . $id . ".mp4", fopen($url, 'r'));
            }
        }
    }
    catch(PDOException $e)
    {
        echo 'ERRO!';
        echo $e;
    }
    echo "\n";  
?>