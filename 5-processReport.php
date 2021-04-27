<?php
    require_once 'connectDB.php';

    $today = date("Y-m-d H:i:s");
    echo "\n========== " . $today . "==========\n";

    // Seleciona os relatorios a serem processados
    $sql1 = "SELECT `Id` FROM `Relatorios` WHERE `Baixado` = 1 AND `Processado` = 0";
    //echo $sql1;
    //echo "\n";
        
    try
    {
        $resultado1 = $conecta->query($sql1);
            
        if($resultado1 != null) 
        {
            foreach($resultado1 as $linha) 
            {
                $meetingId = $linha['Id'];
                    
                $sql2 = "SELECT `Id`, `Inicio` FROM `tutorias` WHERE `IdInterno` = '" . $meetingId . "' AND `Atualizado` = 0";
                echo "\n" . $sql2;
                echo "\n";
                
                try
                {
                    $resultado2 = $conecta->query($sql2);
                        
                    if($resultado2 != null) 
                    {
                        foreach($resultado2 as $linha2) 
                        {
                            $Id = $linha2['Id'];
                            $Inicio = $linha2['Inicio'];
                        }
                    }
                }
                catch(PDOException $e)
                {
                    echo 'ERRO!';
                    echo $e;
                }

                $relatorio = $linha['Id'] . ".json";

                $string = file_get_contents('/home/scripts/reports/' . $relatorio);
                $dados = json_decode($string, true);
                    
                $duracao = $dados['duration'];
                $categoria = $dados['metadata']['bbb_context_name'];
                $sala = $dados['metadata']['bbb_context_label'];
                $participantes = $dados['attendees'];

                /* DEBUG */
                echo "\nId Interna: " . $meetingId;
                echo "\nId: " . $Id;
                echo "\nInicio: " . $Inicio;
                echo "\nDuração: " . $duracao;
                echo "\nCategoria: " . $categoria;
                echo "\nSala: " . $sala;
                echo "\nRelatório: " . $relatorio;
                //echo "\nString:\n";
                //print_r($string);
                echo "\n\n------------- PARTICIPANTES -------------\n";
                /* DEBUG END */

                foreach ($participantes as $participante)
                {
                    $participanteNomeTamanho = strlen($participante['name'])-13;
                    $participanteNome = substr($participante['name'], 0, $participanteNomeTamanho);
                    $participanteID = substr($participante['name'], -10);
                    echo "Nome: " . $participanteNome . "\n";
                    echo "ID: " . $participanteID . "\n\n";

                    $sql3 = "INSERT IGNORE INTO tutorias (`Categoria`, `Sala`, `IdInterno`, `Id`, `Participante`, `Inicio`, `Duracao`, `Atualizado`) VALUES ('" . $categoria . "', '" . $sala . "', '" . $meetingId . "', '" . $Id . "', '" . $participanteID . "', " . $Inicio . ", " . $duracao . ", 1);";
                    echo $sql3;
                    echo "\n";

                    try
                    {
                        $resultado3 = $conecta->query($sql3);
                        
                        echo "Inserção em Tutorias realizada com sucesso!\n";
                    }
                    catch(PDOException $e)
                    {
                        echo 'ERRO!';
                        echo $e;
                    }
                    
                    $sql4 = "INSERT IGNORE INTO Pessoas (`Id`, `Nome`) VALUES ('" . $participanteID . "', '" . $participanteNome . "');";
                    echo $sql4;
                    
                    try
                    {
                        $resultado4 = $conecta->query($sql4);
                        
                        echo "Processamento de Participante realizado com sucesso!\n";
                    }
                    catch(PDOException $e)
                    {
                        echo 'ERRO!';
                        echo $e;
                    }
                    echo "\n-----\n";
                }

                $sql5 = "DELETE FROM tutorias WHERE `IdInterno` = '" . $meetingId . "' AND `Id` = '" . $Id . "' AND `Atualizado` = 0;";
                echo $sql5;
                echo "\n";
                
                try
                {
                    $resultado5 = $conecta->query($sql5);
                    
                    echo "Remoção de linha não atualizada realizada com sucesso!\n";
                }
                catch(PDOException $e)
                {
                    echo 'ERRO!';
                    echo $e;
                }
                
                $sql6 = "UPDATE `Relatorios` SET `Processado` = 1, `URL` = '" . $relatorio . "' WHERE `Id` = '" . $meetingId . "';";
                echo $sql6;
                echo "\n";

                try
                {
                    $resultado6 = $conecta->query($sql6);
                    
                    echo "Tabela Relatórios atualizada com sucesso!\n";
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
    echo "\n";
?>