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

include_once('Class_Config.php');
include_once('Class_BD.php');

  Class Class_IP
  {  
    private $BD_Obj; 
	private $Conf_Obj; 
   
    function __construct()
    {
	    $this->BD_Obj = new Class_BD;
		$this->Conf_Obj = new Class_Config;
		//$this->Paggi = new Class_Paggination;	
	}
  
	public function TestClass() 
	{	    
	    return 'Ответ от класса Class_IP';
	}
	 
	///////////////////////////////////////////////////////
	//   Функция get_ip из сети функция (внешняя)
	//   Параметры: - 
	//////////////////////////////////////////////////////
	public function get_ip()
    {
       if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
           $ip = $_SERVER['HTTP_CLIENT_IP'];
       }
       elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
       }
       else {
          $ip=$_SERVER['REMOTE_ADDR'];
       }
       return $ip;
    }
    
	
	private function getUrl($PHP_SELF = false) {
       $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
       $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
       if ($PHP_SELF)
	     $url .= $_SERVER["PHP_SELF"];
	   else	 	   
	     $url .= $_SERVER["REQUEST_URI"];
      return $url;
    }
	
	//////////////////////////////////////////////////////////
	// Функция добавления записи в лог add_logs_ip
	//   Параметры: 
	//    $geolocation - геолокационное определение клиента по ip-адресу
	//////////////////////////////////////////////////////////
	public function add_logs_ip($geolocation = false) {
	    if ($geolocation) {
		   
		} else {
		   $ip_user = $this->get_ip();
 		   $page_look = $this->getUrl();
		   $macaddress = '000-0000-000-000';
		   $fields = Array('ipaddress', 'macaddress', 'page_look');		   
		   $values = Array($ip_user, $macaddress, $page_look);				   		   
           $Answer = $this->BD_Obj->insert('clicker', $fields, $values, false);	
           return $Answer;		   
		}
	}

	////////////////////////////////////////////////////////
	// Функция see_logs_ip вывода лога адресов в интервале времени 
	//  Параметры:
	//   $time_begin - начало временного интервала (работать только с оригиналом)
	//   $time_end - конец временного интервала 
	//   
	////////////////////////////////////////////////////////
    public function see_logs_ip($time_begin, $time_end) {
	   if ($time_begin > $time_end) {
	      $tmp = &$time_begin;
		  $time_begin = $time_end;
		  $time_end = $tmp;
	   }
	   
	   $where = '';	   
	   if (!empty($time_begin)) $where .= "(`time_look` >".$time_begin.")";
	   if (!empty($time_end)) {
	     if (!empty($where)) $where .= "&&";
	     $where .= "(`time_look` <".$time_end.")";
	   }
	   
	   $Answer = $this->BD_Obj->select('clicker', Array('*'), $where, '', true);		
       return $Answer;	   
	}	
    
  	////////////////////////////////////////////////////////
	// Функция see_logs_ip_param вывода лога адресов в интервале времени с параметрами
	//  Параметры:
	//   $time_begin - начало временного интервала (работать только с оригиналом)
	//   $time_end - конец временного интервала 
	//   
	////////////////////////////////////////////////////////
    public function see_logs_ip_param($time_begin, $time_end, 
	                            $current_page, $amount,
								$paggi_where, $ip_address) {
	   
	   if ($time_begin > $time_end) {
	      $tmp = &$time_begin;
		  $time_begin = $time_end;
		  $time_end = $tmp;
	   }
	   
	   $where = '';	   
	   if (!empty($time_begin)) $where .= "(`time_look` >".$time_begin.")";
	   if (!empty($time_end)) {
	     if (!empty($where)) $where .= "&&";
	     $where .= "(`time_look` <".$time_end.")";
	   }
	   
	   $Answer = $this->BD_Obj->select('clicker', Array('*'), $where, '', true);		
       return $Answer;	   
	}	
} 
?>