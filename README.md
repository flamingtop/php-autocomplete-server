# PHP Auto-complete Server

        HTTP: http://domain/index.php?word=earth
        CLI : php index.php --word=earth
        
* Index a word (utf-8 encoded)
  
    English

        php index.php --word=understand
        php complete.php --key=und
        => ["understand"]

    Chinese
     
         php index.php --word=理解
         php complete.php --key=理
         => ["\u7406\u89e3"] (json)
         
    Japanese
     
         php index.php --word=りかい
         php complete.php --key=り
         => ["\u308a\u304b\u3044"]
         

* Index multiple words

         php index.php --words=understand,理解,りかい
         php complete.php --key=und
         => ["understand"]
         php complete.php --key=理
         => ["\u7406\u89e3"] (json)
         php complete.php --key=り
         => ["\u308a\u304b\u3044"]


* Index word(s) with explicit scores

         php index.php --word=hi:5
         php index.php --word=hello:10
         php complete.php --key=h
         => ["hello","hi"]
         
         php index.php --word=hi:10
         php index.php --word=hello:5
         php complete.php --key=h
         => ["hi","hello"]
         
         php index.php --words=hi:5,hello:10
         php complete.php --key=h
         => ["hello", "hi"]


* Index word(s) to another word

         php index.php --word=理解 --indexWord=understand
         php complete.php --key=und
         => ["\u7406\u89e3"]
         
         php index.php --words=理解,りかい --indexWord=understand
         php complete.php --key=und
         => ["\u7406\u89e3","\u308a\u304b\u3044"]


* Index word(s) into a specific context

         php index.php --words=bob,bill,eve,elvis --context=names:
         php complete.php --key=b --context=names:
         => ["bill","bob"]
         php complete.php --key=names:b
         => ["bill", "bob"]
         
