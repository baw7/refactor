<?php
ini_set ('display_errors', 'On');
error_reporting(E_ALL);


include "displaycode.php";
use display\displaycode as displaycode;
$obj = new displaycode;


define('DATABASE','baw7');
define('USERNAME','baw7');
define('PASSWORD','jeqTTp7ze');
define('CONNECTION','sql1.njit.edu');
class Manage {
    public static function autoload($class) {
        include $class . '.php';
    }
}
spl_autoload_register(array('Manage', 'autoload'));
$obj=new displaycode;
$obj=new main();
class dbConn{
    protected static $db;
    private function __construct() {
        try {
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
    }
    public static function getConnection() {
        if (!self::$db) {
            new dbConn();
        }
        return self::$db;
    }
}
abstract class collection {
    protected $html;
    static public function create() {
        $model = new static::$modelName;
        return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet[0];
    }
}
class accounts extends collection {
    protected static $modelName = 'account';
}
class todos extends collection {
    protected static $modelName = 'todo';
}
abstract class model {
    protected $tableName;
    public function save(){
        if ($this->id != '') {
            $sql = $this->update();
        } else {
           $sql = $this->insert();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $array = get_object_vars($this);
        foreach (array_flip($array) as $key=>$value){
            $statement->bindParam(":$value", $this->$value);
        }
        $statement->execute();
        $id = $db->lastInsertId();
        return $id;
    }
    private function insert() {      
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $columnString = implode(',', array_flip($array));
        $valueString = ':'.implode(',:', array_flip($array));
        $sql =  'INSERT INTO '.$tableName.' ('.$columnString.') VALUES ('.$valueString.')';
        return $sql;
    }
    private function update() {  
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $comma = " ";
        $sql = 'UPDATE '.$tableName.' SET ';
        foreach ($array as $key=>$value){
            if( ! empty($value)) {
                $sql .= $comma . $key . ' = "'. $value .'"';
                $comma = ", ";
                }
            }
            $sql .= ' WHERE id='.$this->id;
        return $sql;
    }
    public function delete() {
        $db = dbConn::getConnection();
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $sql = 'DELETE FROM '.$tableName.' WHERE id ='.$this->id;
        $statement = $db->prepare($sql);
        $statement->execute();
    }
}
class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public static function getTablename(){
        $tableName='accounts';
        return $tableName;
    }
}
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public static function getTablename(){
        $tableName='todos';
        return $tableName;
    }
} 
class main
{
	public function __construct() {
	$form = '<form method ="post" enctype="multipart/form-data">';
	$form .= '<center><b>Table</b> <b>Accounts</b>';
	$form .= '<br>Select all';
	$records = accounts::findAll();
        $tabletable = displaycode::displayTable($records);
	$form .= $tabletable;
	
	$form .= '<p>Select';
	$id = 2;
	$records = accounts::findOne($id);
	$tabletable = displaycode::displaytwo($records);
	$form .= '<b><br>Retrieved  '.$id.'</b>';
	$form .= $tabletable;
	$form .= '<p>Insert';
	$record = new account();
	$record->email="baw7@njit.edu";
	$record->fname="Brianna";
	$record->lname="Wong";
	$record->phone="010-101-1010";
	$record->birthday="1994-11-21";
	$record->gender="female";
	$record->password="1234567";
	$lastID=$record->save();
	$records = accounts::findAll();
	$tabletable = displaycode::displayTable($records);
	$form .= '<b><br>Inserted  '.$lastID.'</b>';
	$form .= $tabletable;
        $form .= '<p>Update';
        $records = accounts::findOne($lastID);
        $record = new account();
        $record->id=$records->id;
        $record->password="09877";
        $record->save();
        $form .= '<b><br>Updated password of id '.$records->id.'</b>';
        $records = accounts::findAll();
        $tabletable = displaycode::displayTable($records);
        $form .= $tabletable;
        $form .= '<p>Delete';
        $records = accounts::findOne($lastID);
        $record= new account();
        $record->id=$records->id;
        $record->delete();
	$form .= '<b><br>Record '.$records->id.' deleted</b>';
	$records = accounts::findAll();
        $tabletable = displaycode::displayTable($records);
	$form .= $tabletable;
	$form .= '<p><b>Table</b> <b>Todos</b>';
	$form .= '<br>Select all ';
	$records = todos::findAll();
	$tabletable = displaycode::displayTable($records);
	$form .= $tabletable;
	$form .= '<p>Select';
	$id = 3;
	$records = todos::findOne($id);
	$tabletable = displaycode::displaytwo($records);
	$form .= '<b><br>Retrieved record '.$id.'</b>';
	$form .= $tabletable;
	$form .= '<p>Insert';
	$record = new todo();
        $record->owneremail="baw@njit.edu";
        $record->ownerid=2;
        $record->createddate="2017-09-05";
        $record->duedate="2017-11-20";
        $record->message="create mobile application";
        $record->isdone=0;
        $lastID=$record->save();
	$records = todos::findAll();
	$tabletable = displaycode::displayTable($records);
	$form .= '<b><br>Inserted '.$lastID.'</b>';
	$form .= $tabletable;
        $form .= '<p>Update';
        $records = todos::findOne($lastID);
        $record = new todo();
        $record->id=$records->id;
	$record->createddate="2018-02-16";
        $record->save();
        $form .= '<b><br>Updated created date '.$records->id.'</b>';
        $records = todos::findAll();
        $tabletable = displaycode::displayTable($records);
        $form .= $tabletable;
        $form .= '<p>Delete ';
        $records = todos::findOne($lastID);
        $record = new todo();
        $record->id=$records->id;
        $record->delete();
	$form .= '<b><br>Record '.$records->id.' deleted</b>';
        $records = todos::findAll();
        $tabletable = displaycode::displayTable($records);
        $form .= $tabletable;
        $form .= '</center></form> ';
	print($form);
	}
}
?>
