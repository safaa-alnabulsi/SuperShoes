# SuperShoes

The German online shop "supershoes.de" sells successfully shoes since years.
Their shoes are all stored in a MySQL database. As a new season starts, the shop wants to put a new collection online. 
Due to that following csv file has to be put into the MySQL database.

Please write a php script:

   1.  generates two new csv files one should only contains "Herren" related data, the other only  "Damen" related data.
     sorting: (Produktname | Anzahl im Lager | Material | Schuhgröße)
   2. Remove all shoes from the final csv files, which aren't in store (Anzahl im Lager = 0),
     due to they weren't needed to show on the website.
     
 
you can find how to use the classes in index.php

