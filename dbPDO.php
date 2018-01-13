<?php
/**
 * MysqliDb Class
 *
 * @category  Database Class
 * @package   dbPDO
 * @author    Yashwant Mehar <yashwantmehar@gmail.com>
 * @copyright Copyright (c) 2017
 * @version   1.0
 **/
class dbPDO
{
	private $_conn;
	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpass;

	private $_stmt;
	private $_query;
	private $_tableFields;
	private $_tableCols;
	private $_tableBindCols;
	private $_tableData;
	private $_where;
	private $_whereCase;
	private $_whereData;
	private $_limit;
	private $_sort;
	private $_res;
	private $_orderBy;
	private $_groupBy;
	private $error;

	function __construct($host, $db, $user, $pass)
	{
		$this->dbhost= $host;
		$this->dbname= $db;
		$this->dbuser= $user;
		$this->dbpass= $pass;
		$this->_conn = new PDO("mysql:host=".$this->dbhost.";dbname=".$this->dbname, $this->dbuser, $this->dbpass);
	}
	function __destruct()
	{
		unset($this);
	}

	/**
     * Reset states after an execution
     *
     */
    protected function reset()
    {
        $this->_stmt = $this->_query = $this->_tableFields = $this->_tableCols = $this->_tableBindCols = $this->_tableData = $this->_where = $this->_whereData = $this->_limit = $this->_sort = $this->_orderBy = $this->_groupBy = $error = "";
    }


	/**
     * Method that will help to build the Where Condition of query
     *
     */
	public function where($whereProp, $whereValue = null, $operator = null)
    {
        if ($operator)
            $whereValue = Array ($operator => $whereValue);

        $this->_where[] = Array ("AND", $whereValue, $whereProp);
        return $this;
    }

    /**
     * Method that will help to build the orWhere Condition of query
     *
     */
    public function orWhere($whereProp, $whereValue = null, $operator = null)
    {
        if ($operator)
            $whereValue = Array ($operator => $whereValue);

        $this->_where[] = Array ("OR", $whereValue, $whereProp);
        return $this;
    }

    /**
     * Method that will help to build the Order By Condition of query
     *
     */
    public function orderBy($orderByField, $orderbyDirection = "DESC")
    {
        $allowedDirection = Array ("ASC", "DESC");
        $orderbyDirection = strtoupper (trim ($orderbyDirection));
        $orderByField = preg_replace ("/[^-a-z0-9\.\(\),_]+/i",'', $orderByField);

        if (empty($orderbyDirection) || !in_array ($orderbyDirection, $allowedDirection))
            die ('Wrong order direction: '.$orderbyDirection);

        $this->_orderBy[$orderByField] = $orderbyDirection;
        return $this;
    }

    /**
     * Method that will help to build the Group By clause of query
     *
     */
    public function groupBy($groupByField)
    {
        $groupByField = preg_replace ("/[^-a-z0-9\.\(\),_]+/i",'', $groupByField);

        $this->_groupBy[] = $groupByField;
        return $this;
    }

    /**
     * Method that will help to build the Limit clause of query
     *
     */
    public function limit($numRows)
    {
        $this->_limit = $numRows;
        return $this;
    }

