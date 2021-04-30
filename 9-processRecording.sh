# Reduz o tamanho do video
for video in $(ls /home/scripts/videos/*.mp4 | head -1); do
    echo "Video: $video";
    dir=${video:0:30}
    arquivo=${video:30:-4};
    echo "Pasta: $dir";
    echo "Arquivo: $arquivo";
    echo
    
    tmp=${dir}proc/${arquivo}_tmp.mp4
    novo=${dir}proc/${arquivo}.mp4

    echo "tmp: $tmp";
    echo "novo $novo";
    
    mv -v $video $tmp
    ffmpeg -i $tmp -vcodec libx265 -crf 24 $novo
done;