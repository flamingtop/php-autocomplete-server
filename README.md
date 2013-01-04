# PHP Auto-complete Server

* Word

        php index.php --word=understand
        php complete.php --key=und
        => ["understand"]


* Multi-Byte(UTF-8) Word
 
     (Chinese)
     
         php index.php --word=理解
         php complete.php --key=理
         => ["理解"]
         
     (Japanese)
     
         php index.php --word=りかい
         php complete.php --key=り
         => ["りかい"]

        php index.php --words=hello,hi
        php complete.php --key=h
        => ["hello", "hi"]





    
    