    /**
     * Abstraction method that will build the part of the WHERE conditions
     */
    protected function buildWhere()
    {
		$this->_where[0][0] = '';
		$this->_query.= " WHERE";
		foreach ($this->_where as $wh) {
			
			if (is_array($wh[1]) AND !empty($wh[1])) {
				// key($wh[1]);
				$this->_whereCase = strtolower(key($wh[1]));
				switch ($this->_whereCase) {
					case '<':
					case '>':
					case '=':
					case '<=':
					case '>=':
					case '<>':
						$this->_whereData[$wh[2]] =  $wh[1][key($wh[1])];
						$this->_query.= " ".$wh[0] ." `". $wh[2]."` ".$this->_whereCase. " :".$wh[2]. "";
						break;
					case 'like':
						$this->_query.= " ".$wh[0] ." `". $wh[2]."` ".$this->_whereCase. " '%".$wh[1][key($wh[1])]. "%'";
						break;
					case 'in':
					case 'not in':
						$this->_query.= " ".$wh[0] ." `". $wh[2]."` ".$this->_whereCase. " ('".implode("','", $wh[1][key($wh[1])]). "')";
						break;
					case 'between':
					case 'not between';
						$this->_query.= " ".$wh[0] ." `". $wh[2]."` ".$this->_whereCase. " '".implode("' AND  '", $wh[1][key($wh[1])]). "'";
						break;
					
					default:
						$this->_whereCase = "=";
						break;
				}
			}else{
				$this->_whereData[$wh[2]] =  $wh[1];
				$this->_query.= " ". $wh[0] ." `". $wh[2]."` = :". $wh[2] . "";
			}
		}
    } 

    /**
     * Abstraction method that will build the ORDERBY part of the WHERE statement
     *
     */
    protected function buildOrderBy () 
    {
        if (empty ($this->_orderBy))
            return;

        $this->_query .= " ORDER BY ";
        foreach ($this->_orderBy as $prop => $value)
            $this->_query .= $prop . " " . $value . ", ";

        $this->_query = rtrim ($this->_query, ', ') . " ";
    }

    /**
     * Abstraction method that will build the GROUP BY part of the WHERE statement
     *
     */
    protected function buildGroupBy()
    {
    	if (empty ($this->_groupBy))
            return;
       	$this->_query .= " GROUP BY ";
       	foreach ($this->_groupBy as $key => $value)
            $this->_query .= $value . ", ";

        $this->_query = rtrim($this->_query, ', ') . " ";
    }

    /**
     * Abstraction method that will build the LIMIT part of the WHERE statement
     *
     */
    protected function buildLimit () 
    {
    	if (empty($this->_limit)) {
    		return;
    	}
        
        if (is_array ($this->_limit))
            $this->_query .= ' LIMIT ' . (int)$this->_limit[0] . ', ' . (int)$this->_limit[1];
        else
            $this->_query .= ' LIMIT ' . (int)$this->_limit;
    }

    /**
     * Method that will use to get the records from respective Table
     * 
     * @param string  $tableName The name of the database table to work with.
     * @param array $columns   Name of column which value want to select
     *
     */
	public function get($tableName, $columns="*")
	{
		if (empty ($columns)){
        	$columns = '*';
        }
        $column = is_array($columns) ? implode(', ', $columns) : $columns; 

		$this->_query = "SELECT ".$column." FROM ".$tableName;

		if (!empty($this->_where)) {
			$this->buildWhere(); 
		}

		if (!empty($this->_groupBy)) {
			$this->buildGroupBy(); 
		}

		if (!empty($this->_orderBy)) {
			$this->buildOrderBy(); 
		}

		if (!empty($this->_limit)) {
    		$this->buildLimit(); 
    	}
    	$this->_stmt = $this->_conn->prepare($this->_query);
    	if (is_array($this->_whereData)) {
    		$this->_stmt->execute($this->_whereData);
    	}else{
    		$this->_stmt->execute();
    	}
		$this->_stmt->setFetchMode(PDO::FETCH_ASSOC);
		$this->_res = $this->_stmt->fetchAll();
		$this->reset();
		return $this->_res;
	}

	/**
     * Method that will use to get the single records from respective Table
     * 
     * @param string  $tableName The name of the database table to work with.
     * @param array $columns   Name of column which value want to select
     *
     */
	public function getOne($tableName, $columns = '*') 
    {
    	$this->limit(1);
        $this->_res = $this->get ($tableName, $columns);
        if (is_object($this->_res))
            return $this->_res;

        if (isset($this->_res[0]))
            return $this->_res[0];
        return null;
    }

