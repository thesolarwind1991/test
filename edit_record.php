<? require_once('Class_BD.php'); 
   
   $BD = new Class_BD(); 

   foreach (json_decode($_GET['mas']) as $value) {
      echo " Фамилия: ".$value[0]." Имя: ".$value[1]." Отчество: ".$value[2];
	  $fields = Array('fam','fname','sname');
      $values = Array($value[0], $value[1], $value[2]);	  
	  $BD->update('people', $fields, $values, "`id` = ".$value[3]);
   }
 
?>