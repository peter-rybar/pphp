<?

class MySQL
{
	var $db;

	function open($host_port, $username, $password, $db_name)
	{
		$this->db = mysql_connect($host_port, $username, $password);
		
		if ( $this->db == false ) {
			//print_r(mysql_error());
			return mysql_error($this->db);
		}

		if ( mysql_select_db($db_name) ) {
			return mysql_error($this->db);
		}

		return true;
	}


	function close()
	{
		mysql_close($this->db);
	}


	function query($query)
	{
		$result = array();
		if ( is_array($query) ) {
			foreach ( $query as $q ) {
				$statement = mysql_query($q);
				if ($statement === false) {
					$result = mysql_error($this->db);
					break;
				} else {
					if ($statement !== true) {
						$res = array();
						while ($row = mysql_fetch_array($statement, MYSQL_ASSOC)) {
							$res[] = $row;
						}
						$result[] = $res;
						mysql_free_result($statement);
					} else {
						$result[] = true;
					}
				}
			}
		} else {
			$statement = mysql_query($query);
			if ($statement === false) {
				$result = mysql_error($this->db);
			} else {
				if ($statement !== true) {
					$res = array();
					while ($row = mysql_fetch_array($statement, MYSQL_ASSOC)) {
						$result[] = $row;
					}
					mysql_free_result($statement);
				}
			}
		}
		return $result;
	}


	function error()
	{
		return '' . mysql_error($this->db);
	}
}

/* test: ----------------------------------------------------------------------

$s = new MySQL();
$s->open('localhost', 'root', 'aaaa', 'mysql');
//$s->open('77.93.217.76', 'cpojcalcdev', 'vU&s6J8wLc', 'cpojcalcdev');
//$query[] = "CREATE TABLE document(id INTEGER PRIMARY KEY AUTO_INCREMENT, meta TEXT, data TEXT, timestamp TIMESTAMP)";
$query[] = "INSERT INTO document (meta, data) VALUES('Hi', 'All')";
$query[] = "INSERT INTO document (meta, data) VALUES('Hello', 'Everybody')";
$query[] = "SELECT * FROM document";
//$query[] = "DROP TABLE document";
$res = $s->query($query);
print_r($res);
$s->close();

*/

?>
