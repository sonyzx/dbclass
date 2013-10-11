<?php
/**
 * Secure Database Class
 * Version 0.2
 * 
 * @author Markus Schuer <markus.schuer@googlemail.com>
 * @param DATABASE_USER const (Databseuser)
 * @param DATABASE_PASS const (Userpass)
 * @param DATABASE_HOST const (Databasehostaddress)
 * @param DATABASE_NAME const (Databasename)
 */


class database
{
	private 	$args = array();
	private 	$sql;	
	private 	$errorMessage = '';
	private 	$sqlError = '';
	public 		$lastResult; 
	public 		$affectedRows; 
	public 		$dataResultSet;
	
	public function __construct()
	{
		$this->user = DATABASE_USER;
		$this->pass = DATABASE_PASS;
		$this->root = DATABASE_HOST;
		$this->dbEx = DATABASE_NAME;
		$this::doConnect();
	}
	
	public function __destruct()
	{
		@mysql_free_result($this->lastResult);
		mysql_close();
	}
	
	private function doConnect()
	{
		try{
			mysql_connect($this->root, $this->user, $this->pass);
		} catch (Exception $e) {
			$this->addErrorMessage('Database connection failed !');
		}
		try {
			mysql_select_db($this->dbEx);
		} catch (Exception $e) {
			$this->addErrorMessage('Database not found!');
		}
	}
	/**
	* runs the SQl Statement
	*
	* @param SQL Statement with Args
	* @return Array 
	*/
	public function doQuery()
	{
		$this->sql 			= $this::query(func_get_args());
		$this->lastResult 	= mysql_query($this->sql);
		$this->lastInsertId	= mysql_insert_id();
		$this->affectedRows = mysql_affected_rows();
		$this->sqlError	    = mysql_error();
		if (empty($this->sqlError)) {
			$this->dataResultSet = $this::getResultArray();
		}
	}
	/**
	* Returns the Results Array
	*
	* @return Array 
	*/
	private function getResultArray()
	{
		$data['rows'] 		= array();
		$data['numrows'] 	= $this->affectedRows;
		while($row = mysql_fetch_assoc($this->lastResult)) 
		{
			$data['rows'][] = $row;
		}
		return $data;
	}
	
	/**
	* builds the Query
	*
	* @return string 
	*/
	
	private function query($args)
	{
		$args		= $this::removeZeroValues($args);
		$query   	= array_shift($args);
		$args    	= array_map('mysql_real_escape_string', $args);
		return  vsprintf($query, $args);;
	}
	
	/**
	* Deletes zero values
	*
	* @return Array 
	*/
	private function removeZeroValues($args)
	{
		$failsafeargs = array();
		foreach($args as $_entry)
		{
			if (empty($_entry))
			{
				$_entry = 0;
			}
			$failsafeargs[] = $_entry;
		}
		return $failsafeargs;
		
	}
	/**
	* Returns the last inserted ID
	*
	* @return Integer
	*/
	public function getLastInsertedId()
	{
		return $this->lastInsertId;
	}
	/**
	* Returns the Results
	*
	* @return Array 
	*/
	public function getDataResultSet()
	{
		return $this->dataResultSet;
	}
	
	/**
	* Adds String to ErrorMessage
	*
	* @param string 
	*/
	private function addErrorMessage($message)
	{
		$this->errorMessage.= $message;
	}
	
	/**
	* Returns all Debuginformations.
	*
	* @return string Debuginformations
	*/
	public function debug()
	{
		$debugData= '<div class="dbdebugmessage"><ul>';
		$debugData.= '<li><span>query</span> ' . $this->sql . '</li>';
		if (!empty($this->sqlError)) 		$debugData.= '<li><span>mysqlerror</span> ' . $this->sqlError . '</li>';
		if (!empty($this->errorMessage)) 	$debugData.= '<li><span>Error</span> ' . $this->errorMessage . '</li>';
		if (!empty($this->lastResult)) 		$debugData.= '<li><span>lastResult</span> ' . $this->lastResult . '</li>';
		if (!empty($this->lastInsertId)) 	$debugData.= '<li><span>LastinsertedId</span> ' . $this->lastInsertId . '</li>';
		if (!empty($this->affectedRows)) 	$debugData.= '<li><span>AffectedRows</span> ' . $this->affectedRows . '</li>';
		$debugData.= '</ul></div>';
		return $debugData;
	}
}
