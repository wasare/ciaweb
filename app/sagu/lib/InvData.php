<?php
//----------------------------------------------------------------------------
// Inverte a data
//----------------------------------------------------------------------------
// PostgreSQL-7.x.x

    Function InvData($dt)
    {

      $dt = str_replace('/', '-', "$dt"); 
      $dt = str_replace('.', '-', "$dt"); 

      list($obj1, $obj2, $obj3) = split("-", $dt, 3);
   
      $obj = $obj3 . "-" . $obj2 . "-" . $obj1;
     
      if ($obj == '--') {
      	$obj = '';
      }
	
      return $obj;
    }


// PostgreSQL-6.5.x
/*   Function InvData($dt)
    {

      $dt = str_replace('/', '-', "$dt"); 
      $dt = str_replace('.', '-', "$dt"); 

      list($obj1, $obj2, $obj3) = split("-", $dt, 3);
   
      $obj = $obj2 . "-" . $obj1 . "-" . $obj3;
     
      if ($obj == '--') {
      	$obj = '';
      }
	
      return $obj;
    } 
*/
?>
