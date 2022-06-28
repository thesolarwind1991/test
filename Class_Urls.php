<?
  /////////////////////////////////////////////////////////////////////
  // Создатель: Ларионов Андрей Николаевич
  /////////////////////////////////////////////////////////////////////

  include_once('Class_BD.php');
  
  Class Class_URLS {
	 private $directory = '';
	 private $Data;
	 public $path = '';
	 
	 function __construct(){ 
	    $this->Data = new Class_BD();		
 	 } 
	 
	 public function SetURL($url) {
		if ((isset($url)) && (!empty($url))) {
		  $this->directory = $this->path.$url;
		  $this->Data->insert('urls', Array('url'), Array($this->directory));
          return true; 		  
		} else 
			return false; 
	 }

	 public function GetData($where) {
        $GetData = $this->Data->select('urls', Array('id','url'), $where); 
		return $GetData;  
	 }	 
	 
     public function GetURL($id) {
        if (($id) && (!empty($id))) {
		  $GetData = $this->Data->select('urls', Array('id','url'), "id=$id"); 
	      return $this->directory = $GetData[0]['url']; 
		} else 	
		  return $this->directory; 
     }	 
	 
	 public function GenerateURL($_length_ = 6, $only_number = false) {
	   if ($only_number) 
          $characters = '0123456789';
	   else 
          $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            
       $charactersLength = strlen($characters);
       $randomString = '';
       for ($i = 0; $i < $_length_; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
       }
	
       return $randomString;	   
	}   	
  }	  
?>