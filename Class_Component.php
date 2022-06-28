<?
  /////////////////////////////////////////////////////////////////////
  //
  // Создатель CMS/Framework SolarWind: Ларионов Андрей Николаевич (Middle-TeamLimited)
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

require_once('Class_BD.php');
require_once('Class_String.php');

abstract class AbstractComponent
{
    abstract protected function setValue($Name_Field, $Value);
	abstract protected function getValue($Field);
    abstract protected function PrintComponent($prefix);	
	abstract protected function TestClass();

    public function printOut() {
        print $this->getValue() . "\n";
    }
}

class Combobox extends AbstractComponent
{
	private $GeneratorNumber;
    private $BD_Conn;	
	private $ID;    
	private $Name;
	private $Class_Component;
	private $Style_Component;
	private $TableName;
	private $Field_Combobox;
	private $Checked_item;
	private $Sorted;
	private $Ordered;
	public $Count_items;
	public $WhereCombobox;
	public $Distinct = false;
	public $Null_current;
	public $ArrayItem = Array();
	public $Debug = false;
	public $Limit = "";
	public $Param;
	
    function __construct() {
	   $this->BD_Conn = new Class_BD;
	   $this->WhereCombobox = "";
	   $this->Sorted = "";
	   $this->Ordered = "";	   
	}

	//////////////////////////////////////////////////////////
	// Метод отклика класса - метод TestClass
	//////////////////////////////////////////////////////////	
	public function TestClass() {
		return 'Ответ от класса Компоненты получен!';
	}	
	
	//////////////////////////////////////////////////////////
	// Установление значения - метод SetValue
	//    Name_Field - имя поля
	//    Value - значение поля
	//////////////////////////////////////////////////////////
    public function setValue($Name_Field, $Value) {
        if (($Name_Field) && (!empty($Name_Field )) &&
		   ($Value) && (!empty($Value))) {
	
		 switch ($Name_Field) {
          case 'ID': $this->ID = $Value; break;
		  case 'Name': $this->Name = $Value; break;
		  case 'Class_Component': $this->Class_Component = $Value; break;
		  case 'Style_Component': $this->Style_Component = $Value; break;
		  case 'TableName': $this->TableName = $Value; break;
		  case 'Field_Combobox': $this->Field_Combobox = $Value; break;
		  case 'Checked_item': $this->Checked_item = $Value; break;
		  case 'Sorted': $this->Sorted = $Value; break;
		  case 'Ordered': $this->Ordered = $Value; break;
         }		 
		 return true;
		 
		} else 
		   return false;
    }

	//////////////////////////////////////////////////////////
	// Возвращение значения - метод GetValue
	//////////////////////////////////////////////////////////
	public function getValue($Field) {
      switch ($Field) {
        case 'ID':
          return $this->ID;
          break;
		case 'Name':
          return $this->Name;
          break;  
        case 'Class_Component':
          return $this->Class_Component;
          break;
        case 'Style_Component':
          return $this->Style_Component;
          break;
        case 'TableName':
          return $this->TableName;
          break;
        case 'Field_Combobox':
          return $this->Field_Combobox;
          break;
        case 'Checked_item':
          return $this->Checked_item;
          break;  
        case 'Sorted':
          return $this->Sorted;
          break; 
        case 'Ordered':
          return $this->Ordered;
          break;    		  
      }		
    } 		
	
	//////////////////////////////////////////////////////////
	// Вывод данных компонента в виде массива - метод GetDataCombobox
	//////////////////////////////////////////////////////////
	public function GetDataCombobox() {
	   $this->ArrayItem = Array();		
	   $ReturnArray = Array();
	   
       if ($this->Distinct == true) 	  
	          $All_rec = $this->BD_Conn->DistinctSelect($this->TableName, 
	                                     $this->Field_Combobox, 
										 $this->WhereCombobox, 
										 $this->Ordered, 
										 $this->Sorted,
										 $this->Limit,										 
										 $this->Debug); 
	    else 
		      $All_rec = $this->BD_Conn->select($this->TableName, 
	                                     Array($this->Field_Combobox), 
										 $this->WhereCombobox, 
										 $this->Ordered, 
										 $this->Sorted,
										 $this->Limit,										 
										 $this->Debug); 
		$Arr = Array();   
		if (is_Array($All_rec)) {
		   foreach ($All_rec as $k => $Record) 
		     foreach ($Record as $key => $Fields) {	   	       
		      $id = $this->BD_Conn->get_field($this->TableName, 'id', "(".$this->Field_Combobox." = '".$Fields."')");
			  $Arr[$id] = $Fields;		  
	        }    
          } else return $All_rec;	
		
		return $Arr;       		
	}
	
