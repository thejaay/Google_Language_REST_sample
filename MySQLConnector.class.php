<?php 
/**
 * Connector to MySQL.
 *
 * @author Jaay
 *
 */
class MySQLConnector
{
	private $_db_name = "nps";
	private $_db_server = "localhost";
	private $_db_user = "root";
	private $_db_pwd = "";
	
	private $_handler;
	
	/**
	* Constructor.
	* Initialize the MySQL connection.
	*/
	public function __construct()
	{
		$this->_handler = new mysqli($this->_db_server, $this->_db_user, $this->_db_pwd, $this->_db_name);
	}

	public function query($query)
	{
		$result = $this->_handler->query($query);
		$objects = array();
		while($row = $result->fetch_assoc())
		{
			$objects[] = $row;
		}
		return $objects;
	}

	public function queryNoReturn($query)
	{
		$result = $this->_handler->query($query);
		return $result;
	}
}

?>
