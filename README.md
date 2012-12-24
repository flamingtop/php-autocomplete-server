
rindex.php --data {"word":"WORD","region":"REGION","score":1} "http://localhost/ac-php/rindex.php"
=> region['REGION']['words']['WORD']

complete.php wa
=> ["war", "washington", "watson"]

select.php washington
=> words['washington']['score']++
