<?php
    require_once 'connectDB.php';

    $sql = "INSERT INTO Relatorios (Id, Status, URL) VALUES ('" . $_POST['internalMeetingID']  . "', '" . $_POST['status']  . "', '" . $_POST['reportUrl']  . "');";
    
    try
    {
        $resultado = $conecta->query($sql);
        
        //echo 'Relatório inserido com sucesso!';
    }
    catch(PDOException $e)
    {
        //echo 'ERRO!';
        //echo $e;
    }
?>