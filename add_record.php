<? require_once('Class_BD.php'); 
   $BD = new Class_BD();
   
   $fields = Array('fam', 'fname', 'sname');
   $values = Array($_POST['fam_input'], $_POST['name_input'], $_POST['sname_input']);
   $Answer = $BD->insert('people', 
                        $fields, 
						$values, 
						false);
      
   echo "ФИО: ".$_POST['fam_input']." ".$_POST['name_input']." ".$_POST['sname_input'];
?>