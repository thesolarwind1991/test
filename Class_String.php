<?
  /////////////////////////////////////////////////////////////////////
  //
  // Создатель CMS/Framework SolarWind: Ларионов Андрей Николаевич
  // По поводу приобретения платной версии системы обращаться:
  // Телефон для связи: 8-923-515-22-84
  // Email: IntegralAL@mail.ru
  // Социальная страница: https://vk.com/larionov.andrey  
  // Работа выполнена исключительно на энтузиазме разработчика Ларионова Андрея без должного финансирования
  // и финансовой подоплеки. Потому все претензии оставляйте у себя, платная поддержка подразумевает значительно более 
  // высокий уровень обеспечения и кодирования
  //
  //  php от 4v и 5.2 и даже до новейших 7                                                                                                         
  ////////////////////////////////////////////////////////////////

Class Class_String 
{
    private $source_string;	    	
	
	public function TestClass() {
	   return 'Ответ от класса Строки!';
	}
	
	////////////////////////////////////////////////
	//  Функция Set_string установки строки 
	//     string - значение 
	//     coder_text - кодировка строковых значений
	////////////////////////////////////////////////
	public function Set_string($string, $coder_text = "UTF-8")
	{
	   $lng = mb_strlen($string, $coder_text);
	   $utf_str = mb_substr($string, 0, $lng, $coder_text);
	   $this->source_string = $utf_str; 	 
	   //$this->source_string = $string; 	
	}
	
	////////////////////////////////////////////////
	//  Функция LngString возвращение длины строки в кодировке 
	//     coder_text - кодировка строки
	////////////////////////////////////////////////
	public function LngString($coder_text = "UTF-8")
	{
	   return mb_strlen($this->source_string, $coder_text);
	}
	
	/////////////////////////////////////////////////
	// Функция SubString возвращает значение подстроки из строки в установленной кодировке
	//
	//   coder_text - кодировка строки
	//
	/////////////////////////////////////////////////
	
	public function SubString($coder_text = "UTF-8")
	{
	    return mb_substr(trim($this->source_string), 
		                 1, 
						 mb_strlen($this->source_string, $coder_text)-2, 
						 $coder_text);	
	}
	
	public function I_create_white_army($_delslash = false, $_delcslashes = false)
	{
	    $temp =  mb_substr(trim($this->source_string), 0, mb_strlen($this->source_string, "UTF-8"), "UTF-8");	
		if ($_delslash) $temp = stripslashes($temp);		
		if ($_delcslashes) $temp = stripcslashes($temp);
		
		return $temp; 
	}
	
	public function I_Kill_black_army($addslash_ = false, $trim_ = false, $strip_tags_ = false, $addcslashes_ = false)
	{	    
		$temp =  mb_substr(trim($this->source_string), 0, mb_strlen($this->source_string, "UTF-8"), "UTF-8");	
		if ($addslash_) $temp = addslashes($temp);
		if ($trim_) $temp = trim($temp);
		if ($addcslashes_) $temp = addcslashes($temp, "\0..\377");
		
		return $temp;
		//return addslashes($temp);
		/*
		if ($temp[0] !== $specChar) $temp = $specChar.$temp;
		if ($temp[mb_strlen($temp, "UTF-8")] !==$specChar) $temp .= $specChar; 
        return $temp;				
		*/
	}
	
	/////////////////////////////////////////////
	//  Функция Get_string возвращения значения 
	/////////////////////////////////////////////
	
	public function Get_string()
	{
	   return $this->source_string;
	}
	
	////////////////////////////////////////////
	//  Функция Find_number поиска числа в строке 
	//
	////////////////////////////////////////////
	public function Find_number()
	{
	   return preg_replace("/[^0-9]/", '', $this->source_string); 
	   /*$tmp = $this::source_string;
	   if ($filter == 'number')
	    if (!empty($mask)) 
		{
           $pos = strpos($tmp, $mask);     
        }*/	   
	}
	
	/////////////////////////////////////////////
	// Функция формирования массива из данных обрамленных в теги {}
	//    coder_text - кодировка строки. 
	//
	/////////////////////////////////////////////
	public function Tags_array_N($coder_text = "UTF-8")
	{
	   $Tags = Array('{','}',',');
	   if ((!empty($Tags)) && (!empty($this->source_string)))
	   {	            		  
			$temp =  mb_substr(trim($this->source_string), 
			                   1, 
							   mb_strlen($this->source_string, $coder_text)-2, 
							   $coder_text);								
			$Items = Array();
			$Items = explode($Tags[2], $temp);			
			return $Items;		  	
			
       }
       else return 'Error';	   		
	}
	
	/////////////////////////////////////////////
	// Функция формирования массива из данных обрамленных в теги { , , , , }
	//    coder_text - кодировка строки. 
	//
	/////////////////////////////////////////////
	
	public function Tags_array($coder_text = "UTF-8")
	{
	   $Tags = Array('{','}',',');
	   if ($this->LngString() > 0) return 'Error';
	   else	   
	   {
			$temp = substr($this->Get_string(), 1, $this->LngString() - 2);								
			$Items = Array();
			$Items = explode(',', $temp);			
			return $temp;		  				
	   } 	   	   
	}
	
	/////////////////////////////////////////////////////////////////
	//  Функция Random_text формирования случайного числобуквенного кода
	//      _length_ - длина генерируемого кода
	//      only_number - аттрибут (только числовые) true/false  
	//
	/////////////////////////////////////////////////////////////////
	public function Random_text($_length_ = 10, $only_number = true) {
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
    
    ///////////////////////////////////////////////////////////////////
    //   Функция GeneratorIDUser создания числовой последовательности в формате id user 
    //     lenght - длина кода  
	//     only_number - аттрибут (только числовые значения)
	//
	///////////////////////////////////////////////////////////////////
    public function GeneratorIDUser($current_ID, $lenght = 16, $only_number = true) {
	  if (($current_ID) && (!empty($current_ID))) { 
	   if ($only_number) 
          $characters = '0123456789';
	   else 
          $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	   $charactersLength = strlen($characters);
	   $length_ID = strlen($current_ID);
	   
	   if ($length_ID == 0) return false;
	   else {
	      $randomString = '';
          $_length_ = $lenght - $length_ID;
		  
		  if ($_length_ > 0)
		    for ($i = 0; $i < $_length_; $i++) {
              $randomString .= $characters[rand(0, $charactersLength - 1)];    
		    }
		  return $randomString."_".$current_ID;	
	    }
	  } else 
          return false;		  
	}	
}
?>