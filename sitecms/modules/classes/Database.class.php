<?php

/*-----------------------------------/
* Mysqli class for handling database interactions
* @author	Pixel Army
* @date		14-17-11
* @file		Database.class.php
*/

class Database{
	
	/*-----------------------------------/
	* @var Database
	* Instance of self
	*/
	protected static $Database;
	
	/*-----------------------------------/
	* @var mysqli
	* Mysqli instance - can utilize native mysqli functions
	*/
	public $mysqli;
	
	/*-----------------------------------/
	* @var querystr
	* String - Last query called
	*/
	protected $querystr;
	
	/*-----------------------------------/
	* @var result
	* Array - Result of the last query
	*/
	protected $result;
	
	/*-----------------------------------/
	* @var error
	* String - Last mysqli/statement error
	*/
	protected $error;
	
	/*-----------------------------------/
	* @var transaction_status
	* Boolean - A transaction is in progress
	*/
	protected $transaction_status;
	
	/*-----------------------------------/
	* Public constructor function
	*
	* @author	Pixel Army
	* @return	Object		New Database object
	*/
	public function __construct(){
        $this->transaction_status = false;
        $this->connect();
        self::$Database = $this;
    }
	
	/*-----------------------------------/
	* Connect to your database
	*
	* @author	Pixel Army
	* @return	New database connection
	*/
	public function connect(){
        $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_TABLE, DB_PORT);
		if($this->mysqli->connect_errno > 0){
			die('Cannot connect to database [' . $this->mysqli->connect_error . ']');
		}
		$this->mysqli->set_charset('utf8');	
    }
	
	/*-----------------------------------/
	* Gets the current Database instance
	*
	* @author	Pixel Army
	* @return	Object	Current Database instance
	*/
	public static function get_instance(){
        return self::$Database;
    }
	
	/*-----------------------------------/
	* Runs a query statement
	*
	* @author	Pixel Army
	* @param	$query		The query string to be run
	* @return	Boolean		True/False on success or failure
	*/
	public function query($query, $bind_params=array()){
		$this->querystr = $this->mysqli->real_escape_string($query);
		
		//Prepare query
		if($stmt = $this->mysqli->prepare($this->querystr)){
			
			//Bind params if necessary
			if(count($bind_params) > 0){
				$params = array('');
				foreach($bind_params as $key=>$val){
					$params[0] .= $this->get_param_type($val);
					array_push($params, $bind_params[$key]);
				}
				call_user_func_array(array($stmt, 'bind_param'), $this->get_ref_values($params));
			}
			
			//Execute query
			if($stmt->execute()){
				
				//Fetch results
				$this->result = $this->bind_results($stmt);
				
				//Clear error if not running a transaction so it won't carry over to next query you run
				if(!$this->transaction_status){
					$this->error = NULL;
				}
				
				return true;
				
			}else{
				$this->error = 'Stmt Error: ' .$stmt->error;
				trigger_error("Problem executing query (" .$this->querystr. "): " . $this->error);
			}
		}else{
			$this->error = 'Mysqli Error: ' .$this->mysqli->error;
			trigger_error("Problem preparing query (" .$this->querystr. "): " . $this->error);
		}
		
		return false;
			
    }
	
	/*-----------------------------------/
	* Binds results of the prepared query
	*
	* @author	Pixel Army
	* @return	Array		Array of data from query
	*/
	public function bind_results(mysqli_stmt $stmt){
        $parameters = array();
        $results = array();

        $meta = $stmt->result_metadata();

        //No sql error and meta is false, so most likely an update/insert/delete which has no results
        if(!$meta && $stmt->sqlstate){ 
            return array();
        }

		//Build result array
        $row = array();
        while($field = $meta->fetch_field()){
            $row[$field->name] = null;
            $parameters[] = & $row[$field->name];
        }
		
        call_user_func_array(array($stmt, 'bind_result'), $parameters);
		
        while($stmt->fetch()){
            $x = array();
            foreach($row as $key => $val){
                $x[$key] = $val;
            }
            array_push($results, $x);
        }
		
		//Close connection
		$stmt->close();

        return $results;
    }
	
	/*-----------------------------------/
	* Return results of your query
	*
	* @author	Pixel Army
	* @return	Array		Array of results for executed query
	*/
	public function fetch_array(){
		return $this->result;
	}
	
	/*-----------------------------------/
	* Gets the number of rows returned for last query run
	*
	* @author	Pixel Army
	* @return	Int		Number of rows returned
	*/
	public function num_rows(){
		return count($this->result);
	}
	
	/*-----------------------------------/
	* Gets the auto increment value of last value inserted for last query run
	*
	* @author	Pixel Army
	* @return	Int		Auto increment value (ID)
	*/
    public function insert_id(){
        return $this->mysqli->insert_id;
    }
	
	/*-----------------------------------/
	* Gets any errors for last query run
	*
	* @author	Pixel Army
	* @return	String		Error message
	*/
    public function error(){
        return $this->error;
    }
	
	/*-----------------------------------/
	* Gets last query string submitted
	*
	* @author	Pixel Army
	* @return	String		Query string
	*/
    public function get_last_query(){
        return $this->querystr;
    }
	
	/*-----------------------------------/
	* Starts a new mysqli transaction clearing old transaction data and turning off autocommit
	* IMPORTANT: Transactions will only work on innoDB tables
	*
	* @author	Pixel Army
	*/
	public function new_transaction(){
		
		//close any transactions already started but not finished
		if($this->transaction_status){
			$this->rollback();
		}
		
        $this->mysqli->autocommit(false);
        $this->transaction_status = true;
    }
	
	/*-----------------------------------/
	* Commits and ends current mysqli transaction and sets autocommit back to true
	*
	* @author	Pixel Army
	*/
	public function commit(){
        $this->mysqli->commit();
        $this->transaction_status = false;
        $this->mysqli->autocommit(true);
    }
	
	/*-----------------------------------/
	* Clears and ends uncommited transaction and sets autocommit back to true
	*
	* @author	Pixel Army
	*/
	public function rollback(){
      $this->mysqli->rollback();
      $this->transaction_status = false;
      $this->mysqli->autocommit(true);
	  $this->error = NULL;
    }
	
	/*-----------------------------------/
	* Keep unused connections open on long-running scripts, or reconnect timed out connections
	*
	* @author	Pixel Army
	* @return	Boolean		True if connection is up
	*/
    public function ping() {
        return $this->mysqli->ping();
    }
	
	/*-----------------------------------/
	* Determine the type of a variable
	*
	* @author	Pixel Army
	* @param	$var		Pass a variable to determine the type
	* @return	String		Variable type (string - s, boolean - i, integer - i, blob - b, double - d)
	*/
    public function get_param_type($var){
        switch(gettype($var)){
            case 'NULL':
            case 'string':
                return 's';
                break;

            case 'boolean':
            case 'integer':
                return 'i';
                break;

            case 'blob':
                return 'b';
                break;

            case 'double':
                return 'd';
                break;
        }
        return '';
    }
	
	/*-----------------------------------/
	* Get reference values for an array as reference is required for PHP 5.3+
	*
	* @author	Pixel Army
	* @param	$array		Array to manipulate
	* @return	Array		Array of references
	*/
    protected function get_ref_values($array){
        if(strnatcmp(phpversion(), '5.3') >= 0){
            $refs = array();
            foreach($array as $key => $value){
                $refs[$key] = & $array[$key];
            }
            return $refs;
        }
        return $array;
    }
	
	/*-----------------------------------/
	* Public destructor function frees results and closes connection
	*
	* @author	Pixel Army
	*/
    public function __destruct(){
		if($this->mysqli){
            $this->mysqli->close();
		}
    }
	
}


?>