    /**
     * Method that will use to delete the records from respective Table
     * 
     * @param string  $tableName The name of the database table to work with.
     *
     */
	public function delete($tableName)
	{
		$this->_query = "DELETE FROM ".$tableName;
		if (!empty($this->_where)) {
			$this->buildWhere(); 
		}
		$this->_stmt = $this->_conn->prepare($this->_query);
		$this->_res = $this->_stmt->execute($this->_whereData);
		// $this->_res =  $this->_conn->exec($this->_query);
		$this->reset();
		return $this->_res;
	}

	/**
     * Method that will use to insert the records into respective Table
     * 
     * @param string  $tableName The name of the database table to work with.
     * @param array $data   Value to be insert into respective table
     *
     */
	public function insert($tableName, $data=array())
	{
		$this->_stmt = $this->_conn->prepare("DESCRIBE ".$tableName);
		$this->_stmt->execute();
		$this->_tableFields = $this->_stmt->fetchAll(PDO::FETCH_COLUMN);
		
		foreach ($this->_tableFields as $fieldName) {
			if (isset($data[$fieldName])) {
				$this->_tableData[$fieldName] = ":".$fieldName;
				$this->_tableCols[] = $fieldName;
				$this->_tableBindCols[$fieldName] = $data[$fieldName];
			}
		}
		$this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$this->_query = "INSERT INTO ".$tableName." (`". implode("`,`", $this->_tableCols). "`) VALUES ";
		$this->_query.= " (". implode(",", $this->_tableData). ") ";
		
		$this->_stmt= $this->_conn->prepare($this->_query);
		$this->_stmt->execute($this->_tableBindCols);
		$this->_res = $this->_conn->lastInsertId();
		$this->reset();
		return $this->_res;
	}

	/**
     * Method that will use to update the records from respective Table. This will work only with where conditions
     * 
     * @param string  $tableName The name of the database table to work with.
     * @param array $data   Value to be update into respective table 
     *
     */
	public function update($tableName,$data)
	{
		if (!empty($this->_where)) {
			$this->_stmt = $this->_conn->prepare("DESCRIBE ".$tableName);
			$this->_stmt->execute();
			$this->_tableFields = $this->_stmt->fetchAll(PDO::FETCH_COLUMN);

			foreach ($this->_tableFields as $fieldName) {
				if (isset($data[$fieldName])) {
					$this->_tableData[$fieldName] = ":".$fieldName;
					// $this->_tableCols[] = $fieldName;
					$this->_tableBindCols[$fieldName] = $data[$fieldName];
				}
			}

			$this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$this->_query = "UPDATE ".$tableName." SET ";
			foreach ($this->_tableData as $key => $value) {
				$this->_query.= "`".$key."` = ".$value.", ";
			}
			$this->_query = substr(trim($this->_query), 0,-1);

			$this->buildWhere();

			#Bind Param with Where Data
			if (in_array($this->_whereCase, array('','<','>','=','<=','>=','<>'))) {
				foreach ($this->_whereData as $key => $value) {
					$this->_tableBindCols[$key] = $value;
				}
			}
			$this->_stmt= $this->_conn->prepare($this->_query);
			$this->_stmt->execute($this->_tableBindCols);
			# return count of updated records
			$this->error = $this->_stmt->errorInfo();
			if ($this->error[0] == 0 && !empty($this->error[2])) {
				$this->_res = $this->error[2];
			}
			else{
				$this->_res = $this->_stmt->rowCount();
			}
			$this->reset();
			return $this->_res;
		}
		else{
			return ;
		}
	}

	/**
     * Method that will use to fetch the value according to respective query.
     * 
     * @param string  $query Query Statement to be execute.
     *
     */
	public function runQuery($query)
    {
    	$this->_query = $query;
    	$this->_stmt = $this->_conn->prepare($this->_query);
    	$this->_stmt->execute();
		$this->_stmt->setFetchMode(PDO::FETCH_ASSOC);
		$this->error = $this->_stmt->errorInfo();

		if ($this->error[0] == 0 && !empty($this->error[2])) {
			$this->_res = $this->error[2];
		}else{
			$this->_res = $this->_stmt->fetchAll();
		}
		$this->reset();
		return $this->_res;
    }
}
?>
