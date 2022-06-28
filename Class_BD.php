<?php
  /////////////////////////////////////////////////////////////////////
  //
  // Создатель CMS/Framework SolarWind: Ларионов Андрей Николаевич
  // По поводу приобретения платной версии системы обращаться:
  // Телефон для связи: 8-923-515-22-84
  // Email: IntegralAL@mail.ru
  // Социальная страница: https://vk.com/larionov.andrey  
  // Класс работы с базой данных CMS SoralWind                
  // Разработчик: Ларионов Андрей.                            
  // Дата разработки: От Январь 2016 года. 
  //                   
  // Работа выполнена исключительно на энтузиазме разработчика Ларионова Андрея без должного финансирования
  // и финансовой подоплеки. Потому все претензии оставляйте у себя, платная поддержка подразумевает значительно более 
  // высокий уровень обеспечения и кодирования
  //
  // php от 4v и 5.2 и даже до новейших 7                                                                                                         
  //////////////////////////////////////////////////////////////

require_once('Class_Config.php');
//require_once('Class_report_error.php');

//require_once('config.php');
//require_once 'Class_checkvalid.php'; 

Class Class_BD 
{
	private $report_error1;	
	private $Config1;
	public $current_date;
	public $mysqli;
	public $ModeEdit;
	public $Current_code;
	
	public function TestClass() {
	   return 'Ответ от класса БД получен!';
	}

	//////////////////////////////////////////////////////////////////
	// Constructor. Create exemplar.
	//////////////////////////////////////////////////////////////////
    function __construct()
    {  // Инициализация класса репорта ошибок
	   //print "Конструктор класса BaseClass\n";
	   
	   //$this->report_error1 = new Report_error();
	   $this->Config1 = new Class_Config();		
	   $ModeEdit = false;
	   
	   // Инициализация класса конфигуратора и подключение к бд
	    $this->mysqli = new mysqli($this->Config1->ipBD,
	                              $this->Config1->LoginBD,
	                              $this->Config1->PwdBD,
								  $this->Config1->NameBD);
	      
          	  
       if ($this->mysqli->connect_errno) 
	   {
		 printf("No connect with BD: %s\n", $this->mysqli->connect_error);
         exit();
       } 
	   else {
	      $ModeEdit = true;
	   } 
	     $CodeWeb = 'RU';
	     $this->mysqli->query("SET lc_time_names = 'ru_RU'");
         $this->mysqli->query("SET NAMES 'utf8'");	   		   
		 	
		 $this->current_date = date('d.m.y'); 	           
		 		   	   
	} 
	
	////////////////////////////////////////////////////////////////
	// Private function execute sql
	////////////////////////////////////////////////////////////////
    public function external_query($sql)
	{
        $result = $this->mysqli->query($sql);
		return $result;	  
	}	
	
	///////////////////////////////////////////////////////////////
	// public function - Simple table
	///////////////////////////////////////////////////////////////
	public function simple_select($sql, $debug = false)
	{
	    if ($debug == true) { 
		  return $sql;
		} else {  
	      $result_set = $this->mysqli->query($sql);

		  if (!$result_set) return false;		
		  $i = 1;
		  $data = array();
		  while ($row = $result_set->fetch_assoc()) {
		    $data[$i] = $row;
		    $i++;
		  }

		  $result_set->close();
		  return $data;
       }		  
	}
	
	///////////////////////////////////////////////////////////////
	// Function Annulate specsimbol
    //	хакинк против sql-иньекций
	///////////////////////////////////////////////////////////////
	public function _strip($data)
	{
		$lit = array("\\t", "\\n", "\\n\\r", "\\r\\n", "  ", "(", ")");
		$sp = array('', '', '', '', '', '[', ']');
		return str_replace($lit, $sp, $data);
	}

	///////////////////////////////////////////////////////////////
	// Function _desrtip
    //	обращение символов 
	///////////////////////////////////////////////////////////////
	public function _destrip($data)
	{
		$lit = array('&[&', '&]&');
		$sp = array("(", ")");
		return str_replace($lit, $sp, $data);
	}	
	
	///////////////////////////////////////////////////////////////
	// Function xss blocking
    // Хакинг против xss-атак	
	///////////////////////////////////////////////////////////////
    public function xss($data)
	{
		if (is_array($data)) {
			$escaped = array();
			foreach ($data as $key => $value) 
			{ $escaped[$key] = $this->xss($value); }
			return $escaped;
		}
		return htmlspecialchars($data, ENT_QUOTES);
	}
		
	///////////////////////////////////////////////////////////////
	//
	// Function Insert of data
	// $table_name - Name Table
	// $fields - list of field's
	// $values - list of field's 
	//
	///////////////////////////////////////////////////////////////
	
	public function insert($table_name, $fields, $values, $debug = false)
	{
	  if ((count($fields)) == (count($values)))
	  {
		 // Защита от xss-атаки 
		 $esc_ = Array();	
		 foreach ($values as $key_ => $value_)
         {		  
		    $esc_[$key_] = $this->_strip($value_);
		 }
		 // Защита от ошибок экранирования
		 $values = $esc_; 	
		 $values = $this->xss($values);
		 $return_where = "(";
		 //
         for ($i = 0; $i < count($fields); $i++) 
	     {
		    if ((strpos($fields[$i], "(") === false) && ($fields[$i] != "*")) 
			  $fields[$i] = "`".$fields[$i]."`";  
		    if ((strpos($values[$i], "(") === false) && ($values[$i] != "*")) 
			  $values[$i] = "'".$values[$i]."'";  	 		 	 
			
			$return_where .= "(`".$fields[$i]."` = '".$values[$i]."')";
            if ($i < count($fields)-1) $return_where .= " and ";			
		 }
	   	 $return_where = ")";
		 
		 $fields = implode(",", $fields);
		 $values = implode(",", $values);
			   		  
		 $table_name = $this->Config1->pref.$table_name;
		  
		 $query = "Insert into `$table_name` ($fields) Values ($values)";
		  		  
		 if ($debug) 
		   return $query;
		 else 
		 {
            $dt = $this->mysqli->query($query); 						
     
            return true;			
		 } 
	  }
      else return false;  
	}
	
	///////////////////////////////////////////////////////////////
	//
	// Function Delete of data - deleteRecord
	// $table_name - Name Table
	// $id - number delete record
	// $debug - отладчик кода
	//
	///////////////////////////////////////////////////////////////
    public function deleteRecord($table_name, $id = '', $debug = false)
	{
	  $table_name = $this->Config1->pref.$table_name;	
      if (empty($id))
		    $query = "Delete from `$table_name`";
	  else 	
	        $query = "Delete from `$table_name` Where (`id` =".$id.")";
	  $value = $this->mysqli->query($query);
      if (!$debug) 
         return $value; 
      else 
         return $query;		  
	}

	///////////////////////////////////////////////////////////////
	//
	// Function Delete of data - deleteRecordWhere
	// $table_name - Name Table
	// $id - number delete record
	// $debug - отладчик кода
	//
	///////////////////////////////////////////////////////////////	
	public function deleteRecordWhere($table_name, $where, $debug = false) {
	  $table_name = $this->Config1->pref.$table_name;	

	    if (empty($where))
		    $query = "Delete from `$table_name`";
	    else 	
	        $query = "Delete from `$table_name` Where ".$where."";
	    $value = $this->mysqli->query($query);	
        if (!$debug)
		   return $value;	
		else 
           return $query;        	  
	}	
		
	///////////////////////////////////////////////////////////////
	//
	// Function Update of data
	// $table_name - Name Table
	// $fields - list of field's
	// $values - list of field's 
	//
	///////////////////////////////////////////////////////////////
	public function update($table_name, $fields, $values, $where, $debug = false)
	{
	  if ((count($fields)) == (count($values)))
	  {
		 $esc_ = Array();	
		 foreach ($values as $key_ => $value_)
         {		  
		   $esc_[$key_] = $this->_strip($value_);
		 }
		 $values = $esc_; 	
		 $values = $this->xss($values);

    	  for ($i = 0; $i < count($fields); $i++) 
	      {
		    if ((strpos($fields[$i], "(") === false) && ($fields[$i] != "*")) 
			  $fields[$i] = "`".$fields[$i]."`";  		    
		   if ((strpos($values[$i], "(") === false) && ($values[$i] != "*")) 
			  $values[$i] = "'".$values[$i]."'";  		 	 
	      }
	      
		  $table_name = $this->Config1->pref.$table_name;	 
		  $query = "Update `$table_name` set ";
		   
		  for ($i = 0; $i < count($fields); $i++) 
	      {
		 	 $query.= ''.$fields[$i].'='.$values[$i].'';
			 if ($i < (count($fields)-1)) 
			   $query.=',';	   
	      }
		  
		  $query.= ' Where ('.$where.');';
		  		  
		  //if (!$result_set) return false;
		  //else return true;
		  if ($debug) 
			return $query;
		  else {		  
		    $this->mysqli->query($query);
			return true;
		  }
	  }
      else return false;
	}
	
	//////////////////////////////////////////////////////////////////
	// count_sql_rec функция количества записей
	//  1. sql - sql-запрос
	//  2. debug - отладчик 
	//////////////////////////////////////////////////////////////////	
    public function count_sql_rec($sql, $debug = false)
	{ 	 
	   if (isset($debug) && ($debug)) {
	     return $sql;
	   }	 
	   else
	   {	     
	      $result_set = $this->mysqli->query($sql);	  
	      if (isset($result_set) && (!empty($result_set)))  
		    return $result_set->num_rows;
          else 
            return false; 			  
	   }	
	}	
	
	//////////////////////////////////////////////////////////////////
	// count_rec функция количества записей
	//  table_name - название таблицы
	//  where - условия
	//////////////////////////////////////////////////////////////////
	
    public function count_rec($table_name, $where = '', $debug = false)
	{ 	 
	   $select = 'Select * from `'.$this->Config1->pref.$table_name.'` ';
	   if (!empty($where)) $select.= $where;
	   $select.= ';';
	   
	   if (isset($debug) && ($debug)) {
	     return $select;
	   }	 
	   else
	   {	     
	      $result_set = $this->mysqli->query($select);	  
	      if (isset($result_set) && (!empty($result_set)))  
		    return $result_set->num_rows;
          else 
            return "0"; 			  
	   }	
	}	
	
    ///////////////////////////////////////////////////////////////
    // Function calling stored procedure
    //   name_procedure - name stored procedure.	
    //
    ///////////////////////////////////////////////////////////////
    public function exec_procedure($name_procedure)	{
		if (empty($name_procedure)) return "Error";
		else {
            $pro = $this->mysqli->prepare("CALL ".$this->Config1->pref.$name_procedure);
            $data = $pro->execute();			
		    return $data;			
        }		
	}	
	
	/////////////////////////////////////////////////////////////////
	// Function get result of field 
	// Функция возврата единичного значения поля 
	//
	// параметры:
	//    table_name - имя таблицы  
	//    field_name - поле возврата 
	//    where - условия выбора  
	//
	////////////////////////////////////////////////////////////////
	public function get_field($table_name, $field_name, $where, $debug = false) {
		if ((empty($table_name)) || (empty($field_name))) {
          exit;
          return "Error: Имя таблицы и возвращаемое значение поля пусто!";		  
		} else {		
		  $select = "Select `$field_name` from `".$this->Config1->pref.$table_name."` ";
		  if (($where) && (!empty($where))) $select .= " Where ".$where;
		  
		  if ($debug) {
		    return $select;
		  } else {
		    $result_set = $this->mysqli->query($select);	
		    //if (is_array($result_set)) {
			if ((!empty($result_set)) && (count($result_set) > 0)) {
			  foreach ($result_set as $datas) {
		        $value = $datas;
		      }		  
        	  
			  return $value[$field_name]; 
			} else 
                return false;			
		    
		  }	
		}  
	}
	
	/////////////////////////////////////////////////////////////////
	// Function get_random_record 
	// Функция возврата случайного значения из поля field_name
	//
	// параметры:
	//    table_name - имя таблицы  
	//    field_name - поле возврата 
	//    where - условия данных 
	//
	////////////////////////////////////////////////////////////////	
	public function get_random_record($table_name, $field_name, $where) {
       if ((empty($table_name)) || (empty($field_name))) {
          exit;
          return "Error: This name table or value clear!";		  
		} else {		
		  if (is_array($field_name)) {
            $strfield = "";
			$i = 0;
			foreach ($field_name as $field) {
                if ($i > 0) $strfield .=", ";
				$strfield.= $field;
                $i++;				
            }            
		    $select = "Select $strfield from `".$this->Config1->pref.$table_name."` ";    			
		  } else 
		    $select = "Select `$field_name` from `".$this->Config1->pref.$table_name."` ";
		  if (($where) && (!empty($where))) $select .= " Where ".$where." ";
		  $select .= " Order By Rand()";
		  $select .= " Limit 1;";
		  
		  $result_set = $this->mysqli->query($select);	  		  
		   foreach ($result_set as $datas)
		     return $datas;  			  
		}				  
    }	
	
	///////////////////////////////////////////////////////////////
	// Function DistinctSelect  
	//    
	// table_name - name table 
	// field - field for input data
	// where - list all if of execute sql
	// order - list sort fields
	// up - sort of fields
	// limit - amount look record's 
	//
	///////////////////////////////////////////////////////////////
	public function DistinctSelect($table_name, $field, $where = "", 
	                               $order = "", $up = true, $limit = "", 
								   $debug = false)
	{
		if (($table_name) && ($field) && (!empty($table_name)) && (!empty($field))) {
		  $sql = "Select DISTINCT ";
	     // if ($this->Config1->CodeLicense == base64_encode(base64_encode(php_uname()))) 
		    $sql .= "`".$field."` from `".$this->Config1->pref.$table_name."`";
	      //else 
		  //  $sql .= "`".$field.", *.* from ".$this->Config1->pref.$table_name."`";
		  	
		  if (($where) && (!empty($where)))
		    $sql .= " Where (".$where.")";
		  if (($order) && (!empty($order))) { 
			 $sql .= ' ORDER BY '.$order;
			   if ($up)  
			     $sql .= ' ASC';
			   else 
			     $sql .= ' DESC';  
		  }
		  
         if (($limit) && (!empty($limit))) $sql .= ' LIMIT '.$limit;
			$sql .= ';';

         if ($debug) 
           return $sql;
	     else {
		 
			$result_set = $this->mysqli->query($sql);			
			if ($result_set) {
			   $data = Array();
			   while ($row = $result_set->fetch_assoc()) {
		          $data[$colvo] = $this->_destrip($row);
		          $colvo++;
		       }
			   
			   $result_set->close();
               return $data; 
		   } else return false;	 
		 }	   
		} else 
			return false;
	}
	
	///////////////////////////////////////////////////////////////
	// Open function select
	// parametr's:
	// 1. table_name - name table 
	// 2. fields - list all fields
	// 3. where - list all if of execute sql
	// 4. order - list sort fields
	// 5. up - sort of fields
	// 6. limit - amount look record's
    // 7. debug - mode text-sql	
	////////////////////////////////////////////////////////////////
    public function select($table_name, $fields, $where = "", 
	                       $order = "", $up = true, 
						   $limit = "", $debug = false) 
	{
	    $select = 'Select ';
	    if (is_array($fields))
		{
		    $colvo = 0;		  
			foreach ($fields as $field)
			{ 
              if ($colvo > 0) $select .= ',';			
			   if ($field == '*') $select .= $field;
			   else $select .= '`'.$field.'`';
              $colvo++;			  
			}
			
			$colvo = 0;
			//if ($this->Config1->CodeLicense === base64_encode(base64_encode(php_uname()))) 
			  $select .= ' from `'.$this->Config1->pref.$table_name.'`';
			//else 
			//  $select .= ' from `'.$this->Data_Config->pref.$table_name.'`';
			
			if (($where) && (!empty($where))) $select .= ' WHERE ('.$where.')';
			//if (($order) && (!empty($order))) { 
			 if ($order) {
			   $select .= ' ORDER BY '.$order;
			   if ($up)  
			     $select .= ' ASC';
			   else 
			     $select .= ' DESC';
				 
			}
			if (($limit) && (!empty($limit))) $select .= ' LIMIT '.$limit;
			$select .= ';';						
			
			if ($debug)
			{
			   return $select;
			} 
			else {
			  $result_set = $this->mysqli->query($select);			
			  if ($result_set) {
			   $data = Array();
               /*foreach ($result_set as $row)
			   {
			      $data[$colvo] = $row;
		          $colvo++;
			   }*/
			   while ($row = $result_set->fetch_assoc()) {
		          $data[$colvo] = $this->_destrip($row);
		          $colvo++;
		       }
			   
			   $result_set->close();
               return $data;			   
			}
			else return false;	      
		   }	
		}
    }  
	
	//////////////////////////////////////////////////////
	// Public query SQL 
	// Test function for track 
	//////////////////////////////////////////////////////
    public function public_query($sql)
	{
       $mysqli = new mysqli("localhost", "root", "", "cmdpwd");
       if ($mysqli->connect_errno) 
	   {
          printf("Fail connect: %s\n", $mysqli->connect_error);
          exit();
       }
       $query = "SELECT * FROM Roles";
       if ($result = $mysqli->query($query)) {
       	   echo "<table>";
	   echo "<tr><td>Number</td><td>Type users</td></tr>";
       while ($row = $result->fetch_assoc()) 
	   {
          echo "<tr><td>".$row["id"]."</td><td>".$row["name_role"]."</td></tr>";
       }
	   echo "</table>";
	   
        $result->free();
       }

     $mysqli->close();		
		
	}	

  	////////////////////////////////////////////////////////////////
	// Destructor. Delete all object's  
	////////////////////////////////////////////////////////////////
	public function __destruct()
	{
	   unset($this->Config1);
	   unset($this->report_error1);
       unset($this->current_date);
	   unset($this->mysqli);
	}
}  

?>