	//////////////////////////////////////////////////////////
	// Вывод компонента в виде списка - метод PrintComponent
	// prefix - префикс компонента
	// Count_items -  количество видимых пунктов
	// Multiple - множественный выбор пунктов списка
	//////////////////////////////////////////////////////////
    public function PrintComponent($prefix = 'Combobox_', 
	                               $Count_items = 1, 
								   $Multiple = false) {
       $this->GeneratorNumber = new Class_String;
	   $Coder = $this->GeneratorNumber->Random_text(5, false);
	   $print_str = '';
	   
	   if (empty($this->ID)) 
		   $this->ID = $Coder;
	   $print_str = "<select id='".$prefix.$this->ID."' ";
	   
	   if (!empty($this->Param))
	     $print_str .= " param='".$this->Param."' ";
	   
	   if (!empty($this->Name)) 
	     $print_str .= " name='".$prefix.$this->Name."' ";
	   	   
	   if (!empty($this->Class_Component))
	       $print_str .= " class='".$prefix.$this->Class_Component."'";
      
	  if (!empty($this->Style_Component))	   
	      $print_str .= " style='".$this->Style_Component."'";
      if ($Count_items > 1) $print_str .= " size=".$Count_items;
	  if ($Multiple) $print_str .= " multiple ";
	  $print_str.= ">";	  
      $All_rec = $this->TableName;
	  
	  if ($this->Null_current == true) {
           $print_str .= "<option value=''></option>";
	  }
	  
	  if (is_Array($this->TableName)) {
		 
		 
		 foreach ($All_rec as $Keys => $Fields) {
			if (is_string($Keys)) 
			  $print_str .= "<option value='".$Keys."'";
			else  
			  $print_str .= "<option value='".$Fields."'"; 
			  
			if ($Fields == $this->Checked_item) $print_str .= " selected ";
			
			$print_str .= ">".$Fields."</option>";  
		 }
		 
	  } else {
		  
	    if ($this->Distinct == true) 	  
	          $All_rec = $this->BD_Conn->DistinctSelect($this->TableName, 
	                                     $this->Field_Combobox, 
										 $this->WhereCombobox, 
										 $this->Ordered, 
										 $this->Sorted,
										 $this->Limit,										 
										 $this->Debug); 
	    else 
		      $All_rec = $this->BD_Conn->select($this->TableName, 
	                                     Array($this->Field_Combobox), 
										 $this->WhereCombobox, 
										 $this->Ordered, 
										 $this->Sorted,
										 $this->Limit,										 
										 $this->Debug); 
		   
		   if (is_Array($All_rec)) {
		   foreach ($All_rec as $Record) 
		     foreach ($Record as $key => $Fields) {	   
	       
		       if ($Fields == $this->Checked_item) 
		         $checked_it = " selected ";
		       else 
		         $checked_it = '';
		       $print_str .="<option $checked_it value='".$Fields."'>".$Fields."</option>";		  
	        }    
          } else return $All_rec;			
	  } 
	 
	  $print_str .="</select>";	  
	  return $print_str;   
    }
}	










/*
class ConcreteClass2 extends AbstractComponent
{
    public function getValue($Name_Field, $Value) {
        return "ConcreteClass2";
    }

	protected function setValue() {
        return "Set Value Class1";
    } 
	
    public function PrintComponent($prefix = '') {
        return "{$prefix}ConcreteClass2";
    }
} 
*/

