<?
include_once('Class_Config.php');
include_once('Class_BD.php');
include_once('Class_String.php');
include_once('Class_Paggination.php');
include_once('SendMailSmtpClass.php');
include_once('Class_Files.php');
include_once('Class_IP.php');
include_once('Class_Codes.php');
include_once('Class_Component.php');

  /////////////////////////////////////////////////////////
  // Класс контроллер-компонентов
  //   
  //   Class_Controller - класс связующее-звено - соединители контроллеров системы и внедряющие компоненты html
  //
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
  /////////////////////////////////////////////////////////

Class Class_Controller
{
    private $BD_Obj; 
	private $Conf_Obj;
    private $Files;	
    private $Strings; 
	public $Codes;
	
	
	function __construct()
    {
	    $this->BD_Obj = new Class_BD;
		$this->Conf_Obj = new Class_Config;
		$this->Paggi = new Class_Paggination;	
		$this->Files = new Class_Files; 
        $this->Codes = new Class_Codes; 
        $this->Strings = new Class_String; 		
	}
	
	////////////////////////////////////////////////////////////
	// Компонент отправки сообщений по почте
	//   $email - получатель сообщения
	//   $title - заголовок
	//   $text - текст письма
	//   $sender - отправитель сообщения
	////////////////////////////////////////////////////////////
	public function SendMail($email, $title, $text, $sender)
	{
	    if ((!empty($email)) && (!empty($title)) && (!empty($text)) && (!empty($sender)))		   
		   {   
               $headers = "From: $sender" . "\r\n" .
                          "Reply-To: $sender" . "\r\n" .
                          "X-Mailer: PHP/" . phpversion();
               mail($email, $title, $text, $headers);
			   return true;
		   }
           else return false;
	}
	
	///////////////////////////////////////////////////////////
	// Компонент отправки сообщений через защищенные протоколы SMTP
	//   обход проверки DKIM и SPF -> Проверка DMARC
	//
	//
	///////////////////////////////////////////////////////////
	public function SendMailSMTP($reader, $pwd)
	{
	   $mailSMTP = new SendMailSmtpClass('ваш логин на Gmail', 'пароль', 'хост', 'имя отправителя');
       $result = $mailSMTP->send('Кому письмо', 'Тема письма', 'Текст письма', 'Заголовки письма');	   
	}
		
	public function TestClass()
	{
	   return ' Ответ от класса Class_Controller';
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

    private function getRoot()
    {
	    $pos = -1;
        $directory = $this->getUrl(true);
        $pos = strrpos($directory, '/', 0);		
		if ($pos > 0) 
		  return substr($directory, 0, $pos+1);	
		else
          return '';		
    }    
		
    ////////// Компонент IP_Reader ///////////////////////////////////////////
	// Запись информации об пользователе, который смотрит веб-страницу
	//////////////////////////////////////////////////////////////////////////
	public function IPReader() {
       $this->Obj_IP = new Class_IP;	
	   return $this->Obj_IP->add_logs_ip(false);	    
	}

    ////////// Компонент SimpleViewIPs ///////////////////////////////////////////
	// Компонент вывода ip-адресов клиентов, смотрящих веб-страницу
	//
	// date_begin - Начало периода
	// date_end - Конец периода
	//
	//////////////////////////////////////////////////////////////////////////	
	public function SimpleViewIPs($date_begin = '', $date_end = '') {
	   $this->Obj_IP = new Class_IP;	   
	   return $this->Obj_IP->see_logs_ip($date_begin, $date_end);	    	 	   
	}
	
    ////////// Компонент ViewIPs ///////////////////////////////////////////
	// Компонент вывода ip-адресов клиентов, смотрящих веб-страницу
	//
	// current_page - текущая GET-страница
	// $amount - количество записей на странице
	// $paggi_where - адрес паггинатора
	// $where_dp - условия выбора данных
	// sort = сортировка данных
	//
	//////////////////////////////////////////////////////////////////////////	
	public function ViewIPs($current_page = 1, $amount = 5, 
	                    $paggi_where = 'index.php?url=stran&page=', 
						$where_dp = '`del_rec` = 0', $sort = 'time_look') 
	{
	   $Obj_IP = new Class_IP;
	   $where = $where_dp;	   
	   
	   $Massive = Array();
	   if (empty($current_page) && ($current_page)) $current_page = 1;
	    $Massive['paggi'] = ''; 
	    if (is_integer($current_page))
	    {
	      //if (($current_page) && is_integer($current_page)) $Limit = '';		  	      
		  $Massive['amount'] = $this->BD_Obj->count_rec('clicker', 'Where ('.$where.')');
	      //$this->Paggi->Set($first, $amount, $Massive['amount']);	   
		  $this->Paggi->SetfromPage($current_page, $amount, $Massive['amount']);	   
	      $end = (int)$this->Paggi->start_blog + (int)$amount;	   
	      if (is_integer($amount)) $Limit = "".$this->Paggi->start_blog.", $amount";	    	            		 	
		  
		  $Limit_int = $this->Paggi->Paggination($current_page);	      
		  $Massive['paggi'] = $this->Paggi->Component_Paggination($paggi_where, $current_page, true, true, 5);
	      if (!empty($Limit_int)) $Limit = $Limit_int;
	      $Massive['data'] = $this->BD_Obj->select('clicker', Array('*'), $where, $sort, false, $Limit);		   			
	    } else {	   
	  	  $Massive['amount'] = $this->BD_Obj->count_rec('clicker' , $where);  
          $Massive['data'] = $this->BD_Obj->select('clicker', Array('*'), $where, '', false);			  
	    }
	   
       $Massive['paggi_where'] = $paggi_where;	   
       $Massive['where'] = $where;	   
	   
	   return $Massive; 
	}
   	  	   	   
		
    ///////// Компонент (ListFace) Лента лиц ///////////////////////////////////////////
	//
	//   pay_face - вывод пользователей платных / бесплатных - true / false 
	//   count_face - количество выводимых в ленте пользователей
	//   text_face - текст сообщение пользователя
	//
	/////////////////////////////////////////////////////////////////////////
	public function ListFace($pay_face = false, $count_face = 10, $text_face = true) {
       $Massive = Array();
	   if ($count_face > 0) {
		   $Limit = "LIMIT 1, $count_face";		   
		   if ($pay_face) 
			  $Where = " Where (pay_user = 1)";		
		   else 
              $Where = "";   
           
           $Massive['data'] = $this->BD_Obj->select('users_face', Array('*'), $Where, true, false, $Limit);   	   
	   } 
          return $Massive;	   
	}

    ///////// Компонент (AjaxConnectListBoxs) Ajax-связанности списков ///////////////////////////////////////////
	//   Компонент ajax-обновления данных дочернего списка путем обновления родительского списка 
	//   ParentList - Родительский список обновления данных
	//   FieldParent - Родительское поле источник
	//   ChildrenList - Дочерний список обновления данных   	
	//   TableChildren - Таблица дочернего списка
	//   FieldChildren - Дочернее поле для вывода данных
	//   WhereChildrenList - Условия формирования дочернего списка
	//
	/////////////////////////////////////////////////////////////////////////
	
	public function AjaxConnectListBoxs($ParentList,
                                        $FieldParent,	
	                                    $ChildrenList, 
										$TableChildren, 
										$FieldChildren
										) {
											
       if ((!empty($ParentList)) && (!empty($ChildrenList)) && (!empty($TableChildren))) {
          ?>
         <script>
         $(document).ready(function(){
	       function UploadList(){
			  var $ListBoxChirdren = $("#<?=$ChildrenList;?>");	  
			  var $ListBoxParent = $("#<?=$ParentList;?>");
			  var $url_ajax = 'core/controllers/admin/ajax/load_listbox.php';
			  
			  console.log("Create ListBox... <?=$ChildrenList?>");			  
              $.ajax({
                  type: 'POST',
                  url: $url_ajax,
				  data: 'ParentVal='+$ListBoxParent.val()+'&ParentField=<?=$FieldParent?>&ChildrenTable=<?=$TableChildren?>&ChildrenList=<?=$ChildrenList?>&ChildrenField=<?=$FieldChildren?>',
			      success: function(data){
					 console.log('View ListBoxData '+ data);
					 $ListBoxChirdren.html(data);
				  },                 
				  error: function(data) {
					 console.log('Error: '+ data);  
				  }	  
			    });						
			}
			
			$("#<?=$ParentList;?>").click(function() { 			    
				UploadList();
			  });			
          });	   
          </script>
          <?		  
       } else 
          return false;		   
    }	
	
	////////// Компонент (SpinEdit)   ////////////////////////////
    //
    //   Компонент числовых значений SpinEdit 
	//     $Value_default - дефолтное значение 
	//     $Id - Id компонента, если значение не указано, то генерируется случайный Id 
	//     $ClassCombobox - Подключение классы стилей css.
	//     $EditMode - Режим редактирования 
    //
    //////////////////////////////////////////////////////////////////////////	
	public function SpinEdit($Value_default, 
	                         $Id = '', 
							 $ClassCombobox = 'input_text2', 
							 $EditMode = false) {
	   ?>
	   <script>
	      $(document).ready(function(){
			  $(".arrow<?=$Id;?>").click(function() { 			    
				  var $this_elem = $(this);
				  var $input_elem = $("#<?=$Id?>"); 
				  var $id_elem = $input_elem.val();
				  
				  if ($this_elem.hasClass('up_arrow')) 
				    $id_elem++;					
					
				  if ($this_elem.hasClass('bt_arrow')) {
				    $id_elem--;
					if ($id_elem < 1) $id_elem = 1;
				  }	
				  
				  $input_elem.val($id_elem);
				  $input_elem.attr('value', $id_elem);
			  });	  
		  });
	   </script>
	   <input type="text" style="float: left; margin-right: 0px;" class="<?=$ClassCombobox;?>" id="<?=$Id;?>" name="<?=$Id;?>" <? if ($EditMode) echo " readonly "; ?>value="<?=$Value_default;?>"/>
	   <div>
	      <a><img src="img/arrow_top.png" id="arrow_up_id_<?=$Id;?>" name="arrow_up_id_<?=$Id;?>" class="arrow<?=$Id;?> up_arrow" width="28px" height="28px"/></a>
		  <a><img src="img/arrow_bottom.png" id="arrow_bt_id_<?=$Id;?>" name="arrow_bt_id_<?=$Id;?>" class="arrow<?=$Id;?> bt_arrow" width="28px" height="28px"/></a>
	   </div>	   
	   <?
	}
		
	////////// Компонент (ComboboxString)   ////////////////////////////
    //
    //   Компонент Формирования Статических Combobox 
	//     $ListArray - массив значений для списка
	//     $CheckItem - Выбранный пункт из списка
	//     $Id - Id компонента, если значение не указано, то генерируется случайный Id 
	//     $ClassCombobox - Подключение классы стилей css.
	//     $Param - особые параметры 
    //
    //////////////////////////////////////////////////////////////////////////	
	public function ComboboxString($ListArray, 
	                               $CheckItem, 
								   $Id = '', 
								   $ClassCombobox = 'input_text2',
								   $Param = "") {
        $Obj_Combobox = new Combobox;
		$Obj_Combobox->setValue('Class_Component',$ClassCombobox);
		$Obj_Combobox->setValue('Field_Combobox', 'id');
		$Obj_Combobox->Param = $Param;
		
		if (($Id) && (!empty($Id))) {
			$Obj_Combobox->setValue('ID', $Id);
			$Obj_Combobox->setValue('Name', $Id);
		}	
		
		if (empty($CheckItem))
		  $Obj_Combobox->setValue('Checked_item', $CheckItem);
		else  
		  if (in_array($CheckItem, $ListArray))
		    $Obj_Combobox->setValue('Checked_item', $CheckItem);
          else
            if (array_key_exists($CheckItem, $ListArray)) 
              $Obj_Combobox->setValue('Checked_item', $ListArray[$CheckItem]);      
            		  		  		
		$Obj_Combobox->WhereCombobox = '';
		if (is_Array($ListArray)) {       
		  $Obj_Combobox->setValue('TableName', $ListArray);
	      return $Obj_Combobox->PrintComponent(''); 		           
		} else 
		  return false;
    }	
	
	////////// Компонент (ListboxString)   ////////////////////////////
    //
    //   Компонент Формирования Статических Listbox 
	//     $ListArray - массив значений для списка
	//     $CheckItem - Выбранный пункт из списка
	//     $Id - Id компонента, если значение не указано, то генерируется случайный Id 
	//     $ClassCombobox - Подключение классы стилей css.
	//     $Count_Items - Количество пунктов в списке
	//     $Multiple - Количество выводимых пунктов.
    //     Distinct =- уникальность поля
	//     Debug - отладчик кода
    //////////////////////////////////////////////////////////////////////////	
	public function ListboxString($ListArray, 
	                               $CheckItem, 
								   $Id = '', 
								   $Count_Items = 7, 
								   $Multiple = false, 
								   $ClassCombobox = 'input_text2', 
								   $Distinct = true,
							       $Debug = false,
								   $Param = ""
							    ) {
									   
        $Obj_Combobox = new Combobox;
		$Obj_Combobox->Debug = $Debug;
		$Obj_Combobox->Param = $Param;
		$Obj_Combobox->setValue('Class_Component',$ClassCombobox);
		$Obj_Combobox->setValue('Field_Combobox', 'id');
		if (($Id) && (!empty($Id))) {
			$Obj_Combobox->setValue('ID', $Id);
		    $Obj_Combobox->setValue('Name', $Id);	
		}

		if (in_array($CheckItem, $ListArray))
		  $Obj_Combobox->setValue('Checked_item', $CheckItem);
        else
          if (array_key_exists($CheckItem, $ListArray)) {
            $Obj_Combobox->setValue('Checked_item', $ListArray[$CheckItem]);      
        }		  
		$Obj_Combobox->Distinct = $Distinct;		
        $Obj_Combobox->WhereCombobox = '';
		
		if (is_Array($ListArray)) {       
		  $Obj_Combobox->setValue('TableName', $ListArray);
	      return $Obj_Combobox->PrintComponent('', $Count_Items, $Multiple); 		           
		} else 
		  return false;
    }	

	////////// Компонент (ComboboxBD)   ////////////////////////////
    //
    //   Компонент Формирования данных из БД в Combobox 
	//     1. Recource - таблица, либо вьюшка для формирования данных ComboboxBD
	//     2. Field_print - поле для вывода данных из таблицы
	//     3. CheckItem - Значение-Выбранный пункт из списка
	//     4. Id - Id компонента, если значение не указано, то генерируется случайный Id 
	//     5. Where - условия набора табличных данных
	//     6. ClassCombobox - Подключение классы стилей css.
	//     7. Default_null - булевое значение (по умолчанию пусто или нет?)
	//     8. Sorting - сортировка поля (по умолчанию по имени поля сортировка, либо по условию программиста)
    //     9. Distinct - уникальность значений 
	//	   10. Debug - отладчик кода
	//     11. Param - параметр-значение для компонента
    //////////////////////////////////////////////////////////////////////////	
	public function ComboboxBD($Recource, 
	                           $Field_print,							   
							   $CheckItem, 
							   $Id = '',
							   $Where = '',							   
							   $ClassCombobox = 'input_text2',
							   $Default_null = false,
							   $Sorting = false,
							   $Distinct = true,
							   $Debug = false, 
							   $Param = ""
							  ) {
        $Obj_Combobox = new Combobox;		
		$Obj_Combobox->Debug = $Debug;
		$Obj_Combobox->Param = $Param;
		$Obj_Combobox->Null_current = $Default_null; 
		
		$Obj_Combobox->setValue('Class_Component',$ClassCombobox);
		$Obj_Combobox->setValue('Field_Combobox', $Field_print);
		if (($Id) && (!empty($Id))) {
			$Obj_Combobox->setValue('ID', $Id);
			$Obj_Combobox->setValue('Name', $Id);
		}
		
		$Obj_Combobox->Distinct = $Distinct;
		$Obj_Combobox->setValue('Checked_item', $CheckItem);
		if ($Sorting == false)
		  $Obj_Combobox->setValue('Sorted', $Field_print);
        else 
		  $Obj_Combobox->setValue('Sorted', $Sorting);
		  
		$Obj_Combobox->setValue('Ordered', $Field_print);
		
        $Obj_Combobox->WhereCombobox = $Where; 
		
		$Obj_Combobox->setValue('TableName', $Recource);
	    return $Obj_Combobox->PrintComponent(''); 		           
		
    }	
	
	////////// Компонент (GridBD)   ////////////////////////////
    //
    //   Компонент Формирования данных из БД в Table (View) 
	//     1. Recource - таблица, либо вьюшка для формирования данных Table
	//     2. Fields_print - поля для вывода данных из таблицы
	//     3. Id - Id компонента, если значение не указано, то генерируется случайный Id 
	//     4. ClassGrid - Подключение классы стилей css.
	//     5. ClassStyle - Стили таблицы 
	//     6. TitleKey - Заголовки key массива Fields_print (булеан)
	//     7. Where - Условия набора табличных данных
	//     8. scroll_width - скроллинг в ширину (зарезервированы на будущее)
	//     9. scroll_height - скроллинг в высоту (зарезервированы на будущее)
	//     10. sort_fields - сортировка по таблице (зарезервированы на будущее)
	//     11. placeholders - всплывающие нотисы и сообщения (зарезервированы на будущее)
	//     
    //
    //////////////////////////////////////////////////////////////////////////	
	public function GridBD($Recource, 
	                       $Fields_print,							   							
						   $Id = '',
						   $ClassGrid,
						   $StyleGrid,
						   $TitleKey = true, 
						   $Where = '') {
		if (($Recource) && (!empty($Recource))) {
           $Colvo = $this->BD_Obj->count_rec($Recource, $Where); 
		   
		   ?>
		      <style>
			     .disvisi {
				    display: none;
				 }
				 
				 .visi {
				   display: block;
				 }
			  </style>
              <script>
                 $(document).ready(
	                function() 
	                { 
                       function RefreshDBGrid() {
				            var json_fields = <?=json_encode($Fields_print)?>;
							var js_fields = JSON.parse('<?=json_encode($Fields_print)?>');
            	            $.ajax({
                                  type: "POST",
                                  url: "core/controllers/admin/ajax/griddb_select_records.php",
				                  data: "table=<?=$Recource?>&fields="+JSON.stringify(json_fields)+"&id_table=<?=base64_encode($Id)?>"+
								      "&class_table=<?=base64_encode($ClassGrid)?>&style_table=<?=base64_encode($StyleGrid)?>"+
									  "&where_select=<?=base64_encode($Where)?>&titlekey=<?=$TitleKey?>",
			                      success: function(data) {					                 
					                 console.log('success editing data table ');
					                 $("#block_<?=$Id?>").html(data);                        									 
                                  },                 
				                  error: function(data) {
					                 console.log('Error: '+ data);  
				                  }	  
				           });
					   }
					   
					   $(document).on('click', '.edit_grid', function() {
			                var OnButton = $(this);
                            var OnIDClass = OnButton.attr('id');
                            var OnParam = OnButton.attr("param1");
                            var ve = ".visible_edit_<?=$Id?>_"+OnParam;
							
							console.log('This is '+OnIDClass+'. Yes? of couse. '+OnParam);	
                            $(ve).each(function() {
							  var _this = $(this);
							  if ($(_this).hasClass('disvisi')) {
							    $(_this).removeClass('disvisi');
							    console.log("Visible editing mode");
							  } else {
							    $(_this).addClass('disvisi');	
							    console.log("Disable mode editing");
							  }                
                           });							
			           }); 

			           $(document).on('click', '.del_grid', function() {
			                var OnButton = $(this);
                            var OnIDClass = OnButton.attr('id');
                            var OnParam = OnButton.attr("param1");
   			    
							console.log('This is '+OnIDClass+'. Param='+OnParam);	
                            
							$.get("core/controllers/admin/ajax/griddb_delete_record.php", 
					          {id_record: OnParam, table: "<?=$Recource;?>"}
						    ).done(
					          function(data) {
                                console.log( "Record success delete: " + data);
								RefreshDBGrid();
                             });
                            							 
			            }); 
					   
					    $(document).on('click', '.refreshDBGrid_<?=$Id?>', function() {	
						   var _this_ = $(this)
						   RefreshDBGrid();
						});
						
					   //$('.save').click(
					   //   function() {
					   $(document).on('click', '.save', function() {			           
                            var _this_ = $(this);
                            var param_id = _this_.attr('param1');
							var visible_IT = [];
							var TEdit = ".editing_text"+param_id;
							var json_fields = <?=json_encode($Fields_print)?>;
							var js_fields = JSON.parse('<?=json_encode($Fields_print)?>');
							
							$(TEdit).each(
                              function() {
							     var _this_ = $(this);
								 var IdTEdit = _this_.attr('id');
								 visible_IT.push($('#'+IdTEdit).val());
							  }
                            );	
													
							console.log(json_fields);	
						    console.log('Click button SAVE');
							
                            $.ajax({
                                type: "POST",
                                url: "core/controllers/admin/ajax/griddb_save_record_.php",
				                data: "table=<?=$Recource?>&fields="+JSON.stringify(json_fields)+"&id_record="+param_id+"&id_table=<?=base64_encode($Id)?>"+
								      "&class_table=<?=base64_encode($ClassGrid)?>&style_table=<?=base64_encode($StyleGrid)?>"+
									  "&data="+JSON.stringify(visible_IT)+"&where_select=<?=base64_encode($Where)?>&titlekey=<?=$TitleKey?>",
			                    success: function(data) {					                 
					                 console.log('success editing data table ');
					                 RefreshDBGrid();
									 //$("#block_<?=$Id?>").html(data);
                                     									 
                                },                 
				                error: function(data) {
					                 console.log('Error: '+ data);  
				                }	  
				            });	
						  });
                   });					   
             </script>
             <div id="block_<?=$Id?>">			 
			  <table id="<?=$Id;?>" name="<?=$Id?>" class="<?=$ClassGrid?>" style="<?=$StyleGrid?>">
			    <tr>
				<th>Edit</th>
				<th>Del</th>
		        <?
		         foreach ($Fields_print as $key => $title) {
			    ?>
	             <th><?if ($TitleKey) echo $key; else echo $title;?></th>		   
			     <?
			    } ?>
			    </tr>
			    <?
				$Data = $this->BD_Obj->select($Recource, $Fields_print, $Where);
				//if ($Data) {
				  $i = 0;  
				if ($Colvo > 0) {
				  foreach ($Data as $value) {
				    $i++;
				  
					?>
				    <tr>
					 <td class="td_grid"><a class="edit_grid" id="<?echo $Id.'_edit_'.$value['id'];?>" param1="<?=$value['id']?>">edit</a>
					  <div class="disvisi visible_edit visible_edit_<?=$Id.'_'.$value['id']?>">
	    				<div param1="<?=$value['id']?>" class="push_button1 red1 save save_<?echo $Id.'_'.$value['id']?>" style="width: 45px; height: 40px">Save</div>	
					  </div>
					 </td>
					 <td class="td_grid"><a class="del_grid" id="<?echo $Id.'_del_'.$value['id'];?>" param1="<?=$value['id']?>">del</a></td>			 
				    <?
                    $i = 0;
					foreach ($Fields_print as $Key_F => $tt) {
					   $i++;
					?>
                       <td class="td_grid">
					    <p class="text text_overflow <?echo $Id.'_text_'.$value['id']?>"><?=$value[$tt]?></p>
					    <div class="disvisi visible_edit visible_edit_<?=$Id.'_'.$value['id']?>">
						  <input type="text" class="editing_text<?=$value['id']?>" id="etext_<?echo $Id.'_'.$tt.'_'.$value['id']?>" value="<?=$value[$tt]?>" style=""/> 
        				</div>						  
					   </td>            					   
                    <?					
					}	
					?>
					</tr>
				<?} ?>			
					<?
				} else {?>
                    <tr>
					 <td><a class="edit_grid">edit</a></td>
					 <td><a class="del_grid">del</a></td>			 
				     <? foreach ($Fields_print as $tt) {?>
					   <td>-</td>
					 <? } ?>
					</tr>
				<? } ?>
				 </table> 
				</div> 
				<?  
               //} else return "Error. Get data no success!";				
		     
        } else {
       return "Error. Table no created!";        		
	}	
   }	

	////////// Компонент (ListboxBD)   ////////////////////////////
    //
    //   Компонент Формирования данных из БД в Listbox 
	//     1. Recource - таблица, либо вьюшка для формирования данных ListboxBD
	//     2. Field_print - поле для вывода данных из таблицы
	//     3. CheckItem - Выбранный пункт из списка
	//     4. Id - Id компонента, если значение не указано, то генерируется случайный Id 
	//     5. Count_Items - Количество пунктов в списке
	//     6. Multiple - Количество выводимых пунктов.	
	//     7. Where - Условия набора табличных данных
    //     8. ClassCombobox - Подключение классы стилей css.
	//     
    //
    //////////////////////////////////////////////////////////////////////////	
	public function ListboxBD($Recource, 
	                           $Field_print,							   
							   $CheckItem, 
							   $Id = '',
							   $Count_Items = 7, 
							   $Multiple = false, 
							   $Where = '', 
							   $ClassCombobox = 'input_text2',
							   $Param = "") {
        
		$Obj_Combobox = new Combobox;
		$Obj_Combobox->setValue('Class_Component',$ClassCombobox);
		$Obj_Combobox->setValue('Field_Combobox', $Field_print);
		$Obj_Combobox->Param = $Param;
		
		if (($Id) && (!empty($Id))) {
			$Obj_Combobox->setValue('ID', $Id);
		    $Obj_Combobox->setValue('Name', $Id);
		}
		
		$Obj_Combobox->Distinct = true;
		
		$Obj_Combobox->setValue('Checked_item', $CheckItem);
        $Obj_Combobox->WhereCombobox = $Where;       
		$Obj_Combobox->setValue('Sorted', $Field_print);
		$Obj_Combobox->setValue('Ordered', $Field_print);
		
		$Obj_Combobox->setValue('TableName', $Recource);
		
		return $Obj_Combobox->PrintComponent('', $Count_Items, $Multiple); 		           
    }	
	
	////////// Компонент (Slider) слайдер ////////////////////////////////////////
	//  
	//  Компонент формирующий слайдер картинок из разных ресурсовю Библиотека slick.
	//   1. Recource - каталог, либо массив картинок  
	//
	//////////////////////////////////////////////////////////////////////////////
	public function Slider($Recource = Array()) {
       if (!empty($Recource)) {?>
	   	<link rel="stylesheet" type="text/css" href="css/slick/slick.css">
        <link rel="stylesheet" type="text/css" href="css/slick/slick-theme.css">
	   <?
		  if (is_Array($Recource)) {?>
		     <div class="box">
		       <section class="lazy slider" data-sizes="50vw">
		       <?
                 foreach ($Recource as $key => $value) {
               ?>
 			   <div>
				  <h3><?=$value['title']?></h3>
		    	  <h3><?echo $value['text']?></h3>
				  <img class="Simg" src="<?=$value['url_img'];?>" style="width: 100%;" alt="<?=$value['title']; ?>">
               </div>
			   <? } ?>			  
              </section>
           </div> 			  
		  <?} else {
             if ($Recource) {
				 
			 }	 
          }	?>
		  <script src="js/jquery.js"></script>
          <script src="js/slick/slick.min.js" type="text/javascript" charset="utf-8"></script>
          <script type="text/javascript">
                $(document).on('ready', function() {
	               $(".regular").slick({
                       dots: true,
                       infinite: true,
                       slidesToShow: 5,
                       slidesToScroll: 7
                    });
	  
	            $(".lazy").slick({
                   lazyLoad: 'ondemand', // ondemand progressive anticipated
                    infinite: true
                });
	          });	
         </script>	
         <?
	   }		  
    }	

	////////// Компонент (SimpleSlider) слайдер ////////////////////////////////////////
	//  
	//  Компонент формирующий слайдер картинок из разных ресурсовю Библиотека slick.
	//   1. Recource - каталог, либо массив картинок  
	//
	//////////////////////////////////////////////////////////////////////////////
	public function SimpleSlider($Recource = Array(), $Debug = false) {
       if (!empty($Recource)) {?>
	   	<link rel="stylesheet" type="text/css" href="css/slick/slick.css">
        <link rel="stylesheet" type="text/css" href="css/slick/slick-theme.css">
	   <? if ($Debug == true) {
	        print_r($Recource); 
	      } else {
		    if (is_array($Recource)) {
			   if (count($Recource) > 0) 
			   {?>
		     <div class="box">
		       <section class="lazy slider" data-sizes="50vw">
		       <?
                 foreach ($Recource as $key => $value) {
               ?>
 			   <div>
				  <img class="Simg" src="<?=$value;?>" style="width: 100%;" alt="<?=$value; ?>">
               </div>
			   <? } ?>			  
              </section>
             </div> 			  
		     <?}
			 } else {
             if ($Recource) {
				 
			 }	 
          }	?>
		  <script src="js/jquery.js"></script>
          <script src="js/slick/slick.min.js" type="text/javascript" charset="utf-8"></script>
          <script type="text/javascript">
                $(document).on('ready', function() {
	               $(".regular").slick({
                       dots: true,
                       infinite: true,
                       slidesToShow: 5,
                       slidesToScroll: 7
                    });
	  
	            $(".lazy").slick({
                   lazyLoad: 'ondemand', // ondemand progressive anticipated
                    infinite: true
                });
	          });	
         </script>	
         <?
	     }
	   }		  
    }	
	
	/////////////// Компонент (AdminSystem) Система управления контентом ////////////////////////
	//   Сенсорные технологии управления инлайновых редакторов.
	//   СТЫРЕНО... НЕ РАБОТАЕТ 
	//   user - логин компонента управления
	//   password - пароль компонента управления
	//
	/////////////////////////////////////////////////////////////////////////////////////////////
	public function AdminSystem($link_css = 'css/adminsystem.css', $inlineedtor = true) {
	    /*if ($_GET['delsession']) {
		   unset($_SESSION['admin']);
		   unset($_SESSION['admin_pwd']);
		   unset($_GET['delsession']);			   
		}*/

        //print_r($_SESSION['admin']);		
		//print_r($_SESSION['admin_pwd']);		
		?>
		 <link rel="stylesheet" type="text/css" href="<?=$link_css;?>"/>
		 <style>
		   div#editor {
              width: 81%;
              margin: auto;
              text-align: left;
           }
		   
		   .float_blocks {
			   float: left;
               margin: 10px;			   
		   }	   
		 </style>
		<? if ($inlineedtor) {?>
		   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
           <link rel="stylesheet" href="../../css/froala_editor.css">
           <link rel="stylesheet" href="../../css/froala_style.css">
           <link rel="stylesheet" href="../../css/plugins/code_view.css">
           <link rel="stylesheet" href="../../css/plugins/colors.css">
           <link rel="stylesheet" href="../../css/plugins/emoticons.css">
           <link rel="stylesheet" href="../../css/plugins/image_manager.css">
           <link rel="stylesheet" href="../../css/plugins/image.css">
           <link rel="stylesheet" href="../../css/plugins/line_breaker.css">
           <link rel="stylesheet" href="../../css/plugins/quick_insert.css">
           <link rel="stylesheet" href="../../css/plugins/table.css">
           <link rel="stylesheet" href="../../css/plugins/file.css">
           <link rel="stylesheet" href="../../css/plugins/char_counter.css">
           <link rel="stylesheet" href="../../css/plugins/video.css">
           <link rel="stylesheet" href="../../css/plugins/emoticons.css">
           <link rel="stylesheet" href="../../css/plugins/fullscreen.css">
           <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css">
           <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
           <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.js"></script>
           <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/mode/xml/xml.min.js"></script>
           <script type="text/javascript" src="../../js/froala_editor.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/align.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/code_beautifier.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/code_view.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/colors.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/emoticons.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/draggable.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/font_size.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/font_family.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/image.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/image_manager.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/line_breaker.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/quick_insert.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/link.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/lists.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/paragraph_format.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/paragraph_style.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/video.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/table.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/url.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/emoticons.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/file.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/entities.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/inline_style.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/save.min.js"></script>
           <script type="text/javascript" src="../../js/plugins/fullscreen.min.js"></script>
		<? }
		/*if ((!empty($_SESSION['admin'])) && (!empty($_SESSION['admin_pwd']))) {		   
		   $pwd = md5($this->Conf_Obj->Admin.$this->Conf_Obj->Pwd.$this->Conf_Obj->Hidekey);
		   if (($this->Conf_Obj->Admin == $_SESSION['admin']) && ($pwd == $_SESSION['admin_pwd'])) */ //&&		       
		       if ($_GET['editor'] == 'inline')
			   {				   
                  ?> 
				  <div id="AdminSystem" class="text-center">
				   <div class="Emblem float_blocks" style="float:left;">
                    <div class="CMS" style="margin-left: 30px;">
	                  <a class="title1" href="admin.php"><img class="simbol_system emblema" style="width:100px;" src="core/views/TheSolarWind.png"></a>
	                  <a class="" style="font-size: 9px!important" href="admin.php">CMS/Framework SolarWind <?=$this->Conf_Obj->Version; ?></a>
					  <div style="both: clear;"></div>
					  <a class="red1" style="margin-left: 24px; color: #FFF;" href="<?=$_SERVER['HTTP_REFERER'];?>" style="" href="">Выход из системы</a>
	                </div>				  				    				  
				   </div>
				   <h3 class="float_blocks">Инлайновый редактор системя</h3>
				  </div>
		          <div style="both: clear;"></div>
				  <section id="editor">
                    <div id='edit' style="margin-top: 30px;">
                  <?				                    
			   } /* else {?>
				  <p style='color: red'><? print_r(md5($user.$password.$this->Conf_Obj->Hidekey));?><br>
				  Error: Логин и пароль не соответствуют действительности!</p>
			<? }   */   	  			  
		//} ?> 
		          

		<?		
	}	
	
    ////////// Компонент (FormGoodPay) Приобретение услуги / товара  ////////////////////////////
    //
    //   Компонент формы услуги / товара для пересыла сообщения от пользователя к администратору по почте 
    //
	//   1. email - ящик отправителя сообщения через форму
	//   2. nameform - имя формы, которая подключается к компоненту. Дефолтное имя - form_goodpay.php. 
    //   Формы расположены в директории views/forms/ и в случае создании своих форм со стилями, 
	//   их можно располагать там же.	
    //   3. title_letter - заголовок письма.
	//
    /////////////////////////////////////////////////////////////////////////////////////////////	
	public function FormGoodPay($email, $nameform = 'form_goodpay.php', $title_letter = '') {
	    $_SESSION['Message'] ="";
		$_SESSION['style'] = "color: red";	
		
        if ((isset($_POST['key_hidden'])) && ($_POST['key_hidden'] == 'key12345')) {
		  if (md5($_POST['norobot']) == $_SESSION['randomnr2'])	{
			if (($_POST['name_client']) && (!empty($_POST['name_client']))&&
			    ($_POST['tel']) && (!empty($_POST['tel']))&&
				($_POST['email']) && (!empty($_POST['email']))&&
				($_POST['text']) && (!empty($_POST['text']))) 
			    {
				    $name_client = $_POST['name_client'];
			        $tel = $_POST['tel'];
			        $sender = $_POST['email'];
			        $text = $_POST['text'];
					
					if (empty($title_letter)) 
					   $title = "Обращение от ".$name_client." telephone: ".$tel;
				    else {
					   $title = $title_letter;
					   $text .= "<br> Меня зовут ".$name_client.". Связаться со мной можно по телефону: ".$tel;
                    }
					
					$this->SendMail($email, $title, $text, $sender);
					$_SESSION['Message'] = "Сообщение было успешно отправлено!";
					$_SESSION['style']= "color: green";					
			    } else
					$_SESSION['Message'] = "Ошибка: Не все обязательные поля заполнены!";
					
		  } else $_SESSION['Message'] = "Капча была введена неверно!"; 	
		} else { 
		  $_SESSION['style'] = "color: green";
		  $_SESSION['Message'] = "Введите свои данные, чтобы сообщение было прочитано!"; //"Ошибка: Ключ формы не обнаружен!"; 
		}
		$Cnf = new Class_Config;
		if ($Cnf->localServer)
	       $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/'.$Cnf->FolderRoot.'/core/views/forms/'.$nameform;
	    else	 
	       $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/core/views/forms/'.$nameform; 
		//echo $tpl_name;
		
        if(!empty($tpl_name) && file_exists($tpl_name)) {
            echo file_get_contents($tpl_name);
        } else $_SESSION['Message'] = "Ошибка: файл шаблона не найден!";

        echo "<p style='".$_SESSION['style']."'>".$_SESSION['Message']."</p>";
	}
	
    ////////// Компонент (Catalog_tree) Деревовидная структура каталогов и файлов ///////////////////
    //   1. Folder - каталог сканирования данных 
    //   2. Path - путь-директория сканирования данных
    /////////////////////////////////////////////////////////////////////////////////////////////////	  
    public function Catalog_tree($Folder, $Path) {

		$Data = $this->Files->ScanDirTheme($Folder, 
		                                   Array('head.php', 'main.php', 'body.php', 'footer.php'), 
		                                   $Path);
        ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<script type="text/javascript" src="js/enhance.js"></script>
        <script type="text/javascript" src="js/example.js"></script>		
		
		<!--<script type="text/javascript">
			// Запуск теста на поддержку браузером скриптов
			enhance({
				loadScripts: [
					'https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js',
					'js/jQuery.tree.js',
					'js/example.js'
				],
				loadStyles: ['css/enhanced.css'],
				forcePassText: ['Посмотреть с подключенными скриптами'],
				forceFailText: ['Посмотреть с выключенными скриптами']
			});   
	    </script>-->		
		<ul id="files">
		<?
		if ((!empty($Data)) && (is_Array($Data))) {
		   foreach ($Data as $key => $value) {
              ?>
                 <li><a href=""><?=$value;?></a>
				 <? if (!(strpos($value, '.') > 0)) {?>
				        <!--<ul>
						   <li>...</li>
						</ul>-->
				 <? } ?>
				 </li>
              <?			  
           }			   
		}
		?>
		</ul>
		<!--<script type="text/javascript" src="http://pcvector.net/templates/pcv/js/pcvector.js"></script>-->
		<?
		return $Data;         								   
	}	

	////////// Компонент (FormGoodPay_js) Валидация FormGoodPay /////////////////////////////////////
    //
	//   Компонент подключения валидации обратной связи (без параметров). Размещается
	//   в верхней части контента веб-проекта (в разделе head)
	//
	/////////////////////////////////////////////////////////////////////////////////////////////////
	public function FormGoodPay_js() {
		?>
		<script src="core/views/forms/validationForm_formgoodpay.js"></script>
		<?
	}	
    

    ////////// Компонент (FormRepairNewPwd) Новый пароль для пользователя ////////////////////////////
    //
    //   Компонент восстановления пользователя обязательные параметры новый пароль и его подтверждение
    //   Форма хранится в отдельном файле шаблона tpl. 
    //
	//   1. token - токен восстановления 
    //////////////////////////////////////////////////////////////////////////		
	public function FormRepairNewPwd($token) {
	   if ((($token) && (!empty($token)))) {
		   $Where = "(`session_token` = '".$token."')";
		   $WhereSelect = 'Where '.$Where;	       	
           $count_rec = $this->BD_Obj->count_rec('token' , $Where); 

           //if ($count_rec > 0) {
              $Data = $this->BD_Obj->select('token', Array('*'), $WhereSelect, true);
			  foreach ($Data as $Val) {
				  echo $Val['time_begin']; 
			  }	  
           //}		   
	   } 	   
	}	
		
   ////////// Компонент (FormRepairUser) Восстановления пользователя ////////////////////////////
    //
    //   Компонент восстановления пользователя обязательные параметры логин и пароль. 
    //   Параметры не вводятся через функцию. Они набираются через форму входа, которая будет храниться	 
    //   в отдельном файле шаблона tpl. 
    //
	//   1. email - почта восстановления
	//   2. nameform - шаблон формы.
    //////////////////////////////////////////////////////////////////////////	
	public function FormRepairUser($email='integralal@the-solarwind.ru', $nameform = 'form_repair.php') {
	   $Message = "";
	   $style= "color: red";
	   
	   if ((isset($_POST['key_hidden'])) && ($_POST['key_hidden'] == 'key_repair')) {
          if ((isset($_POST['email'])) && (!empty($_POST['email']))) {
			   $title = 'Восстановление пароля с портала';
			   $Data = $this->BD_Obj->select('seo', Array('*'), '', true); 			   
			   $count_rec = $this->BD_Obj->count_rec('seo' , ''); 
			   
			   if ($count_rec > 0) {
			      $title = $Data['data']['Description'];
			      $sender = $_POST['email'];			   
			      $token = rand(100, 999).rand(100, 999).rand(100, 999);
			   
			      $text = "От Вас поступило обращение о восстановлении пароля на портале ".$title;
			      $text.= "Перейдите по ссылке, чтобы посстановить свой пароль!";
			      $text.= "<a href='".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?url=new_password&token=".$token."'>Восстановление</a>";
			   
			      $this->SendMail($email, $title, $text, $sender);
				  
				  //echo  "<a href='".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?url=new_password&token=".$token."'>Восстановление</a>";
				  
				  $fields = Array('email', 'session_token');		   
				  $values = Array($sender, $token);				   
				  $Answer = $this->BD_Obj->insert('token', $fields, $values, false);				  
				  
				  $style= "font-size: 20px; color: #40f340;";
				  $Message = 'Письмо было успешно отправлено на почту!';  
			   } else 
				  $Message = 'Такой пользователь не найден в системе! Введите корректный пароль!';  
		  } else 
              $Message = 'Вы не написали ваш Email!';			  
	   } else {
		  $style= "font-size: 20px; color: #40f340;";
		  $Message = 'Напишите свой почтовый ящик, чтобы восстановить пароль!';  
	   }
	   
	   $tpl_name = "core/views/forms/form_repair.php"; 
	   $Cnf = new Class_Config;
	   
	   if ($Cnf->localServer) {
	     $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/'.$Cnf->FolderRoot.'/core/views/forms/'.$nameform;	
		 if (($token) && (!empty($token)))
		   $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/'.$Cnf->FolderRoot.'/core/views/forms/success_email.php';	
	   } else {	 
	     $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/core/views/forms/'.$nameform; 
	     if (($token) && (!empty($token)))
		   $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/core/views/forms/success_email.php';	
	   }
	   
	   if(!empty($tpl_name) && file_exists($tpl_name)) {
            echo file_get_contents($tpl_name);
       } else { 
	       $style= "color: red";
		   $Message = "Ошибка: файл шаблона не найден!";
       }
        echo "<p style='".$style."'>".$Message."</p>";
	}	   
	
    ////////// Компонент (FormRegistrationUser) Авторизации пользователя ////////////////////////////
    //
    //   Компонент авторизации пользователя обязательные параметры логин и пароль. 
    //   Параметры не вводятся через функцию. Они набираются через форму входа, которая будет храниться	 
    //   в отдельном файле шаблона tpl. 
    //
	//   1. nameform - шаблон формы.
    //////////////////////////////////////////////////////////////////////////	
	public function FormRegistrationUser($nameform = 'form_registration.php') {
	   $Message = "";
	   $style= "color: red";
	   
	   if ((isset($_POST['key_hidden'])) && ($_POST['key_hidden'] == 'key12345')) {
		 if (md5($_POST['norobot']) == $_SESSION['randomnr2'])	{
	        if ((isset($_POST['login'])) && (!empty($_POST['login'])) && 
	           (isset($_POST['password'])) && (!empty($_POST['password'])) && 
		       (isset($_POST['fam_user'])) && (!empty($_POST['fam_user'])) && 
			   (isset($_POST['name_user'])) && (!empty($_POST['name_user'])) && 
			   (isset($_POST['secname_user'])) && (!empty($_POST['secname_user'])) && 
		       (isset($_POST['sex'])) && (!empty($_POST['sex'])) && 
		       (isset($_POST['email'])) && (!empty($_POST['email']))) {
				   		   
		          $fields = Array('fam_user', 'name_user', 'secname_user', 
				                  'email', 'sex', 'login', 'pwd', 'address', 'telephone');		   
		          $solt = '$1$'.$this->Conf_Obj->Hidekey.'$';
				  $pwd = crypt($_POST['password'], $solt);
				  
				  if ($this->BD_Obj->count_rec('users', "where (`login` = '".$_POST['login']."')") > 0) {
					  $Message = "Пользователь с таким логином уже зарегистрирован в системе!";
				  }	else {  
				  
				    $values = Array($_POST['fam_user'], $_POST['name_user'], $_POST['secname_user'], 
				                  $_POST['email'], $_POST['sex'], $_POST['login'], 
								  $pwd, $_POST['address'], $_POST['telephone']);
								  
				    $Answer = $this->BD_Obj->insert('users', $fields, $values, false);

				    $id_user = $this->BD_Obj->get_field('users', 'id', "`login` = '".$_POST['login']."'");
				  
				    if (intval($id_user)) 
				      $folder = $this->Files->GenerateFolderUser($id_user, $id_user); 
					  
					  if ($folder == false){					  
				        $style= "color: red";
					    $Message = "Вы зарегистировались в системе, но каталог юзера не был создан!";
			
				      } else {
				        $this->BD_Obj->update('users', Array('folder_code'), Array($folder), "`login` = '".$_POST['login']."'");
				      
					    unset($_POST['login']); 
				        unset($_POST['email']); 
		                unset($_POST['password']);
		                unset($_POST['fam_user']);
                        unset($_POST['name_user']);
                        unset($_POST['secname_user']);				  
		                unset($_POST['sex']);
						unset($_POST['telephone']);
				        unset($_POST['address']);
				  
				        $style= "color: #40f340";
				        $Message = "Вы успешно зарегистрировались в системе! |";
				      }
				  }	
		       } else $Message = "Не все обязательные поля заполнены! "; 
				   
		 } else $Message = "Капча была введена неверно!"; 	
	   } else {
           $style= "color: #40f340";
		   $Message = "Введите свои данные, чтобы зарегистрироваться в системе!";
       }	   
        
	   $tpl_name = "core/views/forms/FormRegistrationUser.php"; 
	   $Cnf = new Class_Config;
	   if ($Cnf->localServer)
	       $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/'.$Cnf->FolderRoot.'/core/views/forms/'.$nameform;
	   else	 
	       $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/core/views/forms/'.$nameform;  
 
	   
	   if(!empty($tpl_name) && file_exists($tpl_name)) {
            include_once($tpl_name);
			//echo file_get_contents($tpl_name);
       } else $Message = "Ошибка: файл шаблона не найден!";

        echo "<p style='".$style."'>".$Message."</p>";
		//print_r($_SESSION);
	}	
	
	////////// Компонент (FormRegistrationUser_js) Валидация FormRegistrationUser /////////////////////////////////////
    //
	//   Компонент подключения валидации обратной связи (без параметров). Размещается
	//   в верхней части контента веб-проекта (в разделе head)
	//
	/////////////////////////////////////////////////////////////////////////////////////////////////
	public function FormRegistrationUser_js() {
		?>
		<script src="core/views/forms/validationFormFormregistrationuser.js"></script>
		<?
	}

    ////////// Компонент (FormAuthorizeUser) Авторизации пользователя ////////////////////////////
    //
    //   Компонент авторизации пользователя обязательные параметры логин и пароль. 
    //   Параметры не вводятся через функцию. Они набираются через форму входа, которая будет храниться	 
    //   в отдельном файле шаблона tpl. 
    //
	//   1. nameform - шаблон-формы.
    //////////////////////////////////////////////////////////////////////////	
	public function FormAuthorizeUser($nameform = 'form_authorize.php') {
	   ?>
	   <script src="core/views/forms/validationFormForm_authorize.js"></script>
	   <?
	   $Message = "";
	   $_SESSION['Message'] = 'Введите поля данных, чтобы авторизоваться!';
	   $result = false;
	   if (empty($_SESSION['style_message'])) $_SESSION['style_message'] = "color: red";
	   $tpl_name = "core/views/forms/form_authorize.php"; 
	   $Cnf = new Class_Config;
	   if ($Cnf->localServer)
	       $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/'.$Cnf->FolderRoot.'/core/views/forms/'.$nameform;
	   else	 
	       $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/core/views/forms/'.$nameform;  
		
	   if(!empty($tpl_name) && file_exists($tpl_name)) {
          echo file_get_contents($tpl_name);
       } else $_SESSION['Message'] = "Ошибка: файл шаблона не найден!";
	   // print_r($_SESSION);
	   echo "<p style='".$_SESSION['style_message']."'>".$_SESSION['Message']."</p>";
       return $result; //вернуть результат!			
	}
		
	//////////// Компонент FormAddMessageHead ///////////////////////////////////////////////
    // Компонент добавления сообщения-объявления располагается в невизуальной части выше тега html
    //   captha - режим капчи (true / false)
	//
    /////////////////////////////////////////////////////////////////////////////////////////
    public function FormAddMessageHead($captha = false) {
     if ($captha) {
		  if(md5($_POST['NewMessageKapcha']) != $_SESSION['randomnr2']) {
                 $_SESSION['Message'] = "!!!!!!!!Капча была введена неправильно!!! капча: ".md5($_POST['kapcha'])." код: ".$_SESSION['randomnr2']; 
	             $_SESSION['Style']='color: red';
				 $ok = false;
          } else {
             $_SESSION['Message'] = ""; 
	         $_SESSION['Style']='color: red';
          }		  
	   } else {
          $ok = true;
          $_SESSION['Message'] = ""; 
	      $_SESSION['Style']='color: red';
       }		  
	   
	  if ($ok) 
	   if (($_POST['id_page']) && (!empty($_POST['id_page']))) {
          if ((isset($_POST['name_page'])) && (!empty($_POST['name_page'])) &&
			  (isset($_POST['text_page'])) && (!empty($_POST['text_page']))
		     ){
				   ?>
				   <style>
                          .Error {
							   border: 5px solid red;  
						  }	  
                   </style>				   
				   <script>
	                $(document).ready(function(){
                        var Title = $('#name_page');
                        var Text = $('#text_page');
                        var ErrorMessage;  						
		                var ErrTitle = 0;
						var ErrT = 0; 
						
		               ErrorMessage = '';
		               $('#AddNewMessage').click(function() {
		                  console.log('Форма начинается подготавливаться!');
		                  if (Title.val() == '') {
                            ErrorMessage = 'Поле заголовок должно быть заполнено!';
                            if (!Title.hasClass('Error')) {
								Title.addClass('Error');
								ErrTitle = 1; 
							}	
						  } else 
							 if (Title.hasClass('Error'))
								Title.removeClass('Error');
                           
						   
						  if (Text.val() == '')
							  if (ErrorMessage == '') {
								  ErrT = 1; 
								  ErrorMessage = 'Поле текста должно быть заполнено!';
                              } else { 
							      ErrT = 1;
								  ErrorMessage.= 'Поле текста должно быть заполнено!';
                              }

                              if (ErrT == 0) 
                                 if (Text.hasClass('Error'))
								   Text.removeClass('Error');
                                 else 
                                   Text.addClass('Error'); 	

                              if ((ErrT == 0) && (ErrTitle == 0))
                                 return false;
                              else 
                                 return true;

                            console.log('Запрос завершен!');							 
	                   });
	 	            }); 
				   </script>
				   <?

				   $id_user = $this->BD_Obj->get_field('users', 'id', "`login` = '".$_SESSION['current_user']."'");			  
			       $fields = Array('title','text', 'creator_', 'from_id', 'to_id', 'type_message', 'see_blog');
				   $values = Array($_POST['name_page'],
				                   base64_encode($_POST['text_page']), 
								   $_SESSION['current_user'],
								   $id_user, 					   				   
								   $_GET['id'], 
								   'privatemess',
								   1);

				   if ($this->BD_Obj->insert('message', $fields, $values)) {
             		   $_SESSION['Message'] = "Сообщение было успешно отправлено! Хотите послать еще одно сообщение?";   
		               $_SESSION['Style'] = "color: #56ff56";    
                   } else {
        		      $_SESSION['Message'] = "Ошибка: Запись данных была прервана!";   
		              $_SESSION['Style'] = "color: red";   
                   }				   
				   
		      } else {
        		   $_SESSION['Message'] = "Ошибка: Поля не заполнены! Повторите ввод!";   
		           $_SESSION['Style'] = "color: red";   
              }			  
	        } else {
		      $_SESSION['Message'] = "Введите свое сообщение для данного пользователя!";   
		      $_SESSION['Style'] = "color: #56ff56";   
            }			
    }	
		
	/////////// Компонент FormAddMessage ///////////////////////////////////////////////////
	//
	//  Компонент формы формирования нового сообщения 
	//   1. captha - капча формы.
	//   2. nameform - шаблон-форма.
	//
	////////////////////////////////////////////////////////////////////////////////////////
	public function FormAddMessage($captha = false, $nameform = 'form_newmessage.php') {
	   if ($captha == true)  
	     $nameform = "form_newmessage_captha.php"; 
	  	   
	   $Cnf = new Class_Config; ?>
	   <p style="<?=$_SESSION['Style']; ?>"><?=$_SESSION['Message'];?></p>  
	   <?
	   if ($Cnf->localServer)
	       $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/'.$Cnf->FolderRoot.'/core/views/forms/'.$nameform;
	   else	 
	       $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/core/views/forms/'.$nameform;  
		
	   if(!empty($tpl_name) && file_exists($tpl_name)) {
          echo file_get_contents($tpl_name);
       } else {
		   $_SESSION['Message'] = "Ошибка: файл шаблона не найден!";   
		   $_SESSION['Style'] = "color: red";   
	   }
	}	
	
	/////////// Компонент (isAuthorize) ////////////////////////////////////////////////////////////
	//
	//    Компонент проверки статуса авторизации в системе
	//
	////////////////////////////////////////////////////////////////////////////////////////////////
	public function isAuthorize() {
       if (($_SESSION['current_user']) && ($_SESSION['id_session'])) {
	     $where = "`id_session` = '".$_SESSION['id_session']."'";
	     if ($this->BD_Obj->count_rec('visit', "Where ".$where) > 0) 
		   return $this->BD_Obj->get_field('users', 'id', "`login` = '".$_SESSION['current_user']."'"); 
	     else 
		   return false;
       } else 
		   return false;	   
 	}
	
	/////////// Компонент (thisUser) ////////////////////////////////////////////////////////////
	//
	//    Компонент проверки статуса пользователя в системе
	//    1. current_user - имя (логин) юзера
	////////////////////////////////////////////////////////////////////////////////////////////////
	public function thisUser($current_user = '') {	   
	   if ((!isset($current_user)) || (empty($current_user)))
		   return false;
	   else
	     if ($_SESSION['current_user'] == $current_user) 
		   return true; 
	     else 
		   return false;	 
 	}
	
	/////////// Компонент (ExitAuthorize) ////////////////////////////////////////////////////////////
	//
	//    Компонент выхода из авторизации в системе
	//    1. redirect - страница-переадресация в случае выхода из авторизации 
	////////////////////////////////////////////////////////////////////////////////////////////////	
	public function ExitAuthorize($redirect = 'index.php') {
		if (($_GET['url']) && ($_GET['url'] == 'exit')) 
		  if (($_SESSION['current_user']) && ($_SESSION['id_session'])) {
             unset($_SESSION['current_user']);
			 unset($_SESSION['id_session']);
			 unset($_SESSION['Message']);
			 header('Location: '.$redirect);
			 exit;
          } else {
             $_SESSION['Message'] = 'Сессия или id сессии не найден!';
          }	
	}	
	
	////////// Компонент (FormAuthorizeUser_js) Валидация FormAuthorizeUser /////////////////////////////////////
    //
	//   Компонент подключения валидации авторизации (без параметров). Размещается
	//   в верхней части контента веб-проекта (в разделе head)
	//   1. redirect - переадресация в случае авторизации
	/////////////////////////////////////////////////////////////////////////////////////////////////	
    public function FormAuthorizeUser_js($redirect = 'index.php') {
	   function DelSession(){
		   unset($_SESSION['current_user']);
		   unset($_SESSION['id_session']);
	   }   
	   
	   if ((isset($_POST['key_hidden'])) && ($_POST['key_hidden'] == 'key_a')) {
	        if ((isset($_POST['login'])) && (!empty($_POST['login'])) && 
	           (isset($_POST['password'])) && (!empty($_POST['password']))) {				  
				  $_SESSION['Message'] = '';
				  $_SESSION['style_message'] = "color: red";
				  $Limit = 'LIMIT 0, 10;';
				  $solt = '$1$'.$this->Conf_Obj->Hidekey.'$';
				  $pwd = crypt($_POST['password'], $solt);
				  $Where = " (`login` = '".$_POST['login']."')";
				  $Data = $this->BD_Obj->select('users', Array('*'), $Where);  		   

				  if (empty($Data)) {
					  $_SESSION['Message'] = "Данный пользователь отсутствует в системе!";
				  } else {
                    if ($pwd == $Data[0]['pwd']) {
					   $_SESSION['style_message']= "color: #40f340";				
			           $_SESSION['Message'] = "Вы в системе!";
		               $_SESSION['current_user'] = $_POST['login'];
					   $_SESSION['id_session'] = $this->Strings->Random_text(30, false);

					   $id_user = 0;
					   $id_user = $this->BD_Obj->get_field('visit', 'id', "(`login` = '".$_POST['login']."')");
					   
					   $fields = Array('id_session', 
					                   'id_user', 
									   'ip', 
									   'login');	
									   
		               $values = Array($_SESSION['id_session'], 
					                   (int)$id_user, 
									   $_SERVER['REMOTE_ADDR'], 
									   $_POST['login']);
									   
			           $where = "(`id_session` = ".$_SESSION['id_session'].")";            
			           if ($this->BD_Obj->count_rec('visit', "Where ".$where) > 0) {			              
						  //echo $this->BD_Obj->update('visit', $fields, $values, $where);
			           } else {			   
                          $this->BD_Obj->insert('visit', $fields, $values);
	                      $fields_config = Array('theme_site', 'theme', 'imgtable','connected_user'); 
	                      $values_config = Array('nordlove', 'see', '1', $this->isAuthorize());
						  $this->BD_Obj->insert('config', $fields_config, $values_config);
			
                		  unset($_POST['login']);
						  unset($_POST['password']);

						  $result = true;
			              header('Location: '.$redirect);
						  exit;
                       }  				   
					} else {
                       $_SESSION['Message'] = "Пароль неверен!";
					   DelSession();
                    }					
			      }
				  
			   } else { 
				   $_SESSION['Message'] = "Логин и\или пароль были неправильными!";
			       $_SESSION['style_message'] = "color: red";
				   DelSession();
			   }
		    } else {
               if (($_SESSION['id_session']) && ($_SESSION['current_user'])) {
                   $where = "`id_session` = '".$_SESSION['id_session']."'";
				   if ($this->BD_Obj->count_rec('visit', "Where ".$where) > 0) {
					   $_SESSION['style_message']= "color: #40f340";				
			           $_SESSION['Message'] = "Вы в системе! Логин: ".$_SESSION['current_user'];
				   } else {
				       $_SESSION['Message'] = "Сессия не найдена в системе!";
			           $_SESSION['style_message'] = "color: red";
					   DelSession();
                   }				   
			   } else {
			     $_SESSION['style_message']= "color: #40f340";				
			     $_SESSION['Message'] = "Введите свои данные, чтобы войти в систему!";
			   }	 
			}
		?>
		<?
	}	
		
	///////// Компонент (Ads) Обьявления /////////////////////////////////////	
    //
	//  1. type_ads - категория обьявлений all / mess / messlove / messpaypal
	//  2. current_page - текущая страница
	//  3. amount - количество записей на странице
	//  4. paggi_where - адрес паггинатора
	//  5. where_dp - условия выбора
	//  6. sort - условия сортировки данных
	//
	//  данные из таблицы standart_message
	//
	////////////////////////////////////////////////////////////////////
	public function Ads($type_ads = 'all', $current_page = 1, $amount = 5, 
	                    $paggi_where = 'index.php?url=stran&page=', 
						$where_dp = '(`enable` = 1)', $sort = 'create_') 
	{
	   if ($type_ads !== 'all') {
	     if (empty($type_ads)) $type_ads = 'messpaypal'; 
		 $where = "(`type_message` = '".$type_ads."')";
		 if (!empty($where_dp)) $where .= " && ".$where_dp;
	   } else $where = $where_dp;	   
	   
	   $Massive = Array();
	   if (empty($current_page) && ($current_page)) $current_page = 1;
	    $Massive['paggi'] = ''; 
	    if (is_integer($current_page))
	    {
	      //if (($current_page) && is_integer($current_page)) $Limit = '';		  	      
		  $Massive['amount'] = $this->BD_Obj->count_rec('message', 'Where ('.$where.')');
	      //$this->Paggi->Set($first, $amount, $Massive['amount']);	   
		  $this->Paggi->SetfromPage($current_page, $amount, $Massive['amount']);	   
	      $end = (int)$this->Paggi->start_blog + (int)$amount;	   
	      if (is_integer($amount)) $Limit = "".$this->Paggi->start_blog.", $amount";	    	            		 	
		  
		  $Limit_int = $this->Paggi->Paggination($current_page);	      
		  $Massive['paggi'] = $this->Paggi->Component_Paggination($paggi_where, $current_page, true, true, 5);
	      if (!empty($Limit_int)) $Limit = $Limit_int;
	      $Massive['data'] = $this->BD_Obj->select('message', Array('*'), $where, $sort, false, $Limit);		   			
	    } else {	   
	  	  $Massive['amount'] = $this->BD_Obj->count_rec('message' , $where);  
          $Massive['data'] = $this->BD_Obj->select('message', Array('*'), $where, '', false);			  
	    }
	   
       $Massive['paggi_where'] = $paggi_where;	   
       $Massive['where'] = $where;	   
	   
	   return $Massive; 
	}

    ///////// Компонент (Finder) Поисковик //////////////////////////////////////
	// 1. key_world - ключевое слово поиска
	// 2. field_find - поле поиска (одно!)
	// 3. frag_find - фрагментарный поиск учитывается (true / false)
	// 4. debug - возвращаемый результат строки условия (true / false)
	// 5. table - таблица в базе данных
	// 6. where - условия поиска данных
	////////////////////////////////////////////////////////////////////////////

	public function Finder($key_word,
                           $field_find = "title", 	
	                       $frag_find = true, 
						   $debug = false, 
						   $table = 'blogs', 
						   $where = '') {{
         
	    if ($frag_find) 
	       $whr = " (`$field_find` LIKE '%$key_word%') ";
	     else 
           $whr = " (`$field_find` = '$key_word') "; 	    
	   
	    if (!empty($where))
	       $whr.= " and ($where)";
	   
	   
       if ($debug) { 
            $data['count'] = $this->BD_Obj->count_rec($table , $whr, $debug);  
            $data['data'] = $this->BD_Obj->select($table, Array('*'), $whr, '', true, "", $debug);	   			
            $data['where'] = $where;
	        return $data;
       } else {
            $data['count'] = $this->BD_Obj->count_rec($table , $whr);  
            $data['data'] = $this->BD_Obj->select($table, Array('*'), $whr, '', true);	   			
	   }	
			
		if (!empty($data))
		   return $data;
		else
           return 0;				                }	   	  
   }
   
  ///////// Компонент (ComponentVusualFinder) Поиск //////////////////////////////////////
  // 1. field_find - поле поиск.   
  // 2. table - таблица-данных.
  // 3. where - условия поиска.
  // 4. sort - сортировка данных.
  // 5. name_form - имя формы.
  // 6. tpl - шаблон поисковой формы.
  // 7. tpl_input_data - шаблон вывода данных.
  // 8. area_print - область вывода данных поиска.
  // 9. action - адрес перехода.  
  // 10. class - класс формы.
  // 11. style - стили формы.   
  // 12. frag_find (true, false) - фрагментарный поиск. 
  // 13. paggination_url - адрес url в виде паггинации.
  // 14. debug (true / false) -  режим тестировки.
  // 15. button - титул-субмит  
  //////////////////////////////////////////////////////////////////////////// 
   public function ComponentVisualFinder($field_find = "title",  
                                         $table = 'blogs', 
										 $where = '',
										 $sort = ' id ',
										 $name_form = "",
										 $tpl = "",
										 $tpl_input_data = "core/models/ajax/ComponentVisualFinder_group.php",
										 $area_print,
										 $class = "",
										 $style = "",
	                                     $frag_find = true, 
										 $paggination_url="index.php?url=bookinterest&page=",
						                 $debug = false, 						                 										  
										 $button = "Поиск") {?>
    <script>
        $(document).ready(function() {
		    $("#clear<?=$name_form;?>").click(
                function(e) {	   
                    var key_find = $("#key_find<?=$name_form;?>");
                        key_find.val("");
						console.log("Clear find submit<?=$name_form;?>");
						$("#submit<?=$name_form;?>").click();
                    }
				);
            					
			$("#submit<?=$name_form;?>").click(function(){
                var name_id = $(this); 
                var key_find = $("#key_find<?=$name_form;?>");
                var value_key_find = key_find.val();				
	            var area = $("#<?=$area_print?>");
				   console.log('Begin find key');
				   area.html("<img src='images/ajax-loader.gif' style='width: 50px; margin-left: 50px margin-top: 50px'/>");
				   $.get("core/models/ajax/ComponentVisualFinder.php", 
				        {table: "<?=$table?>", where: "<?=$where?>", field_find: "<?=$field_find?>",
						 frag_find: <?=$frag_find?>,debug: "<?=$debug?>", sort: "<?=$sort?>",
						 tpl_input_data: "<?=$tpl_input_data;?>", paggination_url: "<?=$paggination_url;?>", 
						 value_key_find: value_key_find 
						}
				     ).done(
					    function(data) {
                          console.log( "Data Loaded information: " + data);
						  area.html(data);
                        }
					);            					
			    });				
		});			 
	</script>									 
	<?if (empty($tpl)) {?> 
		<div id="form<?=$name_form;?>" class="<?=$class;?>" style="<?=$style;?>">
            <input type="text" id="key_find<?=$name_form;?>" name="key_find<?=$name_form;?>" class="input_text text_<?=$name_form;?>"/>
	        <button type="submit" class=" red1 submit_<?=$class;?>" id="submit<?=$name_form;?>" name="submit<?=$name_form;?>"><?=$button;?></button>
            <button class=" red1 submit_<?=$class;?>" id="clear<?=$name_form;?>" name="clear<?=$name_form;?>">Очистка</button>			
		</div>   
		<div style="clear: both"></div>		   
	 <? }
	 else 	  	   
	  if (file_exists($tpl)) include_once($tpl);	 
   }

  ///////// Компонент (ComponentSQLVusualFinder) Поиск //////////////////////////////////////
  // 1. sql - запрос sql данных
  // 2. where - условия поиска.   
  // 3. name_form - имя формы.
  // 4. tpl_input_data - шаблон вывода данных.
  // 5. area_print - область вывода данных поиска.
  // 6. class - класс формы.
  // 7. style - стили формы.   
  // 8. paggination_url - адрес url в виде паггинации.
  // 9. debug (true / false) -  режим тестировки.
  // 10. button - титул-субмит  
  //////////////////////////////////////////////////////////////////////////// 
   public function ComponentSQLVisualFinder($sql,
                                            $where = "",      
                                            $ArrayFindFields = Array(),
										    $name_form = "",
										    $tpl_input_data = "core/models/ajax/ComponentSQLVisualFinder_group.php",
										    $area_print,
										    $class = "",
										    $style = "",
	                                        $paggination_url="index.php?url=bookinterest&page=",
						                    $debug = false,                                         											
										    $button = "Поиск",
											$sort = " id "
											) {?>
    <script>
        $(document).ready(function() {
		    $("#clear<?=$name_form;?>").click(
                function(e) {	   
                    var key_find = $("#key_find<?=$name_form;?>");
                        key_find.val("");
						console.log("Clear find submit<?=$name_form;?>");
						$("#submit<?=$name_form;?>").click();
                    }
				);
            					
			$("#submit<?=$name_form;?>").click(function(){
                var name_id = $(this); 
                var key_find = $("#key_find<?=$name_form;?>");
                var value_key_find = key_find.val();				
	            var area = $("#<?=$area_print?>");
				
				   console.log('Begin find key');				   
				   area.html("<img src='images/ajax-loader.gif' style='width: 50px; margin-left: 50px margin-top: 50px'/>");
				   
				   $.get("core/models/ajax/ComponentSQLVisualFinder.php", 
				        {sql: "<?=$sql?>",debug: "<?=$debug?>", 
						 tpl_input_data: "<?=$tpl_input_data;?>", 
						 where: "<?=$where?>",
						 paggination_url: "<?=$paggination_url;?>",
						 fields: <?=json_encode($ArrayFindFields)?>,
						 value_key_find: value_key_find,
                         sort: "<?=$_GET['sort']?>"						 
						}
				     ).done(
					    function(data) {
                          console.log( "Data Loaded information: " + data);
						  area.html(data);
                        }
					);            					
			    });				
		});			 
	</script>									  
		<div id="form<?=$name_form;?>" class="<?=$class;?>" style="<?=$style;?>">
            <input type="text" id="key_find<?=$name_form;?>" name="key_find<?=$name_form;?>" class="input_text text_<?=$name_form;?>"/>
	        <button type="submit" class=" red1 submit<?=$class;?>" id="submit<?=$name_form;?>" name="submit<?=$name_form;?>"><?=$button;?></button>
            <button class=" red1 submit<?=$class;?>" id="clear<?=$name_form;?>" name="clear<?=$name_form;?>">Очистка</button>			
		</div>   
		<div style="clear: both; padding-bottom: 20px;"></div>		   
	 <?    
	}

   
  ///////// Компонент (ComponentFinderBlog) Упрощенный Поисковик Блога //////////////////////////////////////
  // 
  // 1. frag_find - фрагментарный поиск (true / false)
  // 2. answer_where - возвращаемый результат строки условия (true / false)
  //
  // Примечание: Нарушены правила MVC ради универсальности компонента. В дальнейшем визуальная часть компонента
  // html/css будет отделена в виде tpl файлов, которые будут подгружаться в классы в виде редактируемых форм
  //
  //////////////////////////////////////////////////////////////////////////// 
   public function ComponentFinderBlog($frag_find = true, $answer_where = false) {
	  ?>
        <p>Поиск: <? if (empty($_POST['key_find'])) 
                       if (!empty($_GET['key_find'])) $_POST['key_find'] = $_GET['key_find'];			
		             if (!empty($_POST['key_find'])) {echo "по фразе '".$_POST['key_find']."'";} else ?>
		          <? //if (!empty($_GET['key_find'])) {echo "по фразе '".$_GET['key_find']."'";}?></p>
		<form id="form" action="index.php?url=blogs" method="post" style="margin-bottom: 20px;">
            <input type="text" id="key_find" name="key_find" />
	        <input type="submit" class="add_button" style="background: rgb(240, 90, 84); " id="submit" name="submit" value="Поиск"/>
        </form>   
		<div style="clear: both"></div>	
      <?	

        $where_find = '';
	    if (($_POST['key_find']) && (!empty($_POST['key_find']))) $find_str = $_POST['key_find'];
		//if (($_GET['key_find']) && (!empty($_GET['key_find']))) $find_str = $_POST['key_find'];
        if (($find_str) && (!empty($find_str)))
		{  
           $Books = $this->Finder($find_str, true, true);
	       if (!empty($Books)) {
	          $where_find = " && ".$Books;
	       }  
        }	 
						 
	    if (empty($_GET['page'])) $_GET['page'] = 1;			 
	    if (!empty($_GET['tag'])) {
			$where = "((`visible` = 1) && (`tag2` = '".trim($_GET['tag'])."') || (`tag3` = '".trim($_GET['tag'])."') || (`tag4` = '".trim($_GET['tag'])."')) ".$where_find;
			$paggi_where = "index.php?url=blogs&tag=".trim($_GET['tag'])."&page=";
		}	
		else {
			if (!empty($_POST['key_find'])) $key_where = '&key_find='.$_POST['key_find'];
			//if (!empty($_GET['key_find'])) $key_where = '&key_find='.$_GET['key_find'];
			//if (!empty($_POST['key_find'])) $key_where = 
			$where = " ((`visible` = 1) && (`tag1` = 'blogs'))".$where_find;
			$paggi_where = "index.php?url=blogs".$key_where."&page=";
	    }

		$Data = Array();
        $Data['where'] = $where; 
		$Data['paggi_where'] = $paggi_where;
        return $Data;	  		
   }       
	
	////////// Селект-меню (SelectMenu) пункт меню //////////////////////////////////
	// 1. URL - адрес сравнения с текущей страницей
	// 2. CurrentURL - текущая страница, если текущая страница не задана в параметрах,
	// то функция возвращает параметры сервера SERVER_NAME и PHP_SELF с протоколом http
	////////////////////////////////////////////////////////////////////
    
	public function SelectedMenu($URL, $CurrentURL = '')
	{	   
	   if (empty($CurrentURL)) $CurrentURL = $this->getUrl();
	   if ($URL == 'index.php') 
	     $URL = $this->getUrl(true);
	   else
	     if (strpos($URL, 'http://') === FALSE) 
		    $URL = $this->getUrl(true).$URL;
	   //return $URL." || ".$CurrentURL;
       if ($URL == $CurrentURL)	
	     return true;
       else 
	     return false;	   		
	}

	private function Messages_($janr = 'all') 
	{	        
         ////////////////////////////////////////////
	     //  Виды рандомных сообщений:    
		 //      message - собщение 
	     //	     my_message - сообщение создателя проекта
	     //	     humor - юмор
	     ////////////////////////////////////////////
	   	        	   	   
	     if ((isset($janr)) && ($janr == 'all')) {
	      $RWhere = '';
		  $Where = ''; 
	     } else {
	       if ($janr) {
	         $RWhere = "Where (type_rand = '".$janr."')"; 
	         $Where = "(type_rand = '".$janr."')"; 
		   }  
	     }
		 
		 $count_rec = $this->BD_Obj->count_rec('randmess' , $RWhere);   
	   
	     if ($janr == 'all') {
	       $minRange = 0;
		   $maxRange = $count_rec;
	     }
	   
	     $data = $this->BD_Obj->select('randmess', Array('*'), $Where, '', true);	   	   
	     $ReturnData = Array();
	   
	     if (!empty($data))
	       foreach ($data as $key => $val) {
             $ReturnData[]['id'] = $val['id'];
             $ReturnData[]['title'] = $val['title'];			 
             $ReturnData[]['text'] = $val['text'];
			 break;
	       }	  	  
	       
		   return $data; 	              	   
	} 
		
	/////////// Компонент (RandMess) рандомная фраза ///////////////////////////////
	// 1. janr - вид жанра рандомной фразы (message, my_message, humor)	 
	/////////////////////////////////////////////////////////////////////
	
    public function RandMess($janr = 'all') { 
      	
      $res = $this->Messages_($janr);
      $counts = count($res);	 
	  $id_rand = rand(0, $counts-1);
     
	  $RandomArray = Array();	 
	  foreach ($res as $key => $val) 
	  {
	    if ($id_rand == $key) {
	      $RandomArray['id'] = $val['id'];
	      $RandomArray['title'] = $val['title'];
	      $RandomArray['text'] = $val['text'];		  
		}		
	  }	
	  //print_r($RandomArray);
	  return $RandomArray;
    }

	/////////// Компонент (Rubricator) рубрикатор ///////////////////////////////
	// 1. alphabet - параметр оглавления в рубрикаторе 
	// 2. result_array - возвращение результата в виде массива (по умолчанию ДА)
    // 	
	/////////////////////////////////////////////////////////////////////
	
    public function Rubricator($alhpabet = true, $resultarray = true) { 
        $RWhere = '';
		$AllWhere = '';
		$Count_Rec = 0;
		
		$Count_Rec = $this->BD_Obj->count_rec('tags' , $RWhere);
		$Data = $this->BD_Obj->select('tags', Array('*'), $AllWhere, '', true);	
		
		$FirstAlphabet = '';
		$Alphabet_Array = Array(); 
		$Result_Array = Array();
		
		if (count($Data) > 0)
		  foreach ($Data as $Key => $Value) 		  		    		    		    			 
			$Alphabet_Array[] = $Value['tag'];            		   
	    sort($Alphabet_Array);	
		
		foreach ($Alphabet_Array as $AlphaVal) {		   	
			$RWhere = "Where (tag2 = '".$AlphaVal."') || (tag3 = '".$AlphaVal."') || (tag4 = '".$AlphaVal."')";				
			$Count_Rec = $this->BD_Obj->count_rec('blogs' , $RWhere);
		    $Result_Array[] = Array('rubricator' => $AlphaVal." (".$Count_Rec.")", 'tag' => $AlphaVal);
		}
		if ($resultarray) 
          return $Result_Array;
        else		  
		  return $Alphabet_Array;
    }
    	
	/////////// Компонент (Menu) меню //////////////////////////////////////////
    // Компонент вывода данных меню. От корня до дочерних пунктов меню.
	//
	// 1. id -  номер меню корня.
	// 2. amount - количество пунктов меню.
	// 3. where - условия выбора меню.
	// 4. order_by - условия сортировки меню.
    //	
	/////////////////////////////////////////////////////////////////////

    public function Menu($id, $amount = 20, $where = '', $order_by = '')
	{
	   $Massive = Array();
	   $Limit = '';
	   
	   if (is_integer($amount)) $Limit = "0, $amount";
	   //$AllWhere = "((id = ".$id.") || (parent_menu = ".$id.")) and (sort > 0) and (see_blog = 1)";	   
	   //$RWhere = " Where ".$AllWhere;
	   $AllWhere = "(parent_menu = ".$id.") and (see_blog = 1)";	   
	   $RWhere = " Where ".$AllWhere;
	   	   
	   if ((isset($where)) && (!empty($where))) {
	      $RWhere = ' and '.$where;
		  $AllWhere.= $RWhere; 
	   }   	
	   
	   // echo $this->BD_Obj->count_rec('menu' , $RWhere, true);
	   $Massive['amount'] = $this->BD_Obj->count_rec('menu' , $RWhere);
	   $Massive['data'] = Array();
	   $Massive['limit'] = $Limit;
	   if ($Limit === '') 
		 $Massive['data'] = $this->BD_Obj->select('menu', Array('*'), $AllWhere, $order_by, true);	   
       else 
		  $Massive['data'] = $this->BD_Obj->select('menu', Array('*'), $AllWhere, $order_by, true,  $Limit);		
          	  
	   return $Massive;
	  
	}

	/////////// Компонент (Menu_id) пункт меню //////////////////////////////////////////
	// Компонент вывода пункта меню.
	// 1. id - код пункта меню (связанного с редактором админки TheSolarWind)
	////////////////////////////////////////////////////////////////////////////////////	
	public function Menu_id($id)
	{
	    $Massive = Array();
        $Limit = '';		
		$AllWhere = "id = $id";
		$RWhere = "Where ".$AllWhere;
		$Massive['amount'] = $this->BD_Obj->count_rec('menu' , $RWhere);
		$Massive['data'] = $this->BD_Obj->select('menu', Array('*'), $AllWhere, '', true);	 
        
		return $Massive;		
	}

	/////////// Компонент (SQLBlocks) инфоблоки //////////////////////////////////////////
	// Компонент вывода пользовательских-sql данных отвечающих шаблону блоковых таблиц 
	// (т.е. обязательные поля sort, visible, see_blog, select_blog, for_del).
	//
	// 1. sql -  sql-запрос блока (таблица, вьюшка)
	// 2. sql_count - sql-запрос количества записей (таблица, вьюшка)
	// 3. first - первая запись начала вывода данных
	// 4. amount - количество пунктов блока
	// 5. where - условия выбора блока	
	// 6. sort - сортировка блоков
	// 7. current_page - текущая страница
	// 8. url - шаблон url для паггинации
	// 9. debug - отладчик запроса
	//
	/////////////////////////////////////////////////////////////////////
		
	public function SQLBlocks($sql, $sql_count, $first = 0, $amount = 5, $where = '', $sort = '', 
						   $current_page = '1', $url = '', $debug = false) {
	   $Massive = Array();
	   $Limit = '';
	   
	   if ((!is_integer($first)) || (!is_integer($amount)))
	   {
	       $first = 0;
		   $amount = 5;
	   }
	   
	   $end = (int)$first + (int)$amount;

	   //if (empty($where)) $where= "(`see_blog` = 1)";
       //else $where.= " and (`see_blog` = 1)";
   
	   $Limit = "$first, $amount";	    	  
       $Massive['amount'] = $this->BD_Obj->count_sql_rec($sql_count, $debug);
	   
	   $this->Paggi->Set($first, $amount, $Massive['amount']);	   
	   $Limit_int = $this->Paggi->Paggination($current_page);
	   $Massive['paggi'] = $this->Paggi->Component_Paggination($url, $current_page, true, true, 5);	   
 
       $sort = " ".$sort." ";		 
	     	   	   
	   $Limit = " LIMIT ".$Limit;
	   $Massive['data'] = $this->BD_Obj->simple_select($sql.$where.$sort.$Limit.";", $debug);
           	  
	   return $Massive;
	}
	
	/////////// Компонент (DistinctBlocks) инфоблоки //////////////////////////////////////////
	// Компонент вывода уникальных данных отвечающих шаблону блоковых таблиц 
	// (т.е. обязательные поля sort, visible, see_blog, select_blog, for_del).
	//
	// 1. tag -  теги отбора блока (таблица, либо вьюшка)
	// 2. distinct_ - группировка данных по полю
	// 3. first - первая запись начала вывода данных
	// 4. amount - количество пунктов блока
	// 5. where - условия выбора блока	
	// 6. sort - сортировка блоков
	// 7. current_page - текущая страница
	// 8. url - шаблон url для паггинации
	// 9. rating - сортировка по рейтингу 
	// 10. see_block - видимость блоков 
	// 11. debug - режим отладки
	// 
	/////////////////////////////////////////////////////////////////////
		
	public function DistinctBlocks($tag = 'blogs', $distinct_ = "id", $first = 0, $amount = 5, $where = '', $sort = '', 
						   $current_page = '1', $url = '',  $rating = false, $see_block = false, $debug = false
					)	                            
	{
	   $Massive = Array();
	   $Limit = '';
	   
	   if ((!is_integer($first)) || (!is_integer($amount)))
	   {
	       $first = 0;
		   $amount = 5;
	   }
	   
	   $end = (int)$first + (int)$amount;

	   if (empty($where)) $where= "(`see_blog` = 1)";
       else $where.= " and (`see_blog` = 1)";
   
	   if (is_integer($amount)) $Limit = "$first, $amount";	    	  
       $Massive['amount'] = $this->BD_Obj->count_rec($tag, 'Where ('.$where.')');
	   
	   $this->Paggi->Set($first, $amount, $Massive['amount']);	   
	   $Limit_int = $this->Paggi->Paggination($current_page);
	   $Massive['paggi'] = $this->Paggi->Component_Paggination($url, $current_page, true, true, 5);
	   
	   if (!empty($sort)) { 		   
         if ($rating == true) 
		   $sort= " sort desc, ".$sort;
         else 
           $sort = " ".$sort." ";		 
   	   }
	   
	   if (!empty($Limit_int)) $Limit = $Limit_int;
	   $Massive['data'] = $this->BD_Obj->DistinctSelect($tag, $distinct_, $where, $sort, true, $limit, $debug);
	       	  
	   return $Massive;
	}

	/////////// Компонент (Blocks) инфоблоки //////////////////////////////////////////
	// Компонент вывода данных отвечающих шаблону блоковых таблиц 
	// (т.е. обязательные поля sort, visible, see_blog, select_blog, for_del).
	//
	// 1. tags -  тег отбора блока (таблица, либо вьюшка)
	// 2. first - первая запись начала вывода данных
	// 3. amount - количество пунктов блока
	// 4. where - условия выбора блока	
	// 5. sort - сортировка блоков
	// 6. current_page - текущая страница
	// 7. url - шаблон url для паггинации
	// 8. rating - сортировка по рейтингу 
	// 9. see_block - видимость блоков 
	// 10. distinct_ - группировка данных по полю
	// 11. debug - режим отладки
	// 
	/////////////////////////////////////////////////////////////////////
		
	public function Blocks($tags = 'blogs', $first = 0, $amount = 5, $where = '', $sort = ' id ', 
						          $current_page = '1', $url = '',  $rating = false, $see_block = false,
						          $distinct_ = false, $debug = false
						   )
	                            
	{
	   $Massive = Array();
	   $Limit = '';
	   
	   if ((!is_integer($first)) || (!is_integer($amount)))
	   {
	       $first = 0;
		   $amount = 5;
	   }
	   
	   $end = (int)$first + (int)$amount;

	   if (empty($where)) $where= "(`see_blog` = 1)";
       else $where.= " and (`see_blog` = 1)";
   
	   if (is_integer($amount)) $Limit = "$first, $amount";	    	  
       $Massive['amount'] = $this->BD_Obj->count_rec($tags, 'Where ('.$where.')');
	   
	   $this->Paggi->Set($first, $amount, $Massive['amount']);	   
	   $Limit_int = $this->Paggi->Paggination($current_page);
	   $Massive['paggi'] = $this->Paggi->Component_Paggination($url, $current_page, true, true, 5);
	    
	   if (empty($sort)) { 		   
       
	   } else { 
	      if ($rating == true) 
		   $sort= " sort desc, ".$sort;
          else 
           $sort = " ".$sort." ";		 
   	   }
	   
	   if (!empty($Limit_int)) $Limit = $Limit_int;
	   
        $Massive['data'] = $this->BD_Obj->select($tags, Array('*'), $where, $sort, false, $Limit, $debug);		
           	  
	   return $Massive;
	}

	/////////// Компонент (HTMLBlocks) инфоблоки //////////////////////////////////////////
	// Компонент вывода данных отвечающих шаблону блоковых таблиц 
	// (т.е. обязательные поля sort, visible, see_blog, select_blog, for_del).
	//
	// 1. tags -  тег отбора блока (таблица, либо вьюшка)
	// 2. options - массив параметров визуального вида таблицы
	//       1. id_table - id блока таблицы
    //       2. class_table - класс таблицы
    //       3. array_data - данные-поля таблицы
    //       4. array_title - титулы заголовки
    //       5. default_value - дефолтные значения полей. Если тип значений (code), то default_value представляется 
	//          в виде массива Array('table' => 'Имя_таблица', 
	//                               'field' => 'Имя_поля_вывода', 
	//                               'where' => 'Условия_поиска')
    //	     6. field_type - типы значений полей (code, text, img, del)
	// 3. first - первая запись начала вывода данных
	// 4. amount - количество пунктов блока
	// 5. where - условия выбора блока	
	// 6. sort - сортировка блоков
	// 7. current_page - текущая страница
	// 8. url - шаблон url для паггинации
	// 9. rating - сортировка по рейтингу 
	// 10. see_block - видимость блоков 
	// 11. distinct_ - группировка данных по полю
	// 
	/////////////////////////////////////////////////////////////////////
		
	public function HTMLBlocks($tags = 'blogs', $options = Array('id_table' => 'nametable',
	                                                            'class_table' => 'table_blur',
	                                                            'array_data' => Array(), 
	                                                            'array_title' => Array(),
								                                'default_value' => Array(),
								                                'field_type' => Array(),
															   ),
	                          $first = 0, $amount = 5, $where = '', $sort = ' id ', 
	   				          $current_page = '1', $url = '',  $rating = false, $see_block = false,
						      $distinct_ = false
						     ) {
		   
		$rec = $this->BD_Obj->count_rec($tags, "Where (".$where.")");
        if ($rec > 0) 
           $get_data = $this->Blocks($tags, $first, $amount, $where, $sort, 
									 $current_page, $url, $rating, $see_block, $distinct_);?>
		 <div class="paggination"><?=$get_data['paggi'];?></div>
	     <table id="<?=$options['id_table']?>" class="<?=$options['class_table']?>">        
		 <tr>
		  <? foreach ($options['array_title'] as $valTitle) {?>
           <th><?=$valTitle;?></th>	
	      <? } ?>
         </tr>
	     <? if (empty($get_data['data'])) {?>
	      <tr>
		  <? foreach ($options['array_title'] as $valTitle) {?>
		    <td> - 
		  <? } ?>
		  </tr>
	   <? } else { 
	        foreach ($get_data['data'] as $key => $value) {?>
	      <tr>
	      <? foreach ($options['array_data'] as $key_field => $value_field) {?>
		    <td><?if (($options['field_type'][$key_field]) == 'code' ) {?>
			        <span class="span_<?=$options['id_table'];?>">
					<?=$this->BD_Obj->get_field($options['default_value'][$key_field]['table'], 
					                              $options['default_value'][$key_field]['field'], 
												  "(".$options['default_value'][$key_field]['where'].$value[$value_field].")");
					?></span><?	
				} elseif (($options['field_type'][$key_field]) == 'text') {								  												  
					?><span class="span_<?=$options['id_table'];?>"><?=$value[$value_field];?></span>
			    <?} elseif (($options['field_type'][$key_field]) == 'img') {?>
                   <img src="<?=$value[$value_field]?>" class="img_<?=$options['id_table'];?>" />
				<?
                } elseif (($options['field_type'][$key_field]) == 'del') {?>
                    <a param="<?=$value[$value_field]?>">x</a>
                <?}				
			?>
			</td>
          <? }?>  			
	      </tr>       
	 <?   }
	    }?>
        </table>
		<div class="paggination"><?=$get_data['paggi']; ?></div>
     <?		
	}						 
		
	//////////// Компонент (AjaxFileLoad) инфоблока ///////////////////////////////////////////
	// Функция AjaxFileLoad осуществляет загрузку данных на сервер методом ajax 
	//
	//  1. folder - папка загрузки данных
	//  2. format_files - зарезервированные параметры 
	//  3. progressbar - зарезервированные параметры 
	//  4. paths_files - зарезервированные параметры 
	///////////////////////////////////////////////////////////////////////////////////////////
	public function AjaxFileLoad($folder = 'upload', $format_files = '', $progressbar = true, $paths_files = true) {
	?>
     <script>  
      $(document).ready(function(){
         var files; 
		 
		 $('input[type=file]').change(function() {
		     console.log('Change files!');
		     console.log(this.files); 
		     files = this.files;
	      });
	 	 
		 function move() {
            var elem = document.getElementById("myBar"); 
            var width = 0;
            var id = setInterval(frame, 10);
            function frame() {
               if (width >= 100) {
                  clearInterval(id);
               } else {
                  width++; 
                  elem.style.width = width + '%'; 
                  elem.innerHTML = width * 1 + '%';
               }
            }
         } 
	 
	      $("#button").click(
            function () {
		      event.stopPropagation(); 
              event.preventDefault();  
              console.log('Testing load data.......');
			  
              var data = new FormData();
              $.each(files, function(key, value){
                   data.append(key, value);
              });
 	
	          console.log('Create ajax query!');

	          jQuery.ajax({
                  url: "core/models/ajax/files_load.php?uploadfiles&folder=<?=$folder;?>",
                  type: 'POST',
                  data: data,
                  dataType: 'json',
				  processData: false, 
                  contentType: false, 
                  success: function( respond, textStatus, jqXHR ){
                  if( typeof respond.error === 'undefined' ){
                     var files_path = respond.files;
                     var html = '';
                     $.each(files_path, function(key, val){ 
				        html += val +'<br>'; } 
				     );
                     
					 $('.paths_files').html(html);
                  }
                  else{
                        console.log('Error answer server: ' + respond.error);
                    }
                  },
                  error: function(jqXHR, textStatus, errorThrown){
                      console.log('Error ajax: ' + textStatus);
                  }
                });				
				console.log('final load data');
			});
	    });			
	  </script>
	  
      <input type="file" multiple="multiple" accept=".txt,image/*">
        <br>
      <button id="button" name="button" value="button"/>Кнопка</button>
        <br><br>
      
	  <? if ($progressbar) {?>
	   <div id="myProgress">
          <div id="myBar">0%</div>
       </div><br> 
	   <? }
	      
	   ?>
	   <div class="paths_files"></div>  
	<?		
	}	
	
	/////////// Компонент (AjaxUnicalForm) инфоблока //////////////////////////////////////////
	//  Функция Ajax вывода данных в результате каких-то действий в форме path_form и их обработки в файле path_ajax  
	//  1. id_form - id-формы.
	//  2. array_data - значения fields формы и GET значений
	//  3. array_title - значения заголовков полей формы
	//  4. default_value - дефолтные значения полей fields
	//  5. tag_input - тег-контейнер вывода данных
	//  6. button_enter - кнопка запуска передачи данных
	//  7. options - массив опций данных визуального вывода данных и табличных структур.
	//       7.1. table - имя таблицы вывода данных
    //       7.2. where - условия вывода данных	
    //       7.3. sorter - сортировка данных
	//       7.4. select_php - ajax-файл вывода данных таблицы
	//       7.5. reload_php - ajax-файл шаблона вывода данных таблиц
	//       7.6. paggination - url-паггинации компонента
	//       7.7. class_input - класс для оформления input 
	//       7.8. class_label- класс для оформления титулов
	//       7.9. class_button - класс для оформления кнопок
	//       7.10 name_submit - имя кнопки
	//  8. debug - режим отладки (true / false) 
	/////////////////////////////////////////////////////////////////////////////////
	public function AjaxUnicalForm($id_form = "ajax_form1", 
	                                   $array_data = Array(), 
	                                   $array_title = Array(),
								       $default_value = Array(),
								       $field_type = Array(),
									   $get_path = 'core/models/ajax/AjaxUnicalForm_insert.php',
								       $tag_input = "message",
								       $button_enter = "button",
								       $options = Array('table' =>'test', 
								                        'where' => '',
														'sorter' => 'id',
														'select_php' => 'core/models/ajax/AjaxUnicalForm_select.php',
														'reload_php' => 'core/models/ajax/AjaxUnicalForm_grid.php',
													    'paggination' => 'index.php?url=nameurl&page=',	
													    'class_input' =>'style_input',
													    'class_label'=>'style_label',
													    'class_button'=>'style_button',
													    'name_submit'=>'Выполнить'
													    ),
									   $debug = false					
									  )
	{
		$DataAjaxSQL = Array();
		$POST_GET = Array();
		$get = "";
		?>
		       <script> 
			    $(document).ready(function(){
                    function AjaxUnicalForm_process(data) {
					    var areaRefresh = $("#<?=$tag_input;?>");
					    console.log('Load data content sql');
						console.log(data);
						areaRefresh.html("<img src='images/ajax-loader.gif' style='width: 50px; margin-left: 50px margin-top: 50px'/>");				   
						$.get("<?=$options['select_php']?>", 
					        {
							  table: "<?=$options['table']?>", 
							  where: "<?=$options['where']?>",
							  sorter: "<?=$options['sorter']?>",
							  paggination: "<?=$options['paggination']?>",
							  execute_reload: "<?=$options['reload_php']?>",
							  debug: "<?=$debug?>"
							}
						  ).done(
					       function(d) {
                             console.log( "Data Loaded AjaxUnicalForm: ");
							 areaRefresh.html(d);				   
                           });						
                    }
				
                    $("#clear_<?=$id_form.$button_enter;?>").click(
                       function(e) {	   
						  $(".el_<?=$id_form?>").each(function() {                            
							 var $this_ = $(this);						 
							 if ($this_.attr('type') != 'hidden') {  
							   $this_.val(""); 
							 }  
                          });
					   }
                    ); 					
					
					$("#<?=$id_form.$button_enter;?>").click(
                        function (e) {					        
							var $GetVal = '';
							var $i = 0;
							
							console.log('Run!');					   
                            console.log('Get data in file'+'<?=$get_path;?>');
							$GetVal += '&titles=';
							<? foreach ($array_title as $v) {?>
							    $i++;
								$GetVal += ''+'<?=$v;?>'+'|';
							<? } ?>							
							
							//$('input[type=text]').each(function() {
                            $(".el_<?=$id_form?>").each(function() {                            
							   $GetVal +='&';
							   $GetVal += ""+$(this).attr("name")+"="+$(this).val()+""; 
							   $i++;
                            });	
							
							console.log('GEEETTTIM: '+$GetVal);
							console.log('Parametrs: '+'<?=$get;?>');
							$.get("<?=$get_path;?>", 
							      'table=<?=$options['table'];?>&class_table=<?=$options['class_table'];?>&where=<?=$options['where'];?>&'+$GetVal, 
								  AjaxUnicalForm_process);
					        console.log('Data success GET!');
                        });
				    });  
			   </script>				  
			   
		<?
		if ((is_array($array_data)) && (!empty($array_data))&&
			(is_array($array_title)) && (!empty($array_title))) {
			if ((count($array_data) == count($array_title)) && 
				(count($array_data) == count($field_type)))  {
	         		$i = 0;?>
				<div class="container_ajax">
					<?
					foreach ($array_data as $key=>$value) {
					  if ($field_type[$key] != "hidden") { 
					  ?>
					  <label for="<?=$value;?>"	id="label<?=$value;?>" class="<?=$options['class_label'];?>" name="label<?=$value;?>"><?=$array_title[$key];?></label> <br>
					  <? }
					  if ($field_type[$key] == "text") {?>
					  <input type="text" id="<?=$value;?>" name="<?=$value;?>" class="<?=$options['class_input'];?> el_<?=$id_form?>" style="" value="<?=$default_value[$key];?>"/><br>
					  <?} elseif ($field_type[$key] == "combobox") {
					  ?>
                        <select id="<?=$value;?>" name="<?=$value;?>" class="el_<?=$id_form?> input_text combobox_<?=$value;?> combobox_<?=$class;?>"  size="1">           
			              <? if (is_array($default_value[$key])) {
				               foreach ($default_value[$key] as $k => $v) {?>
				                  <option value="<?=$k;?>" <? if ($v == $k) {?> selected <?}?>><?=$v;?></option>
				            <? } 
				            } else {?>
			                <option value="<?=$default_value[$key];?>"><?=$default_value[$key];?></option>
                         <? }?>
				        </select> 				  
			         <? } elseif ($field_type[$key] == "checkbox") {?>
			             <input type="check" class="el_<?=$id_form?> check_<?=$value;?> check_<?=$class;?> input_text" id="<?=$value;?>" name="<?=$value;?>" value="<?=$default_value[$key];?>"/>   			
			         <? } elseif ($field_type[$key] == "hidden") { ?>
			             <input type="hidden" class="el_<?=$id_form?> hidden_<?=$value;?> hidden_<?=$class;?>" id="<?=$value;?>" name="<?=$value;?>" value="<?=$default_value[$key];?>"/>   			
			         <? } elseif ($field_type[$key] == "listbox") { ?>
		                <select id="<?=$value;?>" name="<?=$value;?>" class="el_<?=$id_form?> input_text listbox_<?=$value;?> listbox_<?=$class;?>"  size="6">           
			            <? if (is_array($default_value[$key])) {
				             foreach ($default_value[$key] as $k => $v) {?>
				               <option value="<?=$v;?>" <? if ($v == $k) {?> selected <?}?>><?=$v;?></option>
				        <?   } 
				           } else {?>
			                <option value="<?=$default_value[$key];?>"><?=$default_value[$key];?></option>
                        <? }?>
				        </select> 				  	
			         <? } else {?>
			            <textarea type="text" class="el_<?=$id_form?> text_<?=$value;?> text_<?=$class;?> input_text" id="<?=$value;?>" name="<?=$value;?>"><?=$default_value[$key];?></textarea>   			
			         <? } 	
					  if ($i > 0) $get .="&";
                      
					  $get .= "$value=$default_value[$key]";
                      $i++;					  
					}
                ?>  <br>
                    <input type="button" id="<?=$id_form.$button_enter;?>" name="<?=$id_form.$button_enter;?>" class="<?=$options['class_button'];?>" value="<?=$options['name_submit'];?>"/>
	                <input type="button" id="clear_<?=$id_form.$button_enter;?>" name="clear_<?=$id_form.$button_enter;?>" class="<?=$options['class_button'];?>" value="Очистить"/>				
				</div>	
                <?					
                } else return "Значения титулов и данных неравны!";					
		} else return "Массивы не заполнены!";  
	}
	

	/////////// Компонент (Block) инфоблока //////////////////////////////////////////
	// 1. tag - тематический раздел (таблица или въюшки)	
	// 2. id -  код инфоблока
	/////////////////////////////////////////////////////////////////////
	
	public function Block($tag, $id)
	{
	  $Massive = Array();
	  if ((isset($id)) && (!empty($id))) { 
	     $Massive['amount'] = $this->BD_Obj->count_rec($tag, ' where (id = '.$id.')');  
         $Massive['data'] = $this->BD_Obj->select($tag, Array('*'), "(id = ".$id.")", '');
		 return $Massive;
	  }
      else return 0;  	  
	}

    ///////////  Компонент Быстрые Сообщения (FormQuickMessagePHP) ///////////////
    //   размещается в в невизуальной части до тега html 
	//   1. code_file - собственный код-обработчик файла   	
    //////////////////////////////////////////////////////////////////////////////
	public function FormQuickMessagePHP($code_file = '') {
       if (empty($code_file)) {
	    if(md5($_POST['kapcha']) != $_SESSION['randomnr2']) {// $_SESSION['rand_code']) {
         $_SESSION['Message'] = 'Капча была введена неправильна!!!'; 
	     $_SESSION['Style'] ='color: red';
        } else {
         if (($_POST['form_code']) && ($_POST['form_code'] == 'keyAddUser') &&
	        (!empty($_POST['title'])) && (!empty($_POST['name'])) && 
	        (!empty($_POST['email'])) && (!empty($_POST['text']))) {
                 $title = $_POST['title'];
	             $name = $_POST['name'];
	             $email = $_POST['email'];
	             $tel = $_POST['tel'];
		         $text = base64_encode($_POST['text']);
		         $year = $_POST['year'];
		         $long = $_POST['long'];
		         $kg = $_POST['kg'];
		
		         $url_img = '';
			
	             $fields = Array('title', 'name', 'text', 'email', 'url_img', 'tel', 'long', 'kg', 'year', 'type_message');		   
		         $values = Array($title, $name, $text, $email, $url_img, $tel, $long, $kg, $year, 'messlove');				   		   
                 $Answer = $this->BD_Obj->insert('message', $fields, $values, false);
		         $_SESSION['Message'] = 'Ваше обьявление будет промодерировано!!!';
	             $_SESSION['Style']='color: green';	  

                 unset($_POST['form_code']);
                 unset($_POST['title']);
                 unset($_POST['name']);
                 unset($_POST['email']);
                 unset($_POST['text']);
                 unset($_POST['year']);
                 unset($_POST['long']);
                 unset($_POST['kg']);				 

          } else {
           $_SESSION['Message'] = 'Не все обязательные поля заполнены!!!';
	       $_SESSION['Style']='color: red';	    
          }
        }		
	  } else {
         if (file_exists($code_file)) {
			 include_once($code_file);
		 } else {
           $_SESSION['Message'] = 'Шаблон кода не был найден!';
	       $_SESSION['Style']='color: red';	    
         }		 
      }
	}	  
		
	/////////// Компонент Быстрые сообщения (FormQuickMessage) ///////////////////
    //   1. nameform - шаблон формы.
    //   2. css - стили шаблона.	
    //////////////////////////////////////////////////////////////////////////////
    public function FormQuickMessage($nameform = 'form_answer_text.php',  $css = '') {
      if (!empty($css)) {?>
	    <link rel="stylesheet" href="<?=$css;?>" />
	  <? }
	  
	  if (empty($tpl_name)) {
		   $Cnf = new Class_Config;
	       if ($Cnf->localServer)
	         $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/'.$Cnf->FolderRoot.'/core/views/forms/'.$nameform;
	       else	 
	         $tpl_name = $_SERVER['DOCUMENT_ROOT'].'/core/views/forms/'.$nameform;  
		
	       if(!empty($tpl_name) && file_exists($tpl_name)) {
              echo file_get_contents($tpl_name);
           } else {
		      $_SESSION['Message'] = "Ошибка: файл шаблона не найден!";   
		      $_SESSION['Style'] = "color: red";   
	       }  
	    ?>
	      <p style="<?=$_SESSION['Style']; ?>"><?=$_SESSION['Message'];?></p>  
	    <?
	    }
    }	
	
	/////////// Компонент (Blogs) блоги //////////////////////////////////////////
	// 1. tag -  теги отбора блога - не таблицы и не въюшки.
	// 2. amount - количество пунктов меню.
	// 3. where - условия выбора меню.
	/////////////////////////////////////////////////////////////////////
	
	public function Blogs($tags = 'all', $amount = 5, $where = '')	
	{
      $Obj_Str = new Class_String;	  
	  if ($tags !== 'all') 
	  {
	      $TM_tags = Array();
	      $StrTags = Array();
		  $Obj_Str->Set_string($tags);
	      $StrTags = $Obj_Str->Tags_array_N();
		  
		  $i = 0;
		  $WhereTags = '';
          foreach ($StrTags as $key => $Value)		  
		  {		      
		      if (!empty($Value))
			  {
			     if ($i > 0) $WhereTags .= ' || ';
				 $WhereTags .= "(tag = '".$Value."')";	  	   			  				 
				 $TM_tags[] = $Value;
			  }	 
			  $i++;
		  }
		  
  	      $tags_amount = $this->BD_Obj->count_rec('tags', 'Where '.$WhereTags);  
          if ($tags_amount > 0)
		     $InfoTags_ = $this->BD_Obj->select('tags', Array('*'), $WhereTags, '', true);		      
          
		  $id_tags = Array();      	  
		  foreach ($InfoTags_ as $KeyInfo => $ValueInfo) $id_tags[] = $ValueInfo['id'];
	      
	  } else $id_tags = Array();
	  	  	  
	  $Massive = Array();	   	  	  
	  if ((isset($where)) && (!empty($where))) $CountWhere= 'Where '.$where;
      	  	   
	  $Massive['amount'] = 0;
	  $cblogs = $this->BD_Obj->count_rec('blogs', $CountWhere);  
      if ($cblogs > 0)
	    $Data = $this->BD_Obj->select('blogs', Array('*'), $where, '', true);      
	  
	  $i = 0;
	  if (!empty($id_tags))
	  {
	     $Massive['data'] = Array();
	     foreach ($Data as $key => $value)
	     {
	        $FindString = Array();	  
	        /*if (!empty($value['tags_blog']))  
		    {*/
		       $Obj_Str->Set_string($value['tags_blog']);	  	  
	           $FindString = $Obj_Str->Tags_array_N();
			   
			   foreach ($FindString as $ThisKey => $ThisValue)
			     foreach ($id_tags as $FindKey => $FindTags)			     
			     {
			        if ($FindTags == $ThisValue) 
					{
					   $Massive['data'][$i]['id'] = $value['id'];
					   $Massive['data'][$i]['title_blog'] = $value['title_blog'];
					   $Massive['data'][$i]['text_blog'] = $value['text_blog'];
					   $Massive['data'][$i]['text_preview_blog'] = $value['text_preview_blog'];
					   $Massive['data'][$i]['create_blog'] = $value['create_blog'];
					   $Massive['data'][$i]['creator_blog'] = $value['creator_blog'];
					   $Massive['data'][$i]['url_video_blog'] = $value['url_video_blog'];
					   $Massive['data'][$i]['url_img_title'] = $value['url_img_title'];					   
					   $i++;
					}  
			     }
				 
		    //}		    
	     }
		 
		 $Massive['tags'] = $TM_tags;
	  } 
	  else {
	     $Massive['tags'] = '';
	     $Massive['amount'] = $this->BD_Obj->count_rec('blogs', $CountWhere);  
		 $Massive['data'] = $this->BD_Obj->select('blogs', Array('*'), $where, '', true);      
	  }
	         	 	  	  
	  return $Massive;
	}
	
	/////////// Компонент (Blog) блог //////////////////////////////////////////
	// id -  номер блога.
	/////////////////////////////////////////////////////////////////////
	
	public function Blog($id)
	{
	  $Massive = Array();
	  if ((isset($id)) && (!empty($id))) { 
	    $Massive['amount'] = $this->BD_Obj->count_rec('blogs', 'Where (id = '.$id.')');  
        $Massive['data'] = $this->BD_Obj->select('blogs', Array('*'), "(id = ".$id.")", '', true);
		return $Massive;
	  }
      else return 0;  	  
	}

	/////////// Компонент (Goods) товары //////////////////////////////////////////
	// 1. tag -  теги отбора блога.
	// 2. first_rec - начальная запись вывода данных товаров.
	// 3. amount - количество пунктов записей товаров.
	// 4. where - условия выбора товаров.
	// 5. debug - режим отладки
	///////////////////////////////////////////////////////////////////////
		
	public function Goods($tags = 'all', $first_rec, $amount_rec, $where = '', $debug = false)
	{
	   $Limit = '';
	   $Massive = Array();	   
	   $WhereAmount = '';
	   $WhereData = '';
	   
	   if (!empty($where)) {
	      $WhereAmount = "Where ".$where;
		  if ($tags !== 'all') $WhereAmount .= "and (type_good ='".$tags."')";
	      $WhereData = $where;
		  if ($tags !== 'all') $WhereAmount .= "and  (type_good ='".$tags."')";
	   } 
	   else {
	      if ($tags !== 'all') $WhereAmount .= "Where (type_good ='".$tags."')";
		  if ($tags !== 'all') $WhereAmount .= "Where (type_good ='".$tags."')";
	   }
	   	   
	   if (isset($first_rec) && (isset($amount_rec)))
	   {
          $WhereAmount .= " LIMIT $first_rec, $amount_rec";
		  $Limit = "$first_rec, $amount_rec";
       }	   
	   
	   $Massive['amount'] = $this->BD_Obj->count_rec('goods', $WhereAmount, $debug);  
       $Massive['data'] = $this->BD_Obj->select('goods', Array('*'), $WhereData, '', true, $Limit, $debug);	   	   
	   return $Massive;	
	   
	}

	/////////// Компонент (Good) товар //////////////////////////////////////////
	// 1. id -  номер товара.
	/////////////////////////////////////////////////////////////////////
	
	public function Good($id)
	{
	  $Massive = Array();
	  if ((isset($id)) && (!empty($id))) {
        	  
	    $Massive['amount'] = $this->BD_Obj->count_rec('goods', 'Where (id = '.$id.')');  
        $Massive['data'] = $this->BD_Obj->select('goods', Array('*'), "(id = ".$id.")", '', true);
		return $Massive;
	  }
      else	return 0;  	
	}

	/////////// Компонент (Videos) видео //////////////////////////////////////////
	// 1. tag -  теги отбора видео (не таблицы и не вьюшки)
	// 2. amount - количество пунктов меню.
	// 3. where - условия выбора меню.
	/////////////////////////////////////////////////////////////////////
	
	public function Videos($tags = 'all', $amount = 5, $where = '')
	{
	   $Massive = Array();	   
	   if ($tags !== 'all') $ALLWhere = "(tags_blogs LIKE '%$tags%')";	  
	   $RWhere.= $ALLWhere;  
	   if ((isset($where)) && (!empty($where))) $RWhere.= $where; 
	   
	   $Massive['amount'] = $BD_Obj->count_rec('blogs', !empty($RWhere)?'Where '.$RWhere:'');  
       $Massive['data'] = $BD_Obj->select('blogs', Array('*'), $RWhere, '', true);
	   
	   return $Massive;
	}

	/////////// Компонент (Video) видео //////////////////////////////////////////
	// 1. id -  номер видео.
	/////////////////////////////////////////////////////////////////////
	
	public function Video($id)
	{
	  $Massive = Array();
	  if ((isset($id)) && (!empty($id))) { 
	    $Massive['amount'] = $BD_Obj->count_rec('blogs', 'Where (id = '.$id.')');  
        $Massive['data'] = $BD_Obj->select('blogs', Array('*'), "(id = ".$id.")", '', true);
		return $Massive;
	  }
      else	return 0;  	
	}

	/////////// Компонент (Musics) музыка //////////////////////////////////////////
	// 1. tags -  теги отбора музыки (не таблицы и не въюшки).
	// 2. amount - количество пунктов меню.
	// 3. where - условия выбора меню 
	/////////////////////////////////////////////////////////////////////
	
	public function Musics($tags = 'all', $amount = 5, $where = '')
	{
	   $Massive = Array();	   
	   if ($tags !== 'all') $ALLWhere = "(tags_blogs LIKE '%$tags%')";	  
	   $RWhere.= $ALLWhere;  
	   if ((isset($where)) && (!empty($where))) $RWhere.= $where; 
	   
	   $Massive['amount'] = $BD_Obj->count_rec('blogs', !empty($RWhere)?'Where '.$RWhere:'');  
       $Massive['data'] = $BD_Obj->select('blogs', Array('*'), $RWhere, '', true);
	   
	   return $Massive;	
	}

	/////////// Компонент (Music) музыки //////////////////////////////////////////
	// 1. id -  номер музыки.
	/////////////////////////////////////////////////////////////////////
	
	public function Music($id)
	{
	  $Massive = Array();
	  if ((isset($id)) && (!empty($id))) { 
	    $Massive['amount'] = $BD_Obj->count_rec('blogs', 'Where (id = '.$id.')');  
        $Massive['data'] = $BD_Obj->select('blogs', Array('*'), "(id = ".$id.")", '', true);
		return $Massive;
	  }
      else	return 0;  	
	}
	
	/////////// Компонент (Pictures) изображения //////////////////////////////////////////
	// 1. folder -  каталог(и) картинок 
	// 2. amount - количество пунктов меню.
	/////////////////////////////////////////////////////////////////////
	
	public function Pictures($folder = 'all', $amount = 5)
	{
	   $Massive = Array();	   
	   if ($tags !== 'all') $ALLWhere = "(tags_blogs LIKE '%$tags%')";	  
	   $RWhere.= $ALLWhere;  
	   if ((isset($where)) && (!empty($where))) $RWhere.= $where; 
	   
	   $Massive['amount'] = $BD_Obj->count_rec('blogs', !empty($RWhere)?'Where '.$RWhere:'');  
       $Massive['data'] = $BD_Obj->select('blogs', Array('*'), $RWhere, '', true);
	   
	   return $Massive;
	}

	/////////// Компонент (Picture) картинка //////////////////////////////////////////
	// 1. name -  название картинки.
	/////////////////////////////////////////////////////////////////////	
	public function Picture($name)
	{
	  $Massive = Array();
	  if ((isset($id)) && (!empty($id))) { 
	    $Massive['amount'] = $BD_Obj->count_rec('blogs', 'Where (id = '.$id.')');  
        $Massive['data'] = $BD_Obj->select('blogs', Array('*'), "(id = ".$id.")", '', true);
		return $Massive;
	  }
      else	return 0;  	
	}
	
	//////////// Компонент корзины Shop_php /////////////////////////////////////////////////////////
	// Компонент размещается в связке с компонентом Shop в довизуальной части html-кода
	//////////////////////////////////////////////////////////////////
	/*public function Shop_php() {
	 if ($_POST['keyclear_session'] = 'Y') {		 
		if ($_SESSION['session_backet']) unset($_SESSION['session_backet']);
		if ($_SESSION['goods']) unset($_SESSION['goods']);
		if ($_SESSION['const']) unset($_SESSION['const']);
	    unset($_POST['keyclear_session'], $_POST['submit_clear_session']);
	    header('Location: '.$_SERVER['HTTP_REFERER']); 
        exit;	
	 }	
	}
	*/		
	
	//////////// Компонент (Shop) ///////////////////////////////////////////////////////////////////
	// Компонент для вывода интернет-магазина для таблиц стандартизированных CMS TheSolarWind
	//
	//  1. object - объект инфоблока (товары (goods) / блоги (blogs). По умолчанию goods.
	//  2. count_str - количество товаров на странице компонента. Дефолтное значение 20.
	//  3. style - стиль интернет-магазина (таблица - table / блоки - bloks)
	//  4. type_good - вид товара (book, good, music, video). По умолчанию book.
	//  5. url_shop - шаблоная страница магазина
	//  6. url_next - шаблонная страница корзины (следующий шаг)
	//  7. where - условия дополнительные для магазина. Синтаксис sql 
	//  8.link_css - подключение таблицы стилей корзины
	//  9.tr_shop - поля и шапки вывода данных в магазине 
	//        9.1. img_good - Изображение 
	//		  9.2. name_good - Название 
	//        9.3. creator_good - Разработчик 
	//        9.4. isbn_good - Код товара, 
	//        9.5. price_good - Цена 
	//        9.6. create_good - Дата публикации
	/////////////////////////////////////////////////////////////////////////////////////////////////
	public function Shop($object = 'goods', 
	                     $count_str = 20, 
						 $style = 'table', 
						 $type_good = 'book', 
						 $mini_basket = false,	
						 $url_shop = 'index.php?url=shop&page=',
                         $url_next = 'index.php?url=basket',						              					 
						 $where = '',
						 $link_css = 'core/views/admincss/see_admin_tables.css',
						 $tr_shop = Array('img_good' => 'Изображение', 
						                  'name_good' => 'Название', 
										  'creator_good' => 'Разработчик', 
										  'isbn_good' => 'Код товара', 
										  'price_good' => 'Цена', 
										  'create_good' => 'Дата публикации')
						)	
    {
		?>		
		<link rel="stylesheet" type="text/css" href="core/views/admincss/see_buttons_css.css"/>
		<link rel="stylesheet" type="text/css" href="<?=$link_css;?>"/>
		<?
		if (($object !== 'goods') && ($object !== 'blogs')) {
		   exit;			
		   return "Error! Ошибка загрузки данных для компонента shop!";
		} else {
		   if (($type_good) && (!empty($type_good))) $_SESSION['type_good'] = $type_good;
           else $_SESSION['type_good'] = 'book'; 		   
           if (($style !== 'table') || ($style !== 'bloks')) 
			 $style = 'table';
		 ?>
		  <script>
              $(document).ready(function() {
			     $('.add_backet').click(function(){
                     var $name_id = (this).id; 
					  
					 $.get("core/models/ajax/add_backet.php", 
					        {id: $name_id, type_good: "<?=$object;?>"}
						  ).done(
					       function(data) {
                             console.log( "Data Loaded: " + data.parseJSON);
                           });	   
			      });
		     });			 
		 </script>
		 <? if ($mini_basket) { ?>
		 <table id="mini_backet" class="table_blur" style="float: right;">
		   <tr>
		      <th>Товары корзины:</th>
			  <th>Стоимость:</th>
		   </tr>
		   <tr>
		      <td id="count_goods">0</td>
			  <td id="cost_goods">0</td>
		   </tr>
		 </table><br>		 
		 <?
		 }
		 
		   // стиль таблицы-магазина
		   if ($style == 'table') {
			 ?>			
             <table id="shop_table" class="table_blur">
			  <tr>
			    <? foreach ($tr_shop as $key_ => $val_) {?>
				<th><?=$val_; ?>
                <? } ?>
				<th>В корзину
			  </tr>
             <?	                      
			    if (empty($_GET['page'])) $_GET['page'] = 1;
			                                 //   1   2  3            4                               5                        6              7   		  			    
				$Goods = $this->Blocks('goods', 0, $count_str, "type_good = '".$type_good."'", "sort desc, create_good", $_GET['page'], $url_shop);
                if (count($Goods['data']) < 1) {
                ?>
                <tr>
			      <? foreach ($tr_shop as $val_) {?>
				  <td>-
                  <? } ?>
				  <td>-
			  </tr>
                <?
                }
				
			    foreach ($Goods['data'] as $key => $item) {
				?>                  
                   <tr>
                <?					
				   foreach ($tr_shop as $key_ => $val_) 
				   { 
				     ?><td>
					 <?
                        if ($key_ == 'img_good') {
                        ?>
                          <img class="img-responsive img-border" alt="<?=$item['name_good'];?>" src="<?=$item[$key_]?>" style='width: 360px;'/>					
						<? }
                        elseif ($key_ == 'name_good') {
						?>	
					      <h5><?=$item['name_good'];?></h5>   
					    <?
						}
						else {                        							
						?>
                          <p><?=$item[$key_];?></p>
                        <?
                        }
                        ?>
                        </td>						 
			        <?			   
			       } 
				   ?>
				       <td>
					      <button class="add_backet push_button1 red1" code_id="t<?=$item['id'];?>" id="<?=$item['id'];?>" style="width: 100px !important;">Добавить</button>
					   </td>
				   </tr>
				  <? 
				 } 
			        ?>
                </table>				  		
			    <div><center><?=$Goods['paggi']; ?></center></div>	<!-- паггинация -->			   		 
             <?			 
		   } // конец табличного стиля 		   
           elseif ($style == 'bloks') { // стиль блоков
             ?>
			 <div id="shop_bloks">
			 <?
                foreach ($tr_shop as $key_ => $val_) {?>
				<div id="shop_header"><?=$key_; ?>
                <? } ?>	
                </div>
				<?
				$Goods = $Components->Blocks('goods', 
				                             0, 
				                             $count_str, 
											 "type_good = '".$type_good."'", 
				                             "sort desc, create_good", 
											 $_GET['page'], 
											 $url_shop);
											 
			    foreach ($Goods['data'] as $key => $item) {
				?>                  
                   <div class="">
                <?					
				   foreach ($tr_shop as $key_ => $val_) 
                   { ?><div>
					 <?
                        if ($key_ == 'img_good') {
                        ?>
                          <img class="img-responsive img-border" alt="<?=$item['name_good'];?>" src="<?=$item[$key_]?>" style='width: 360px;'/>					
						<? }
                        elseif ($key_ == 'name_good') {
						?>	
					      <h2><?=$item['name_good'];?></h2>   
					    <?
						}
						else {                        							
						?>
                          <p><?=$item[$key_];?></p>
                        <?
                        }
                        ?>
                       </div>						 
			        <?	
				   }
                }				   
                ?>
                   </div>			
			 </div>
			 <div><center><?=$Goods['paggi']; ?></center></div>
			 <?
           } ?>
          <form id="Next" action="<?=$url_next;?>" method="POST">
		    <input type="hidden" id="keyclear_session" name="keyclear_session" value="Y"/>
			<input class="push_button1 red1" type="submit" id="submit_clear_session" name="submit_clear_session" value="Перейти в корзину"/>
		  </form>
        <?		   
        }		
	}	
 
    /////////// Компонент (AjaxFilesLoad) загрузки ajax-данных //////////////////////////////////////
	// 1. catalog_load - папка загрузки данных
    // 2. ajax_file - исполняемый файл ajax загрузки.
    /////////////////////////////////////////////////////////////////////////////////////////////////
    public function AjaxFilesLoad($catalog_load = 'upload', $ajax_file = 'upload.php') {
      ?>	
		<link href="assets/css/style_r.css" rel="stylesheet" />
		<style>
          #upload ul li {
	             height: 100px !important; 
          }	  
        </style>
		<form id="upload" method="post" action="<?=$ajax_file;?>?folder=<?=$catalog_load;?>" enctype="multipart/form-data" style="width: 100% !important;">
			<div id="drop">
				Область ajax-загрузки данных
				<a>Файлы</a>
				<input type="file" name="upl" multiple />
			</div>
			<ul>
				<!-- The file uploads will be shown here -->
			</ul>
		</form>
		<script src="js/jquery.js"></script>
		<script src="assets/js/jquery.knob.js"></script>
		<script src="assets/js/jquery.ui.widget.js"></script>
		<script src="assets/js/jquery.iframe-transport.js"></script>
		<script src="assets/js/jquery.fileupload.js"></script>
		<script src="assets/js/script.js"></script>
		<script src="http://cdn.tutorialzine.com/misc/enhance/v1.js" async></script>	
      <?	  
    }
  
    //////////// Платежные системы PayPal - компонент оплаты сделки ///////////////////////////////////////////
	// Компонент для осуществления сделки через разные платежные системы.
	//    1. goods - по-умолчанию товары загружаются в корзину (goods). Альтернатива blogs
	//    2. url_back - директория-кнопка возврата назад в корзину. Дефолтный адрес index.php?url=basket
	//    3. link_css - таблица стилей для таблицы и кнопки. Дефолтный адрес core/views/admincss/see_admin_tables.css
	//    4. money - валюта сделки. Дефолтное значение "руб". Таблицы валют записаны в классе валют. (MoneyClass). 
	//
	////////////////////////////////////////////////////////////////////////////////////////////////////
    public function PayPal($goods = 'goods', 
	                       $url_back = 'index.php?url=basket',
  						   $link_css = 'core/views/admincss/see_admin_tables.css',
						   $money = 'руб') {
		?>
    	<link rel="stylesheet" type="text/css" href="core/views/admincss/see_buttons_css.css"/>
		<link rel="stylesheet" type="text/css" href="<?=$link_css;?>">
		<h5>Сделка №<?=$_SESSION['session_backet'];?></h5>		
		<?
		  $i = 0; 
		  $allcost = 0; 
		  foreach ($_SESSION['goods'] as $key => $val) {
			$i += $val; 
			$allcost += $val*$_SESSION['cost'][$key];
		  }	
		?>
		<p>В корзине приобретений у вас находится <span style="color: blue"><?=$i;?></span> товаров стоимостью в <span style="color: red"><?=$allcost;?> <?=$money;?></span>.</p>
        <h5>Способы оплаты сделки:</h5>
		<form action="success_paypal.php" method="POST">
		<table class="table_blur">
            <tr>
              <th></th>
			  <th></th>
			</tr>
          <tr>
            <td><img src="img/pay_webmoney.png" style="width: 150px; height: 100px;"/>		  
		    <td><input type="radio" name="form_pay" value="WebMoney"/>Веб-мани 
		  </tr>
		  <tr>
		    <td><img src="img/pay_robokassa.jpg" style="width: 150px; height: 100px;"/>		  
		    <td><input type="radio" name="form_pay" value="Robokassa"/>Робокасса
		  <tr> 
            <td><img src="img/pay_sms.jpg" style="width: 150px; height: 100px; "/>		  	  
			<td><input type="radio" name="form_pay" value="SMSpay"/>СМС-сообщение 
		  	
		</table>		
        <a href="<?=$url_back;?>" class="push_button1 red1" style="float:left;">В корзину</a>	
        <input class="push_button1 red1" style="margin: 20px 5px 0px 0px;" type="submit" id="submit_paypal" name="submit_paypal" value="Оплатить сделку"/>
		</form>
		<div style="clear: both"></div>		
		<? 		
	}	
  
	/////////// Невизуальный компонент (Backet_php) корзина интернет-магазина //////////////////////
	// Размещается в контенте до html-кода
	//  1. session_id - код сессии сделки
	////////////////////////////////////////////////////////////////////////////////////////////////////
	public function Backet_php($session_id = '') {   
	 if ((empty($session_id)) || (!isset($session_id)))   
		$session_id = $_SESSION['session_backet'];
     
	 if (!isset($_SESSION['type_good']) || (!empty($_SESSION['type_good'])))
		 $_SESSION['type_good'] = 'book';
	 
	 if (($_GET['deleteid']) && (!empty($_GET['deleteid']))) {
            if ($_SESSION['goods'][$_GET['deleteid']]) unset($_SESSION['goods'][$_GET['deleteid']]);
            if ($_SESSION['cost'][$_GET['deleteid']]) unset($_SESSION['cost'][$_GET['deleteid']]);
		}

      if ((isset($_GET['operation'])) && ($_GET['operation'] == 'plus')) {
         if ($_GET['editid']) {				           
			if ($_SESSION['goods'][$_GET['editid']]) 
				$_SESSION['goods'][$_GET['editid']] = $_SESSION['goods'][$_GET['editid']] + 1;
			
			//$count_element = (int)$_GET['count'] + 1;
			//$fields = Array('count_good');			
		    /*$values = Array($count_element);
			$where = "(`code_good` = ".$_GET['editid'].") and (`session_id` = ".$session_id.")";            
			if ($this->BD_Obj->count_rec('shop', "Where ".$where) > 0) {
			   echo $this->BD_Obj->update('shop', $fields, $values, $where);
			} else {
               $fields_ins = Array('code_good','session_id','count_good');
               $values_ins = Array($_GET['editid'], $session_id, $count_element);  			   
               $this->BD_Obj->insert('shop', $fields_ins, $values_ins, $where_ins);
			   unset($_GET['editid']);
			   header('Location: '.$_SERVER['HTTP_REFERER']); 
               exit; 
            }
            */			
		 }	 
      } 

      if ($_GET['operation'] == 'minus') {
         if (($_GET['editid']) && (!empty($_GET['editid']))) {            
		  if ($_GET['editid']) {				           
			if ($_SESSION['goods'][$_GET['editid']]) {
				$val = $_SESSION['goods'][$_GET['editid']] - 1;
				if ($val < 1) 
				   $_SESSION['goods'][$_GET['editid']] = 1;
			    else
				   $_SESSION['goods'][$_GET['editid']] = $val;
			}
		  }	
			/*$fields = Array('count_good');	
            $count_element = (int)$_GET['count'] - 1;
            if ($count_element > 0) {		
		      $values = Array($count_element);
			  $where = "((`code_good` = ".$_GET['editid'].") and (`session_id` = ".$session_id."))";
			  echo $this->BD_Obj->update('shop', $fields, $values, $where, true);
			  
			  unset($_GET['editid']);
			  header('Location: '.$_SERVER['HTTP_REFERER']); 
              exit; 
			}
            */			
         }	
      }  
	}	

    /////////// Компонент (Backet) корзина интернет-магазина (запись данных в сессию) /////////////////////////////////////// 
	// Компонент формирования корзины для интернет-магазина  
	//  1. goods - по-умолчанию товары загружаются в корзину (goods). Альтернатива blogs
    //  2. session_id - сессия сделки-покупки.
    //  3. where - условия компонента корзины.
    //  4. url_back - страница в магазин (назад)
	//  5. url_next - страница в платежную систему (вперед)
	//  6. directory - страница корзины 
	//  7. link_css - стили подключения к компоненту корзины.
    //	
	/////////////////////////////////////////////////////////////////////////////////////////////////	
 	
	public function Backet($goods = 'goods',
	                                  $session_id = '', 
	                                  $where ='', 
									  $url_back = 'index.php?url=shop',
									  $url_next = 'index.php?url=payment',
							          $directory = 'index.php?url=basket',
							          $link_css = 'core/views/admincss/see_admin_tables.css') {
        
		function ClearTable($url_back) {
		?>
		    <tr>
              <td>-</td>
              <td>-</td>
	          <td>-</td>
              <td>-</td>
              <td>-</td>
	          <td>-</td>
           </tr>
		   </table>
		   <a href="<?=$url_back;?>" class="push_button1 red1" style="float:left;">В магазин</a>
           <div style="clear: both"></div>
        <?		
		}	
		if ((empty($session_id)) || (!isset($session_id))) 
			$session_id = $_SESSION['session_backet'];
		if (empty($directory))
          $directory = $_SERVER['REQUEST_URI'];	
		?>
		  <link rel="stylesheet" type="text/css" href="core/views/admincss/see_buttons_css.css"/>
		  <link rel="stylesheet" type="text/css" href="<?=$link_css;?>">
		  <table class="table_blur">
            <tr>
              <th>№</th>
              <th>Название товара</th>
	          <th>Стоимость</th>
              <th>Кол-во</th>
              <th>Все цена</th>
	          <th>Del</th>
           </tr>
		<?
		
		if (($goods !== 'goods') && ($goods !== 'blogs')) {
		?>
           <p style="color: red">Ошибка: К корзине не подключены ни товары (goods), ни блоги (blogs)!</p>
        <?		
			ClearTable($url_back); 	
			return false;
  	    } else {
          if ((empty($session_id)) || (strlen($session_id) < 6))	{
        ?>
           <p style="color: red">Ошибка: Код сессии не установлен в базе данных!</p>
           <?		
			  ClearTable($url_back);
			  return false;
           }
        ?>         		
		<?
   		if (($_SESSION['goods']) && (!empty($_SESSION['goods'])) &&
			($_SESSION['cost']) && (!empty($_SESSION['cost']))) {
			$i = 0;	
			  foreach ($_SESSION['goods'] as $key => $val) {
			   $i++;
			   $where = "id=$key";
			   $price = $this->BD_Obj->get_field("$goods", 'price_good', $where);
			   ?>
                <tr id="good<?=$key;?>">
                     <td><p><?=$i;?></p></td>
					 <td><p><?=$this->BD_Obj->get_field("$goods", 'name_good', $where);?> {<?=$key;?>}</p></td>
					 <td><p class="price"><?=$price;?></p></td>
					 <td>
					     <a href="<?=$directory;?>&operation=plus&editid=<?=$key;?>&count=<?=$val;?>">
						  <img class="ajax_up" field_id="count_<?=$key;?>" src="img/up_str.png" style="width:16px"><br>
					     </a> 
						  <p class="count_good" id="good<?=$key;?>" style="margin: -2px 0px 0px 2px;">
						     <?=$val;?>
						  </p>
					     <a href="<?=$directory;?>&operation=minus&editid=<?=$key;?>&count=<?=$val;?>">
						   <img class="ajax_down" src="img/down_str.png" style="width:16px">
						 </a>  
					 </td>
					 <td><p class="cost"><?=$price*$val;?></p></td>
					 <td><p class="delete"><a href="<?=$directory;?>&deleteid=<?=$key;?>">Удалить</a></p></td>
                </tr>	
               <?			   
			  }	
            ?>				
			</table>
			<a href="<?=$url_back;?>" class="push_button1 red1" style="float:left;">В магазин</a>
			<form id="NextBasket" action="<?=$url_next;?>" method="POST">
		      <input type="hidden" id="keyclear_basket" name="keyclear_basket" value="Y"/>
			  <input class="push_button1 red1" style="margin: 20px 5px 0px 0px;" type="submit" id="submit_basket" name="submit_basket" value="Перейти к оплате"/>
		    </form>
			<div style="clear: both"></div>
			<?
		  } else ClearTable($url_back);
		}			   
	}									  
	
    /////////// Компонент (BasketShop) корзина интернет-магазина (запись данных в базу данных) /////////////////////////////////////// 
	// Компонент формирования корзины для интернет-магазина
	//  1. goods - по-умолчанию товары загружаются в корзину (goods). Альтернатива blogs
    //  2. session_id - сессия сделки-покупки.
    //  3. where - условия компонента корзины.
	//  4. directory - адрес корзины интернет-магазина.
    //  5. link_css - стили подключения к компоненту корзины.
	/////////////////////////////////////////////////////////////////////////////////////////////////	
    public function BasketShopBackEnd($goods = 'goods',
	                                  $session_id = '', 
	                                  $where ='', 
							          $directory = 'index.php?url=basket',
							          $link_css = 'core/views/admincss/see_admin_tables.css') {
        if ((empty($session_id)) || (!isset($session_id))) 
			$session_id = $_SESSION['session_backet'];
		if (empty($directory))
          $directory = $_SERVER['REQUEST_URI'];
         	  
		?>
		  <link rel="stylesheet" type="text/css" href="<?=$link_css;?>">
		<?
		
		if (($goods !== 'goods') && ($goods !== 'blogs')) {
		?>
           <p style="color: red">Ошибка: К корзине не подключены ни товары (goods), ни блоги (blogs)!</p>
        <?		
			exit;
			return false;
  	    }

        if ((empty($session_id)) || (strlen($session_id) < 6))	{
        ?>
           <p style="color: red">Ошибка: Код сессии не установлен в базе данных!</p>
        <?		
			exit;
			return false;
        }
	
		$Prefic = $this->Conf_Obj->pref;
		if ($goods =='goods') 
          $sql_procedure = "
                           SELECT distinct(`name_good`), count_good, g.id as uid, g.*, s.* 
                           FROM `".$Prefic."goods` g, `".$Prefic."shop` s 
                           WHERE (g.`id` = s.`code_good`) and (s.`session_id` = ".$session_id.") and (s.`type_operation` =  'goods') $where
                           GROUP BY `name_good` 
                           ORDER BY `name_good` ASC;
                         ";
        else 
		  $sql_procedure = "
                           SELECT distinct(`title`), count_good, g.id as uid, g.*, s.* 
                           FROM `".$Prefic."blogs` g, `".$Prefic."shop` s 
                           WHERE (g.`id` = s.`code_good`) and (s.`session_id` = ".$session_id.") and (s.`type_operation` =  'blogs') $where
                           GROUP BY `title` 
                           ORDER BY `title` ASC;
                         ";	
						 
		$Data_record = $this->BD_Obj->simple_select($sql_procedure);		
		?>
        <table class="table_blur">
         <tr>
           <th>№</th>
           <th>Название товара</th>
	       <th>Стоимость</th>
           <th>Кол-во</th>
           <th>Все цена</th>
	       <th>Del</th>
        </tr>
        <?
		  if (!empty($Data_record)) {
			if ($goods =='goods')  {
        	   foreach ($Data_record as $key => $value) {
		          ?>
				  <tr id="good<?=$key;?>">
                     <td><p><?=$key; ?></p></td>
					 <td><p><?=$value['name_good'];?></p></td>
					 <td><p class="price"><?=$value['price_good'];?></p></td>
					 <td>
					     <a href="<?=$directory;?>&operation=plus&editid=<?=$value['uid'];?>&count=<?=$value['count_good'];?>">
						  <img class="ajax_up" field_id="count_<?=$key;?>" src="img/up_str.png" style="width:16px"><br>
					     </a> 
						  <p class="count_good" id="" style="margin: -2px 0px 0px 2px;">
						     <?=$value['count_good'];?>
						  </p>
					     <a href="<?=$directory;?>&operation=minus&editid=<?=$value['uid'];?>&count=<?=$value['count_good'];?>">
						   <img class="ajax_down" src="img/down_str.png" style="width:16px">
						 </a>  
					 </td>
					 <td><p class="cost"><?=$value['price_good']*$value['count_good'];?></p></td>
					 <td><p class="delete"><a href="<?=$directory;?>&deleteid=<?=$value['uid'];?>">Удалить</a></p></td>
                  </tr>				  
				  <?
		          //print_r ($value);              100%
		        }	
            } elseif ($goods =='blogs') {
                  foreach ($Data_record as $key => $value) {
				  ?>
				  <tr id="blog<?=$key;?>">
                     <td><p><?=$key; ?></p></td>
					 <td><p><?=$value['title'];?></p></td>
					 <td><p class="price"><?=$value['price'];?></p></td>
					 <td>
					     <a href="<?=$directory;?>&operation=plus&editid=<?=$value['uid'];?>">
						  <img class="ajax_up" field_id="count_<?=$key;?>" src="img/up_str.png" style="width:16px"><br>
					     </a> 
						 <p class="count_good" style="margin: -2px 0px 0px 2px;">
						     <?=$value['count_good'];?>
						 </p>
					     <a href="<?=$directory;?>&operation=minus&editid=<?=$value['uid'];?>">
						   <img class="ajax_down" src="img/down_str.png" style="width:16px">
						 </a>  
					 </td>
					 <td><p class="cost"><?=$value['price']*$value['count_good'];?></p></td>
					 <td><p class="delete"><a href="<?=$directory;?>&deleteid=<?=$value['uid'];?>">Удалить</a></p></td>
                  </tr>				  
				  <?}
            }			
		  } else {
				?>
				  <tr>
                     <td><p>-</p></td>
					 <td><p>-</p></td>
					 <td><p>-</p></td>
					 <td><p>-</p></td>
					 <td><p>-</p></td>
					 <td><p>-</p></td>
                  </tr>				  
				<?
			}?>
          </table>
          <?		  		  	   
    }	
 
    /////////// Компонент (FormEmailSubscription) подписки емайла /////////////////////////////////////// 
	// Компонент формы подписки идет в паре с компонентом FormEmailSubscription_php
	//  1. group_subscribe - код группы подписки, по умолчанию, 1.  	
	//  2. tpl_name - шаблон форма email-подписки 
	/////////////////////////////////////////////////////////////////////////////////////////////////
    public function FormEmailSubscription($group_subscribe = 1, $tpl_name = 'form_subscribe.php') {?>
        <script>
		    $(document).ready(function() {
			  $('#form_subscription').submit(function(){

				if($('#email_subscription').val() != '') {
					var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;					
				
					if(pattern.test($('#email_subscription').val())){
						$('#email_subscription').css({'border' : '2px solid #569b44'});
						$('.message').text('Вы успешно подписались!');
						return true;
					} else {
						$('#email_subscription').css({'border' : '1px solid #ff0000'});
						$('.message').text('Формат email был неверным');
						return false;
					}
				} else {
					$('#email_subscription').css({'border' : '2px solid #ff0000'});
					$('.message').text('Поле email не должно быть пустым');
					return false;
				}
			  });
		   });
	</script>
    <?	
         $_SESSION['Message'] = '';
		 if (($_POST['key_sub']) && ($_POST['key_sub'] == 'keySubmit')) {
	     if (($_POST['email_subscription']) && (!empty($_POST['email_subscription']))) {
		   $fields = Array('email', 'group_subscribe');		   
		   $values = Array($_POST['email_subscription'], $group_subscribe);

		   $Count_data = $this->BD_Obj->count_rec('subscription', "Where (email = '".$_POST['email_subscription']."')");  
		   $Selected = $this->BD_Obj->select('subscription', Array('*'), "(email = '".$_POST['email_subscription']."')", '', true);
	      
		   if ($Count_data < 1) {             
              if (1 == preg_match('/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-0-9A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u', $_POST['email_subscription']) . '<br/>'){			 
			    $Answer = $this->BD_Obj->insert('subscription', $fields, $values, false);
		        $_SESSION['Answer'] = $Answer; 
                if (($Answer) && ($Answer > 0))
                  $_SESSION['Message'] = "Вы успешно подписались! На почтовый ящик ".$_POST['email_subscription']." будем высылать вам сообщения!"; 
              } else {
				  $_SESSION['Message'] = 'Извините, но ваш формат Email не соответствует стандартам!';  
			  }	 
             //unset($_POST['email_subscription']);
             //unset($_POST['key_sub']);
		   } 
		   else 
			 $_SESSION['Message'] = "Вы состоите в группе подписки! Почта ".$_POST['email_subscription']." в базе данных"; 
			   	   		   
	     } else {
          $_SESSION['Message'] = "Вам не удалось подписаться!";
        }
	
		//return $Selected;
	   }?>
	    <style>
		   #email_subscription {
			    width: 250px !important;
                margin-bottom: 15px;				
		   }	   
		</style>
		<?
		   $tpl_name = "core/views/forms/".$tpl_name;
		   if(!empty($tpl_name) && file_exists($tpl_name)) 
              include_once($tpl_name);
			  //echo file_get_contents($tpl_name);
            else $_SESSION['Message'] = "Ошибка: файл шаблона не найден!";

		?>
	    
       <?	   
    }
	
	/////////// Компонент (FilesMultiCatalog) каталога картинок //////////////////////////////////////////
	// 1. folder -  каталог картинок.
	// 2. count_items - количество вывода данных картинок в компоненте.
	// 3. style - стиль селектора-компонента.
	// 4. input_field - имя input для вывода url-картинки
	// 5. default_value - значение по-умолчанию.
	// 6. root - текущая директория 
	/////////////////////////////////////////////////////////////////////////////////
	
	public function FilesMultiCatalog($folder = 'upload', 
	                                  $count_items = 5, 
									  $style='', 
									  $input_field = 'namefile', 
									  $default_value, 
									  $root = false)
	{ 
       $Class_Files = new Class_Files;
       $Class_Files->Path = $folder;
       
	   $Class_Files->TagList = 'option';	   	   
	   $FirstFolder = Array();
	   $Catalogs = Array();
	   
	   $FirstFolder[] = "upload";
	   $Catalogs = $this->Files->ScanDirCatalog("upload"); 
	   
	   foreach ($Catalogs['data'] as $key=> $value) 
		  $Catalogs['data'][$key] = "upload/".$value;   
	   ?>
	   
	   <script>
			     $(document).ready(function() 
                 { 
                     $("#id_catalog").change(function(){      	    	       
		                 var $path;
						 var $url_first = $(location).attr('href');
					     var $lenght_true = $(location).attr('href').indexOf("current_catalog");
					     var $url_new 
						 
						 if ($lenght_true > 0)
						   $url_new = $url_first.substring(0, $lenght_true - 1);
					     else 
						   $url_new = $url_first;
					   
						 console.log($url_first); 
					     console.log($url_new);		 			 
	                     console.log($(this).val());
						 $url_new += "&current_catalog="+$(this).val();
		                 $("#newcatalog").html("Переход в "+$(this).val());
						 $("#newcatalog").attr('href', $url_new);
		             }); 
				 });				 
	  </script>
		
	   <hr>
	   <p class='left_text'>Каталоги папок (upload):</p>
	   <?
       echo $this->ComboboxString(array_merge($FirstFolder, $Catalogs['data']), 
			                                $folder, 
											'id_catalog', 
											'input_text2');
       ?>											
	   	<a id="newcatalog" name="newcatalog" class='push_button1 red1' style='float: right; width: 330px;' href='admin.php?url=editblog&tags=<?=$_GET['tags']?>&id=<?=$_GET['id']?>&current_catalog=<?=$folder;?>'>Переход в ...</a>  
		<div style="clear: both"></div>
		
       <p class='left_text'>Текущий каталог - <?=$folder;?></p>
       <?		
	   if ($root) $RootDir = $this->getRoot();
	   else $RootDir = '';
	   ?>	
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>	   
	    <script>
         $(document).ready(function() 
         { 	
           var $path;		
		   
		   <? if (empty($default_value)) {?>
		   $path = '<?=$RootDir;?>img/ClearPicture.png';
		   console.log($path);
	       $("#preview").attr('src', $path);
           $("#<?=$input_field;?>").attr('value', $path);	
		   <? } ?>
		   
		   $("#BackgroundColor").change(function(){      	    	       
		   var $path;
		   var $point = -1;
		   
		   if($(this).val() == 0) return false;      
	       console.log($(this).val());
		   if ($(this).val() == 'Без картинки')
		     $path = '<?=$RootDir;?>img/ClearPicture.png';
		   else  
		     $path = '<?=$RootDir.$folder;?>/'+$(this).val();
		   
		   $point = $path.indexOf(".");
		   
		   if ($point < 0) $path = '<?=$RootDir;?>img/folder-icon.png';	 
			 
		   console.log($path);
		   console.log('Начинаем передавать данные!');
	       $(".preview").attr('src', $path);
		   console.log('Картинка-путь: '+$path);
           $("#<?=$input_field;?>").attr('value', $path);		   
          });
	     });
        </script>	 
			    
		<img src="<?=$default_value;?>" id='preview' class="preview" name='preview' style='width: 100px; float: left; margin-right: 5px;'/>
		<select class='selects <?=$style;?>' id ='BackgroundColor' name='BackgroundColor' size='<?=$count_items;?>'>
		  <option>Без картинки</option>
		  <?
		    echo $Class_Files->ScanDirList($default_value);			
		  ?>		  
		</select>           
        <div style="clear: both;"></div>		
		<!--<p style="float: left;">Файл-картинка:</p>-->				
		<input type="text" class="<?=$style;?>" name="<?=$input_field;?>" id="<?=$input_field;?>" style="width: 370px; float: left;" value="<?=$default_value;?>" />		
	    <div style="clear: both;"></div>
		<hr>
	  <?		    	   
	}
	
	/////////// Компонент (FilesCatalog) каталога картинок //////////////////////////////////////////
	// 1. folder -  каталог картинок.
	// 2. count_items - количество видимых элементов в компоненте
	// 3. style - классы-стили
	// 4. input_field - поле вывода картинки
	// 5. default_value - дефолтное значение 
	// 6. id_suff - суффикс компонента
	// 7. root - домашняя корень-директория 
	/////////////////////////////////////////////////////////////////////////////////
	
	public function FilesCatalog($folder = 'upload', 
	                             $count_items = 5, 
								 $style='', 
								 $input_field = 'namefile', 
								 $default_value, 
								 $id_suff = '', 
								 $root = false)
	{ 
       $Class_Files = new Class_Files;
       $Class_Files->Path = $folder;
       $Class_Files->TagList = 'option';	   
	   
	   if ($root) $RootDir = $this->getRoot();
	   else $RootDir = '';
	   ?>	
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>	   
	    -->
		<script src="js/jquery.js"></script>
		<script>
         $(document).ready(function() 
         { 	
           var $path;		
		   
		   <? if (empty($default_value)) {?>
		   $path = '<?=$RootDir;?>img/ClearPicture.png';
		   console.log($path);
	       $("#preview<?=$id_suff;?>").attr('src', $path);
           $("#<?=$input_field.$id_suff;?>").attr('value', $path);	
		   <? } ?>
		   
		   $("#BackgroundColor<?=$id_suff?>").change(function(){      	    	       
		   var $path;
		   var $point = -1;
		   
		   if($(this).val() == 0) return false;      
	       console.log($(this).val());
		   if ($(this).val() == 'Без картинки')
		     $path = '<?=$RootDir;?>img/ClearPicture.png';
		   else  
		     $path = '<?=$RootDir.$folder;?>/'+$(this).val();
		   
		   $point = $path.indexOf(".");
		   
		   if ($point < 0) $path = '<?=$RootDir.$folder;?>img/folder-icon.png';	 
			 
		   console.log($path);
		   console.log('Начинаем передавать данные!!!!!!!!!!!');
	       $("#preview<?=$id_suff?>").attr('src', $path);
		   console.log('Картинка-путь: '+$path);
           $("#<?=$input_field.$id_suff;?>").attr('value', $path);		   
          });
	     });
        </script>	 
			    
		<img src="<?=$default_value;?>" id='preview<?=$id_suff;?>' class="preview" name='preview<?=$id_suff;?>' style='width: 100px; float: left; margin-right: 5px;'/>
		<select class='selects <?=$style;?>' id ='BackgroundColor<?=$id_suff;?>' name='BackgroundColor<?=$id_suff;?>' size='<?=$count_items;?>'>
		  <option>Без картинки</option>
		  <?
		    echo $Class_Files->ScanDirList($default_value);			
		  ?>		  
		</select>           
        <div style="clear: both;"></div>		
		<!--<p style="float: left;">Файл-картинка:</p>-->				
		<input type="text" class="<?=$style;?>" name="<?=$input_field.$id_suff;?>" id="<?=$input_field.$id_suff;?>" style="width: 370px; float: left;" value="<?=$default_value;?>" />		
	  <?		    	   
	}
	
	/////////// Компонент (TreeFilesCatalog) дерева файлов и каталогов //////////////////////////////////////////
	// 1. root_dir - директория-корень.
	// 2. folder -  каталог.
	// 3. where - условия выбора баннеров.
	/////////////////////////////////////////////////////////////////////
	
    public function TreeFilesCatalog($root_dir, $folder = 'upload', $where = '')
	{
       $Catalog = ''; //Array(); //Array('category' => Array());	   
	   $Class_Files = new Class_Files; 
	   $Arr = $Class_Files->ScanDirFilesAndFolder($folder);
	   $Catalog .= "<div class='catalog'>";
	   $Catalog .= "";
	   foreach ($Arr['files'] as $key => $value)
	   {
	       //$Catalog .= $value."</br>";
		   if ($value[$key] == '.') 
		     $Catalog .= '<div><img src="img\commander\Cfolder.png"/></div><br/>'; //$Arr['category'][$key] = 'level';
           else		   
		   if ($value[$key] == '..') $Catalog .= '<div><img src="img\commander\folder_2.png"/></div><br/>';  //$Arr['category'][$key] = 'level';
		   else
		   {		       
			   $position = mb_strpos(trim($value), '.', 0, 'UTF-8');
			   //$position = strpos(trim($value), '.');			   
			   if (empty($position))
			     $Catalog .= '<div style="margin-left: 17px;"/><img src="img\commander\folder.png"/> '.$value.' </div><br/>';
			   else
			     $Catalog .= '<div style="margin-left: 17px;"/><a href="index.php?url=test&img_see='.$value.'"><img src="img\commander\file.png"/> '.$value.'</a> </div><br/>';      			   
               //$Arr['category'][$key] = '';
		   }	 
	   }
	   $Catalog .= "</div>";
	   return $Catalog; //$Arr;	   
	}
	
	/////////// Компонент (Banners) баннер //////////////////////////////////////////
	// 1. folder - каталог баннеров. 
	// 2. amount - количество пунктов баннеров.
	// 3. banners - условия выбора баннеров.
	// 4. protocol - протокол 
	/////////////////////////////////////////////////////////////////////
	
	public function Banners($folder = 'all', 
	                        $amount = 5, 
							$banners = 'banners/', 
							$protocol = "https") 
	{
	   $Massive = Array();	   
	   $Cnf = new Class_Config;
	   if ($Cnf->localServer)
	     $Path = $_SERVER['DOCUMENT_ROOT'].'/'.$Cnf->FolderRoot.'/'.$banners;
	   else	 
	    $Path = $_SERVER['DOCUMENT_ROOT'].'/'.$banners;
	   	   
	   $DFiles = new Class_Files;
	   if ($folder == 'all') {
	      //$Temp = $DFiles->ScanDirFolder($Path);		  	   
		  $Temp = $DFiles->ScanDirFilesAndFolder($Path);		  
		  foreach ($Temp['files'] as $val) {
		   $Massive[] = $val;
		  }		  
	   }	  
       else { 
          //$Massive = $DFiles->ScanDirFolder($Path."$folder");	         
		  $Temp = $DFiles->ScanDirFolder($Path."$folder");		  	   
		  foreach ($Temp[2] as $val) {
		    $Massive[] = $val;		  
		  }	
	  }	  
          if ($Cnf->localServer)
		    $Massive['folder'] = "$protocol://".$_SERVER['SERVER_NAME'].'/'.$Cnf->FolderRoot.'/';    		  
		  else
            $Massive['folder'] = "$protocol://".$_SERVER['SERVER_NAME'].'/';    		  
		  
	   return $Massive;	
	}

	/////////// Компонент (Banner) баннер //////////////////////////////////////////
	// 1. name -  название баннера.
	/////////////////////////////////////////////////////////////////////
	
	public function Banner($name)
	{
	  $Massive = Array();
	  if ((isset($id)) && (!empty($id))) { 
	    $Massive['amount'] = $BD_Obj->count_rec('blogs', 'Where (id = '.$id.')');  
        $Massive['data'] = $BD_Obj->select('blogs', Array('*'), "(id = ".$id.")", '', true);
		return $Massive;
	  }
      else	return 0;  	
	}

	/////////// Компонент (Cliker) кликера //////////////////////////////////////////
	
	public function Cliker()
	{
	   $Massive = Array();	   
	   /*if ($tags !== 'all') $ALLWhere = "(tags_blogs LIKE '%$tags%')";	  
	   $RWhere.= $ALLWhere;  
	   if ((isset($where)) && (!empty($where))) $RWhere.= $where; 
	   
	   $Massive['amount'] = $BD_Obj->count_rec('blogs', !empty($RWhere)?'Where '.$RWhere:'');  
       $Massive['data'] = $BD_Obj->select('blogs', Array('*'), $RWhere, '', true);
	   */
	   return $Massive;
	}
	
	/////////// Компонент (Shablon) шаблона //////////////////////////////////////////
	// 1. name -  название шаблона.
	////////////////////////////////////////////////////////////////////////
	
	public function Shablon($name)
	{
	  $Massive = Array();
	  if ((isset($id)) && (!empty($id))) { 
	    $Massive['amount'] = $BD_Obj->count_rec('blogs', 'Where (id = '.$id.')');  
        $Massive['data'] = $BD_Obj->select('blogs', Array('*'), "(id = ".$id.")", '', true);
		return $Massive;
	  }
      else	return 0;  	
	}

	/////////// Компонент (Shablons) шаблоны //////////////////////////////////////////
	// 1. where -  условия отбора шаблона.
	////////////////////////////////////////////////////////////////////////
	
	public function Shablons($where)
	{
	   $Massive = Array();	   
	   if ($tags !== 'all') $ALLWhere = "(tags_blogs LIKE '%$tags%')";	  
	   $RWhere.= $ALLWhere;  
	   if ((isset($where)) && (!empty($where))) $RWhere.= $where; 
	   
	   $Massive['amount'] = $BD_Obj->count_rec('blogs', !empty($RWhere)?'Where '.$RWhere:'');  
       $Massive['data'] = $BD_Obj->select('blogs', Array('*'), $RWhere, '', true);
	   
	   return $Massive;
	}

	/////////// Компонент (Comments) комментариев //////////////////////////////////////////
	// 1. id -  номер блога /товара.
	// 2. amount - количество записей для вывода.
	////////////////////////////////////////////////////////////////////////
	
	public function Comments($id, $amount = 5)
	{
	   $Massive = Array();	   
	   if ($tags !== 'all') $ALLWhere = "(tags_blogs LIKE '%$tags%')";	  
	   $RWhere.= $ALLWhere;  
	   if ((isset($where)) && (!empty($where))) $RWhere.= $where; 
	   
	   $Massive['amount'] = $BD_Obj->count_rec('blogs', !empty($RWhere)?'Where '.$RWhere:'');  
       $Massive['data'] = $BD_Obj->select('blogs', Array('*'), $RWhere, '', true);
	   
	   return $Massive;
	}

	/////////// Компонент (Comment) комментарий //////////////////////////////////////////
	// 1. id_comment -  номер комментария
	////////////////////////////////////////////////////////////////////////
	
	public function Comment($id_comment)
	{
	  $Massive = Array();
	  if ((isset($id)) && (!empty($id))) { 
	    $Massive['amount'] = $BD_Obj->count_rec('blogs', 'Where (id = '.$id.')');  
        $Massive['data'] = $BD_Obj->select('blogs', Array('*'), "(id = ".$id.")", '', true);
		return $Massive;
	  }
      else	return 0;  	
	}
	
	//////////// Компонент (ModeEditor) перехода в режим редактирования контента ///////////
	// 1. status - да / нет (true / false)
	////////////////////////////////////////////////////////////////////////////////////////
	public function ModeEditor($status = true) {			   
	  if ($status) {
	    if (($_SESSION['edit_key']) && ($_SESSION['edit_status'])) {
			return false; 
		} else { 		 
		    $_SESSION['edit_key'] = $this->Codes->random_coder(20);
		    $_SESSION['edit_status'] = true; 
			return true; 		  
		}          
	   } else {
	      unset($_SESSION['edit_key']);
		  unset($_SESSION['edit_status']);
          unset($_GET['mode']); 
      	  header('Location: index.php'); 
          exit;				  		  
       }
	}

    public function __destruct(){
		unset($this->BD_Obj);
		unset($this->Conf_Obj);
		unset($this->Paggi);
		unset($this->Files); 
        unset($this->Codes);
        unset($this->Strings);		
	}		
}
?>