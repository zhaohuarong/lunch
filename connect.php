<?php
header("content-type:text/html;charset=utf-8");
   class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('lunch.db');
      }
   }
   $db = new MyDB();
   $db->busyTimeout(3000);
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
?>