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

 Class Class_paggination
 {
    public $start_blog;
	public $count_blog;
	public $amount_blogs;
	public $all_page;
    
    function Class_paggination()
    {
       return 'Class create Paggination';
    }	
	
	public function SetfromPage($page, $count_blog_, $amount_rec_)
	{
	   $all_page = (int)ceil($amount_rec_/$count_blog_);  	   
	   
	   if (($page == 1) || (empty($page)))
	      $this->start_blog = 0;
	   else
         if ($page != $all_page) 
		   $this->start_blog = $page * $amount_rec_ + 1;	   
		 else
           $this->start_blog = $page * $amount_rec_ + 1 - $amount_rec_;	   		 
		  	   
	   $this->count_blog = $count_blog_;	   
	   $this->amount_blogs = $amount_rec_;  
	}
	
	public function Set($first, $count_blog_, $amount_rec_)
	{
	   $this->start_blog = $first;
	   $this->count_blog = $count_blog_;
	   $this->amount_blogs = $amount_rec_;  
	}
	
	public function Component_Paggination($url, $Selected, $first = false, $last = false, $visible_items = 5)
	//                                     1       2         3               4              5
	{
	   $this->all_page = (int)ceil($this->amount_blogs/$this->count_blog); //ceil
	   
	   $FirstPos = 1;	
	   $iteration = $this->all_page;
	   $pointer1 = '...';
	   	          
       if ($this->amount_blogs > $this->count_blog)	     
	   {
	     $_Answer_ = "<ul class='pagination'>";	     
		 if ($first) $_Answer_ .= '<li><a class="first_pg" href="'.$url.'1">Первая</a></li>';		 		 
		 if ($Selected < $visible_items) {
		    $pointer1 = '';
			$pointer2 = '...';			 
			$FirstPos = 1;
			$iteration = $visible_items;
		 }
		 		 
		 if (($Selected >= $visible_items) && ($Selected < $this->all_page)) {		    
			$pointer1 = '...';
			$pointer2 = '...';			
			$FirstPos = $Selected - 2;
			$iteration = $Selected + 2;
			if ($iteration >  $this->all_page) $iteration = $this->all_page;
		 }		 
		 
		 if (($Selected >= $visible_items) && ($Selected == $this->all_page)) {
		    $pointer1 = '...';
			$pointer2 = '';
			$FirstPos = $Selected - $visible_items + 1;
			$iteration = $Selected;			
		 }		 
		 
		 if ($this->all_page == $visible_items) {
		    $pointer1 = '';
			$pointer2 = '';
		 }
		 
		 if ($this->all_page < $visible_items) {
		    $iteration =  $this->all_page;
			$pointer1 = '';
			$pointer2 = '';
		 }
		   		 	
         if (!empty($pointer1))
		   $_Answer_ .= '<li><a href="">'.$pointer1.'</a></li>';
		   
		 for ($i = $FirstPos; $i <= $iteration; $i++)
         {
	       if ($i == $Selected) $Select = " style='background: rgb(240, 90, 84);' ";
	       else $Select = '';
		   if ((empty($Selected)) && ($i == 1)) $Select = " style='background: rgb(240, 90, 84);' ";
           $_Answer_ .= '<li><a '.$Select.' href="'.$url.$i.'"> '.$i.' </a></li>';
         }

		 if (!empty($pointer2))
		   $_Answer_ .= '<li><a href="">'.$pointer2.'</a></li>';
		 
		 if ($last) $_Answer_ .= '<li><a class="last_pg" href="'.$url.$this->all_page.'">Последняя</a></li>';		 
	     $Answer .= '</ul>';	    
	   }
       return $_Answer_;	   
	}
	
    public function Paggination($current_page)
    {
        if ($this->count_blog > 0)
		   $all_page = (int)ceil($this->amount_blogs/$this->count_blog); // ceil
		else
           $all_page = 1;		

        if (empty($current_page)) $current_page = 1;
        $limit = '';    
        $_page = (int)$current_page;
  
        switch ($_page) {
          case 1: return $this->start_blog.', '.$this->count_blog; break;	 
	      case $all_page: return (($all_page-1) * $this->count_blog).', '.$this->count_blog; break;
		  //case $all_page: return ($this->amount_blogs-$this->count_blog).', '.$this->count_blog; break;
	      default: return (($_page - 1) * $this->count_blog).', '.$this->count_blog; break;	 
        }
    }	
  }
?>