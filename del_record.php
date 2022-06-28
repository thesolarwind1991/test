<? require_once('Class_BD.php'); 
   
   $BD = new Class_BD();
   //$Data = $BD->select('people', Array('*'), '', false, false); 
   foreach (json_decode($_POST['data']) as $value) {
      $BD->deleteRecord('people', $value);
   }
   print_r($_POST['data']);?>