<?
  /////////////////////////////////////////////////////////////////////
  //
  // Создатель CMS/Framework SolarWind: Ларионов Андрей Николаевич
  // По поводу приобретения платной версии системы обращаться:
  // Телефон для связи: 8-923-515-22-84
  // Email: IntegralAL@mail.ru
  // Социальная страница: https://vk.com/larionov.andrey  
  //
  ////////////////////////////////////////////////////////////////////
  //  Работа выполнена исключительно на энтузиазме разработчика Ларионова Андрея без должного финансирования
  //  и финансовой подоплеки. Потому все претензии оставляйте у себя, платная поддержка подразумевает значительно более 
  //  высокий уровень обеспечения и кодирования
  //
  //  php от 4v и 5.2 и даже до новейших 7                                                                                                         
  ////////////////////////////////////////////////////////////////
 
  class Class_Config  {             
        const MAIN_DIR = __DIR__;
		private $_MySQL_;
		public $ipBD = "localhost";
        public $NameBD = "peoples";    		
		public $LoginBD = "root"; 
        public $PwdBD = "";
		public $pref = ''; 
		public $Debug = false;
		public $FolderRoot = 'peoples'; 
		public $localServer = true;		

        function __construct() {		
			  $this->_MySQL_ = new mysqli($this->ipBD,
	                                    $this->LoginBD,
	                                    $this->PwdBD,
								        $this->NameBD);   
			  			
			  if ($this->_MySQL_->connect_errno) {
		         printf("No connect with BD: %s\n", $this->_MySQL_->connect_error);
                 exit();
              } else {
	      
	          }
			
			  $this->_MySQL_->query("SET lc_time_names = 'ru_RU'");
              $this->_MySQL_->query("SET NAMES 'utf8'");
              			
			  if ($this->_MySQL_) mysqli_close($this->_MySQL_); 
				  
		}
				
		public function __destruct()
        {        
	       unset($this->ipBD);
	       unset($this->LoginBD);
	       unset($this->PwdBD);
		   unset($this->NameBD);
		   unset($this->_MySQL_);
        }  	
  } 
 ?>