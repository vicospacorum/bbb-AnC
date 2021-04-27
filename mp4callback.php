<?php
    require_once 'connectDB.php';

    $sql = "INSERT INTO Videos (`recordingId`, `videoURL`) VALUES ('" . $_POST['recordId']  . "', '" . $_POST['mp4Link']  . "');";
    
    try
    {
        $resultado = $conecta->query($sql);
    }
    catch(PDOException $e)
    {
        echo 'ERRO!';
        echo $e;
    }
?>