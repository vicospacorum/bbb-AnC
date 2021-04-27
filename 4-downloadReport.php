<?php    
    require_once 'connectDB.php';

    $today = date("Y-m-d H:i:sZ");
    echo "\n========== " . $today . "==========\n";


    $sql = "SELECT Id, URL FROM `Relatorios` WHERE `Baixado` = 0;";
    //echo $sql;
    //echo "\n";

    try
    {
        $resultado = $conecta->query($sql);
        
        if($resultado != null) 
        {
            foreach($resultado as $linha) 
            {
                // Get Session Id
                $id = $linha['Id'];
                echo $id;
                echo "\n";

                // Initialize a file URL to the variable
                $url = rawurldecode($linha['URL']);
                echo $url;
                echo "\n";
                
                // Initialize the cURL session
                $ch = curl_init($url);
                
                // Inintialize directory name where
                // file will be save
                $dir = '/home/scripts/reports/';
                
                // Use basename() function to return
                // the base name of file 
                $file_name = basename($url, ".json");
                $tamanho = strpos($file_name, "?");
                $file_name = substr($file_name, 0, $tamanho);

                // Save file into file location
                $save_file_loc = $dir . $file_name;
                
                // Open file 
                $fp = fopen($save_file_loc, 'wb');
                
                // It set an option for a cURL transfer
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                
                // Perform a cURL session
                curl_exec($ch);
                
                // Closes a cURL session and frees all resources
                curl_close($ch);

                // Close file
                fclose($fp);
                
                // Marca o Relatório como baixado
                $sql2 = "UPDATE `Relatorios` SET `Baixado` = 1 WHERE `Id` = '" . $id . "';";
                try
                {
                    $resultado2 = $conecta->query($sql2);

                     // aaaa-mm-dd hh:mm:ss (the MySQL DATETIME format)
                    $today = date("Y-m-d H:i:s");
                    echo $today . ": Operação realizada com sucesso!\n";
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
?>