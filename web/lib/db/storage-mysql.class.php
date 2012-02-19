<?
require_once('mysql.class.php');


class Storage
{
	var $db;

	function open($DATABASE)
	{
		$this->db = new MySQL();
		
		$res = $this->db->open($DATABASE['host_port'], $DATABASE['username'], $DATABASE['password'], $DATABASE['db_name']);

		return $res;
	}

	function create_db()
	{
		// create tables
		//$query[] = "BEGIN transaction;";
		$query = "CREATE TABLE document (id INTEGER PRIMARY KEY AUTO_INCREMENT, meta TEXT, data TEXT, timestamp TIMESTAMP);";
		//$query[] = "DROP TABLE document";
		$res[]  = $this->db->query($query);
		//print_r($res);

		return $res;
	}

	function close()
	{
		$this->db->close();
	}

	function begin()
	{
		$query = "BEGIN transaction";
		return $this->db->query($query);
	}

	function commit()
	{
		$query = "COMMMIT";
		return $this->db->query($query);
	}

	function rollack()
	{
		$query = "ROLLBACK";
		return $this->db->query($query);
	}

	// document

	function list_documents($from_date = null)
	{
		$query = "select id, meta, timestamp as 'time'";
		$query .= "	from document";
		if ($from_date !== null) {
			$query .= "	where timestamp >= '" . mysql_real_escape_string($from_date) . "'";
		}
		$query .= "	order by timestamp";
		return $this->db->query($query);
	}

	function read_document($id = null)
	{
		$query = "select * from document";
		if ($id !== null) {
			$query .= "	where";
			$query .= "		id = '" . mysql_real_escape_string($id) . "'";
		}

		$res = $this->db->query($query);

		if (is_array($res) and isset($res[0]['id'])) {
			$res[0]['data'] = json_decode($res[0]['data']);
			$res = $res[0];
		}

		return $res;
	}

	function write_document($id, $meta, $data)
	{
		$json_data = json_encode($data);

		$query = "update document"
			. "	set"
			. "		meta = '". mysql_real_escape_string($meta) . "',"
			. "		data = '" . mysql_real_escape_string($json_data) . "'"
			. "	where id = " . mysql_real_escape_string($id);
		$this->db->query($query);
		
		return $this->db->query($query);
	}

	function create_document($meta, $data)
	{
		$json_data = json_encode($data);

		$query[] = "insert into document "
				. "(meta, data) values ('"
				. mysql_real_escape_string($meta) . "', '"
			       	. mysql_real_escape_string($json_data) . "')";

		$query[] = "select id from document where id = last_insert_id()";
		
		$res = $this->db->query($query);

		if (is_array($res) and isset($res[1][0]['id'])) {
			$res = $res[1][0]['id'];
		}

		return $res;
	}

	function remove_document($id = null)
	{
		if ($id === null) {
			$query = "delete from document";
			$this->db->query($query);
			return;
		} else {
			$query = "delete from document";
			$query .= "	where id = '" . mysql_real_escape_string($id) . "'";
			$this->db->query($query);
		}
	}

	function remove_documents($to_date = null)
	{
		if ($to_date !== null) {
			$query = "delete from document";
			$query .= "	where timestamp < '" . mysql_real_escape_string($to_date) . "'";
			$this->db->query($query);
		}
	}

}



/* test -----------------------------------------------------------------------


$DATABASE = array(
	'host_port' => 'localhost',
	'username' => 'root',
	'password' => 'rybarp',
	'db_name' => 'mysql');

$d = new Storage();
$d->open($DATABASE);
$d->create_db();

print "create: -------------------------------------------------------\n";
$id = $d->create_document('doc 1', array(1, 'doc 1 data'));
var_dump($id);

print "read: -------------------------------------------------------\n";
var_dump($d->read_document($id));

print "write: -------------------------------------------------------\n";
$res = $d->write_document($id, 'doc 1 xxx', array(111, 'doc 1 data xxx'));
var_dump($res);

print "read: -------------------------------------------------------\n";
var_dump($d->read_document($id));

print "list: -------------------------------------------------------\n";
$docs = $d->list_documents();
var_dump($docs);

print "list time: -------------------------------------------------------\n";
$docs = $d->list_documents('2010-08-17 18:28:56');
var_dump($docs);

print "remove: -------------------------------------------------------\n";
$docs = $d->remove_document($id);
var_dump($docs);

print "remove docs: -------------------------------------------------------\n";
$docs = $d->remove_documents('2010-08-20 16:07:24');
var_dump($docs);

print "read: -------------------------------------------------------\n";
var_dump($d->read_document($id));

$d->close();

*/

?>
