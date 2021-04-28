# Reduz o tamanho do video
#f4515f7a041dcb0f19b228601a8bb9ea75474af9-1619544281443
for video in $(ls /home/scripts/videos/*.mp4); do
    echo "Video: $video";
    dir=${video:0:30}
    arquivo=${video:30:-4};
    echo "Pasta: $dir";
    echo "Arquivo: $arquivo";
    echo
    echo
    arquivotmp=${dir}proc/${arquivo}_tmp.mp4
    arquivonovo=${dir}proc/${arquivo}.mp4

    mv -v $video $arquivotmp
    ffmpeg -i $arquivotmp -vcodec libx265 -crf 24 $arquivonovo
    
    # Envia para a nuvem
    google-drive-ocamlfuse /home/storage
    mv -v $arquivonovo /home/storage/dir/${arquivo}.mp4
    rm $arquivotmp

    # Gera Link de compartilhamento

    # Atualiza od bancos de dados
    user=USR
    password=PASSWD
    database=DBNAME
    host=IP
    
    query="UPDATE tutorias SET Gravacao = 3 WHERE IdInterno = '$arquivo';";

    mysql --user="$user" --password="$password" --host="$host" --database="$database" --execute="$query"
done;