/*
abstract class AbstractComponents
{
    private $BD_Conn;
	private $Id_Component;
	private $Class_Component;
	private $Style_Component;
	private $TableName;
	private $FieldCombobox;
	private $Checked_item;
	
	abstract public function CreateComponent();
	abstract public function GetValue($value);	 	
	abstract public function SetValue($Name_Field, $Value); 	
	
	public function PrintComponent()
	{
	   print_r(CreateComponent());
	}	
}

Class Combobox extends AbstractComponents
{
    private $BD_Conn;
	private $Id_Component;    
	private $Class_Component;
	private $Style_Component;
	private $TableName;
	private $FieldCombobox;
	private $Checked_item;
	
    function __construct() {
	   $this->BD_Conn = new Class_BD;
	}
		
    public function CreateComponent() {
       $print_str = "<select id='".$this->Id_Component."' class='".$this->Class_Component."' style='".$this->Style_Component."'>"; 
	   $All_rec = $this->BD_Conn->select($this->TableName, Array('*'), '', '', true); 	   
	   
	   foreach ($All_rec as $Fields)
       {	   
	     if ($Fields[$this->FieldCombobox] == $this->Checked_item) $checked_it = "selected = 'selected'";
		 else $checked_it = '';
		 $print_str .="<option $checked_it value='".$Fields[$this->FieldCombobox]."'>".$Fields[$this->FieldCombobox]."</option>";		  
	   }	   
	   $print_str .="</select>";
	   return $print_str;
	}

    public function GetValue($value) {
      switch ($Name_Field) {
          case 'Id_Component': $this->Id_Component = $Value;break;
		  case 'Class_Component': $this->Class_Component = $Value;break;
		  case 'TableName': $this->TableName = $Value;break;
		  case 'FieldCombobox': $this->FieldCombobox = $Value;break;
		  case 'Checked_item': $this->Checked_item = $Value;break;
        } 			
	}
	
    public function GetValue($value) {
      switch ($value) {
        case 'ID':
          return $this->Id_Component;
          break;
        case 'Class':
          return $this->Class_Component;
          break;
        case 'Style':
          return $this->Style_Component;
          break;
        case 'Table':
          return $this->TableName;
          break;
        case 'Field':
          return $this->FieldCombobox;
          break;
        case 'Checked':
          return $this->Checked_item;
          break;        		  
      }		
	}
	
    public function PrintComponent();	
}

/*
Class Itembox extends AbstractComponents
{
    private $BD_Conn;
	public $Id_Component;    
	public $Class_Component;
	public $Style_Component;
	public $TableName;
	public $FieldCombobox;
	public $Amount_item;
	public $Multiple;
	
    function __construct()
	{
	   $this->BD_Conn = new Class_BD;
	   $this->Amount_item = 5;
	   $Multiple = ""; //"multiple";
	}
			
    protected function CreateComponent()
	{
	   $print_str = "<select id='".$this->Id_Component."' size='".$this->Amount_item."' class='".$this->Class_Component."' style='".$this->Style_Component."'>" ; 
	   $All_rec = $this->BD_Conn->select($this->TableName, Array('*'), '', '', true); 	   
	   
	   foreach ($All_rec as $Fields)	   
	     $print_str .="<option value='".$Fields[$this->FieldCombobox]."'>".$Fields[$this->FieldCombobox]."</option>";		  
	   $print_str .="</select>";
	   
	   return $print_str;
	}	
}

Class RadioButton extends AbstractComponents
{
    private $BD_Conn;
	public $Id_Component;    
	public $Class_Component;
	public $Style_Component;
	public $TableName;
	public $FieldCombobox;
	
    function __construct()
	{
	   $this->BD_Conn = new Class_BD;
	} 

    protected function CreateComponent()
	{	   	   
	   $All_rec = $this->BD_Conn->select($this->TableName, Array('*'), '', '', true); 	   	   
	   foreach ($All_rec as $Fields)	   
	     $print_str .="<input type='radio' value='".$Fields[$this->FieldCombobox]."'>".$Fields[$this->FieldCombobox]."</option>";		  
		 
		/*$print_str = "<select id='".$this->Id_Component."' size='".$this->Amount_item."' class='".$this->Class_Component."' style='".$this->Style_Component."'>" ;  
		 
		 
	   $print_str .="</select>";	   
	   return $print_str;
	   
	}	*/	
//}
?>