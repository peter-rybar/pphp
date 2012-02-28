<?

class SQLite
{
	var $db;

	function open($filename)
	{
		$this->db = sqlite_open($filename, 0666, $sqliteerror);
		
		if ( $this->db == false ) {
			//print($sqliteerror);
			return $sqliteerror;
		} else {
			return true;
		}
	}


	function close()
	{
		sqlite_close($this->db);
	}


	function query($query)
	{
		if ( is_array($query) ) {
			foreach ( $query as $q ) {
				$res = sqlite_array_query($this->db, $q, SQLITE_ASSOC);
				$err = sqlite_last_error($this->db);
				if ($err !== 0) {
					return $err;
				}
				$result[] = $res;
			}
		} else {
			$result = sqlite_array_query($this->db, $query, SQLITE_ASSOC);
			$err = sqlite_last_error($this->db);
			if ($err !== 0) {
				return $err;
			}
		}
		return $result;
	}


	function error()
	{
		return '' . sqlite_last_error($this->db) . ': ' . sqlite_error_string(sqlite_last_error($this->db));
	}
}

/* test: ----------------------------------------------------------------------

$s = new SQLite();
$s->open(':memory:');
var_dump( $s->query("CREATE TABLE foo(bar INTEGER PRIMARY KEY, baz TEXT)") );
var_dump( $s->query("INSERT INTO foo VALUES(Null, '" . sqlite_escape_string('\'Hi"') . "')") );
//var_dump( $s->query("INSERT INTO foo VALUES(1, 'Mom')") );
print_r( $s->query("SELECT *FROM foo") );
$s->close();

$s = new SQLite();
$s->open(':memory:');
$query[] = "CREATE TABLE foo(bar INTEGER PRIMARY KEY, baz TEXT)";
$query[] = "INSERT INTO foo VALUES(Null, 'Hi')";
$query[] = "INSERT INTO foo VALUES(1, 'Mom')";
$query[] = "SELECT *FROM foo";
print_r( $s->query($query) );
$s->close();
*/

?>
