<?php
  $username= "pi";             // mysql username
  $password= "my5ql";// mysql password
  $database= "testDB";           // database name
  $server  = "localhost";      // IP

  // Connect to server
  $connection = mysql_connect("$server","$username","$password")
  or die ("Unable to connect to server").mysql_error();

  // Connect to database
  mysql_select_db("$database",$connection)
  or die ("Unable to connect to database $database").mysql_error();
?>
