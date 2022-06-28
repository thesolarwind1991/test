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

require_once('Class_Config.php'); 
include_once('Class_BD.php');
include_once('Class_String.php');

Class Class_Files
{
    public $TagList;
	public $Selected_Item;
	public $MaxSizeFile;
	public $MaxAmountFiles;
	public $Path;
	public $DB;
	public $ConfigConnect;
	
    function __construct()
    {  
	   $this->MaxSizeFile = 11;
	   $this->MaxAmountFiles = 50;
	   $this->Selected_Item = " selected = 'selected' ";	   	   
       $this->ConfigConnect = new Class_Config;	   
    }

	public function TestClass() {
	   return 'Ответ от класса Class_Files получен!';
	}

	///////////////////////////////////////////////////////////////////
	//   ScanDirListStr($SelectItem) - строковый метод записи данных
	//   ScanDirList($SelectItem) - массивный метод записи данных с предустановленной сортировкой
	//   
	//   Функция сканирования файлов в виде списка, где теги задаются через TagList
	//   $SelectItem - имя маски-файла 
	//   $Answer - ответ строковые значения <option> значения </option>
	///////////////////////////////////////////////////////////////////
    public function ScanDirListStr($SelectItem)
	{
        $Answer = '';		
		if (empty($this->TagList))	$this->TagList = 'li';
	    if (empty($this->Path)) $this->Path = 'upload';
        if ($handle = opendir($this->Path)) {
                   
		  
          /* Именно этот способ чтения элементов каталога является правильным. */
          while (false !== ($file = readdir($handle))) { 
		    $tmp = '';
			$SFile = 'table'.$SelectItem.'.jpg';
            if ((!empty($SelectItem)) && ($SFile == $file)) 			
			   $tmp = $this->Selected_Item;
			
			if (($file !== '..') && ($file !== '.'))
			  $Answer .= "<".$this->TagList." ".$tmp.">".$file."</".$this->TagList.">";
          }		
		  closedir($handle); 		  
        }
		sort($Answer);
		return $Answer;
    }

	//////////////////////////////////////////////////////
	// Функция сканирования upload темы.
	//    
	//      SelectItem - картинки темы.  
	//
	//////////////////////////////////////////////////////
	public function ScanDirList($SelectItem)
	{
        $Answer = Array();
		$Answer_Str = '';
		
		if (empty($this->TagList))	$this->TagList = 'li';
	    if (empty($this->Path)) $this->Path = 'upload';
        if ($handle = opendir($this->Path)) {
                   	  
        /* Именно этот способ чтения элементов каталога является правильным. */
          while (false !== ($file = readdir($handle))) { 
		    $tmp = '';
			$SFile = 'table'.$SelectItem.'.jpg';
            if ((!empty($SelectItem)) && ($SFile == $file)) 			
			   $tmp = $this->Selected_Item;
			
			if (($file !== '..') && ($file !== '.'))
			  $Answer[] = $file;
			  //$Answer .= "<".$this->TagList." ".$tmp.">".$file."</".$this->TagList.">";
          }		
		  closedir($handle); 
          sort($Answer);
          foreach ($Answer as $files) {
		     $Answer_Str .= "<".$this->TagList." ".$tmp.">".$files."</".$this->TagList.">";
		  }
        }
		
		return $Answer_Str;
    }

	
	////////////////////////////////////////////////////////////////
	//   ScanDirFilesAndFolder($Folder)
	//   Функция сканирования файлов и папок в виде массива
	//   $Folder - имя папки сканирования
	//   $LFiles - ответ массив имен файлов
	///////////////////////////////////////////////////////////////	
	public function ScanDirFilesAndFolder($Folder, $Maska = Array('jpg'))
	{
	   $LFiles = Array();
	   $amount = 0;
	   if ((empty($Folder))&&(empty($this->Path))) return false;
       else
       {
	      if (!empty($this->Path)) $handle = opendir($this->Path);
		  if (!empty($Folder)) {
		     $this->Path = $Folder;
			 $handle = opendir($Folder);			 
		  }	 

          while (false !== ($file = readdir($handle))) { 			
		    if (($file !== '..') && ($file !== '.')) {
    	       if (mb_strlen($file, "UTF-8") > 0) {	           
			      $temp = substr($file, mb_strlen($file, "UTF-8") - 3, mb_strlen($file, "UTF-8"));								
			      if (is_array($Maska)) {
				    foreach ($Maska as $val)
                      if ($temp == $val)					
				        $LFiles['files'][] = $file;
				  }
                     				  
			      $amount++;
               }				  
			}   
          }
		  
		  $LFiles['amount'] = $amount;
		  $LFiles['path'] = $Folder;   
		  if (count($LFiles['files']) > 0)
		    sort($LFiles['files']);
		  closedir($handle); 		  		  
          return $LFiles;
       }
	}
	
	////////////////////////////////////////////////////////////////
	//   ScanDirFolder($Folder)
	//   Функция сканирования файлов в виде массива
	//   $Folder - имя папки сканирования
	//   $LFiles - ответ массив имен файлов
	////////////////////////////////////////////////////////////////	
	public function ScanDirFolder($Folder)
	{
	   $LFiles = Array();
	   $amount = 0;
	   if ((empty($Folder))&&(empty($this->Path))) return false;
       else
       {
	      if (!empty($this->Path)) $handle = opendir($this->Path);
		  if (!empty($Folder)) {
		     $this->Path = $Folder;
			 $handle = opendir($Folder);			 
		  }	 

          while (false !== ($file = readdir($handle))) { 
			if (($file !== '..') && ($file !== '.'))
            {			 
			   $LFiles['files'][] = $file;
			   $amount++;
			}   
          }
		  
		  $LFiles['amount'] = $amount;
		  $LFiles['path'] = $Folder;   
		  sort($LFiles);
		  closedir($handle); 		  		  
          return $LFiles;
       }
      	   
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////////
	//   ScanDirTheme($Folder)
	//   Функция сканирования папок темы.
	//   
	//   $Folder - имя папки сканирования
	//   $Path_ - директория пути сканирования 
	//   $Array_exception - массив исключений из общего правила сканирования  
	//   $tfile - расширения для файлов (например, если маска - .php, то алгоритм будет искать 
	//   в массиве Array_exception только элементы с расширением .php 
	//
	////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function ScanDirTheme($Folder = '', $Array_exception = Array(), $Path_ = 'core/views/themes/', $tfile = '.php')
	{
		$Answer = Array();							    
		$except = true;
        if ((empty($Array_exception)) || (!is_Array($Array_exception)))
		  $except = false;
       
		$Theme = $this->ConfigConnect->Name_Theme;		         						
		if (empty($Folder)) $Folder = $Theme;

		if (empty($Path_)) $Path_ = $this->Path;
        $Path_ .= $Folder;		
		
		if (is_dir($Path_)) {
          if ($dh = opendir($Path_)) {
               while (($file = readdir($dh)) !== false) {
                  if (($file != '..') && ($file != '.')) {
                    if ($except) {
                         $find = false;
					     foreach ($Array_exception as $key_except => $val_except) {					      
						   if ($val_except == $file) $find = true;							
					     }
					     if (!$find) $Answer[] = $file;
					   
                       } else {					
					      $pos = -1;
                          $pos = strpos($file, $rfile);
                          if ($pos > 0) {  					  						
					        $Answer[] =  $file;  	
					      }
					   }  
					} /*else {
                       $Answer[] =  $file;*/  	
                    //}						
                }					               
               closedir($dh);
            }
         } else 
			  echo "Ошибка директории! [".$Path_."] ";
        
		
		sort($Answer);
		return $Answer;        
	}

	///////////////////////////////////////////////////////////////////////////////
	//   ReadFile($NameFile)
	//   Функция чтения контента файла
	//   $Folder - имя папки сканирования
	//   $Answer - ответ массив имен файлов
	///////////////////////////////////////////////////////////////////////////////		
	public function ReadFiles($NameFile)
	{
	   // ./people.txt
	   if (file_exists($NameFile)) {
	      $file = file_get_contents($NameFile, FILE_USE_INCLUDE_PATH);
	      return $file; // ответ да, (уши слышат и читают.  )
	   } else return 0;	  
	}
    
	////////////////////////////////////////////////////////////////////////////
	//   CreateExecThemeFiles($name, $content = '')
    //   Функция создания страницы-файла в теме проекта       
	//   $name - имя документа-файла, get-параметра в строке
	//   $content	- контент-документа, по-умолчанию, значение пустое   
	//   
	//   Возращает true в случае успеха, и false в случае ошибки
	//
	/////////////////////////////////////////////////////////////////////////////
    public function CreateExecThemeFiles($name, $content = '')
    {       
	   $Createfile = true;
	   $Dir = $_SERVER['DOCUMENT_ROOT'].'/'.$this->ConfigConnect->FolderRoot.'/';      
       $file = $name.".php";       	   
	   $fileDir = $Dir.'core/views/themes/'.$this->ConfigConnect->Name_Theme.'/'.$file;
       	   
	   try {
	      $handle = fopen($fileDir, "a"); 
          fwrite($handle, $content);
       }
	   catch (Exception $e) {
	     $Createfile = false;
	   } 

	   if ($Createfile) fclose($handle);		   
	   return $Createfile;       				
    }

	///////////////////////////////////////////////////////////////
	//   DeleteExecThemeFiles($name)
    //   Функция удаления страницы-файла в теме проекта       
	//   $name - имя документа-файла, get-параметра в строке
	//   
	//   Возращает true в случае успеха, и false в случае ошибки
	///////////////////////////////////////////////////////////////
	public function DeleteExecThemeFiles($name)
	{
	   $Remove_file = false;
	   $Dir = $_SERVER['DOCUMENT_ROOT'].'/'.$this->ConfigConnect->FolderRoot.'/';      
       $file = $name.".php";       	   
	   $fileDir = $Dir.'core/views/themes/'.$this->ConfigConnect->Name_Theme.'/'.$file;
	   if (file_exists($fileDir)) {
	      unlink($fileDir);	   
		  $Remove_file = true;
	   } 
	    
	   return $Remove_file;	 
	}

	////////////////////////////////////////////////////////////
	//   EditExecThemeFiles($newname, $name)
    //   Функция редактирования страницы-файла в теме проекта       
	//   $newname - новое имя документа-файла
	//   $name - первоначальное имя документа
	//   
	//   Возращает true в случае успеха, и false в случае ошибки
	/////////////////////////////////////////////////////////////	
	public function EditExecThemeFiles($newname, $name, $content = '')
	{	    
	    $Operation = true;
		if (!$this->DeleteExecThemeFiles($fileDir)) $Operation = false;             		   		   
		if (!$this->CreateExecThemeFiles($newname, $content)) $Operation = false;
		return $Operation; 
	}
	
    //////////////////////////////////////////////////////////////		
	//  WriteFileIndex 
	//    Функция записи в индексный файл!
	//
	//
	//////////////////////////////////////////////////////////////
	public function WriteFileIndex()
	{		
	    $DB = new Class_BD;
		// индексный файл статитики
		$NameFile = 'index.php'; 
		// контроллер индексного файла с гет параметрами
		$filecindex = 'cindex.php';
		$error = false;
		if (file_exists($filecindex))
		{
		   $this->ReadFile($filecindex);
		   /*$handle_cindex = fopen($filecindex, 'r');
		   $controller_index = fread($handle_cindex, filesize($filecindex));*/
		} else $error = true;   

		if (!$error)
		{
		  if (file_exists($NameFile)) unlink($NameFile);
		  $handle_index = fopen($NameFile, 'w+');			  
		  $text = '';
		  $text.= $controller_index;
		  $count_res = $this->DB->count_rec('pages', 'where id = 1'); 
		  $res = $this->DB->select('pages', Array('*'), 'id = 1');
		  if ($count_res > 0)
		  {
		    foreach ($res as $fields)
		      $text.= $fields['text_page'];
		  }                    		  
		  fwrite($handle_index, $text);		  
		  return true;
		}
		else return false;
		unset($DB);
		
		//fclose($handle_cindex);	
	}
	
	//////////////////////////////////////////////////////
	//  CreateFile функция создания файла
	//    path_name_file - путь доступа создаваемого файла
	//    text_file - текст файла
	//
	//////////////////////////////////////////////////////
	public function CreateFile($path_name_file, $text_file) {
	   	if (file_exists($path_name_file))
			return false;
		else{
          $handle = fopen($path_name_file, 'w+');
		  fwrite($handle, $text_file);
          fclose($handle);
          return $path_name_file;  		  
        }		
	}	
	
	//////////////////////////////////////////////////////
	//  ScanDirCatalog функция поиска каталогов и исключений 
	//
	//   Folder - родитель каталог поиска 
	//
	//////////////////////////////////////////////////////
	public function ScanDirCatalog($Folder) {
        $data = $this->ScanDirFolder($Folder);
		$newdata = Array();
		$except = Array();
		foreach ($data[2] as $value) {
			if (strpos($value, '.') > 0) {
				$except[] = $value;
			} else 
				$newdata[] = $value;					
		}			
		
		return Array('data' => $newdata, 'except' => $except);
    }	
	
	//////////////////////////////////////////////////////
	//   ScanDirThemeFilter($Folder)
	//   Функция сканирования папок темы
	//   $Folder - имя папки сканирования
	//   $Answer - ответ массив имен файлов
	//////////////////////////////////////////////////////		
	public function ScanDirThemeFilter($Folder)
	{
	    function Sub_str($value, $format)
        {            
			$ExceptionFiles = Array('main.php','head.php','footer.php','body.php');            
			if ((isset($format)) && (!empty($format)))
			{			   
               $pos = -1;
			   if (!in_array($value, $ExceptionFiles)) 
			   {
			     $pos = strpos($value, $format);
			     if ($pos > 0) return true;
			   } 
			   else return false;
            } else            			
			    return false;
        }		
		
	    $Answer = Array();
		if ((empty($Folder))&&(empty($this->Path))) 
		 if ($this->ConfigConnect->localServer) 
	       $this->Path = '/core/views/themes/default/';
	     else 
		   $this->Path = 'core/views/themes/default/';	 
		else
          /*
		   if ($this->ConfigConnect->localServer) 
	         $this->Path = '/core/views/themes/'.$Folder.'/';
		   else */
	       
		   $this->Path = 'core/views/themes/'.$Folder.'/';			  
		 
		   if ($handle = opendir($this->Path)) {          		            
             while (false !== ($file = readdir($handle))) { 
			   if (($file !== '..') && ($file !== '.'))
			     if (Sub_str($file, '.php'))   
                   $Answer[] = $file;
              }			  
		      closedir($handle); 		  		
           }
		
		sort($Answer);
        return $Answer;		
	}
		
	//////////////////////////////////////////////////////////////////
	//    RecreateUrlPages - восстановление файлов в базе данных
	//////////////////////////////////////////////////////////////////
	public function RecreateUrlPages()
	{
	    $this->Path = $this->ConfigConnect->Name_Theme;		
		if (empty($this->Path)) $this->Path = 'default';
		ScanDirThemeFilter($Folder);		
	}

    /////////////////////////////////////////////////////////////////
	//  GenerateFolderUser - генератор папки личной папки 
	//
	//     folder - каталог для создания личной территории юзера
	//     directory - директория доступа к папке юзера
	//     mode - уровень прав каталога
    //	
	/////////////////////////////////////////////////////////////////
	public function GenerateFolderUser($ID, 
									   $directory = 'upload/users/', 
	                                   $mode = '0777', 
									   $generate_folder = true) {       
	   if ((!$directory) || (empty($directory))) 
		   $directory = 'upload/users/';
	      
       $Obj_Str = new Class_String;
	   if ($generate_folder)
	     $ID_Code = $Obj_Str->GeneratorIDUser($ID, 20, true);
	   else 
	     $ID_Code = $ID;
		 
	   $path = $directory.$ID_Code."/";
	   
	   if (file_exists($path))
         return $ID_Code;
	   else { 
	   
          if (mkdir($path, $mode, true))
         	 return $ID_Code;
		  else 
             return false;		  
	   }	   
    }
 
    public function __destruct()
    {
      unset($this->TagList);
	  unset($this->Selected_Item);
	  unset($this->MaxSizeFile);
	  unset($this->MaxAmountFiles);
	  unset($this->Path);
	  unset($this->ConfigConnect);
    }  	
}
?>