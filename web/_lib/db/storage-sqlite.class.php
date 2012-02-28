<?
require_once('sqlite.class.php');


class Storage
{
	var $db;

	function open($filename)
	{
		$this->db = new SQLite();
		
		if (! file_exists($filename)) {
			$res[] = $this->db->open($filename);
			$query[] = "begin transaction;";
			$query[] = "create table document (
						id		integer		primary key asc,
						meta		text,
						data		text,
						timestamp	datetime
						--timestamp	datetime	not null default CURRENT_TIMESTAMP
						--timestamp	datetime	not null default (datetime('now', 'localtime'))
					)";
			$query[] = "create trigger insert_document_datetime_trigger after insert on document
					begin
						update document
							set timestamp = datetime('NOW', 'localtime')
							where rowid = new.rowid;
					end";
			$query[] = "create trigger update_document_datetime_trigger after update on document
					begin
						update document
							set timestamp = datetime('NOW', 'localtime')
							where rowid = new.rowid;
					end";
			$query[] = "commit";
			$res[]  = $this->db->query($query);
		} else {
			$res = $this->db->open($filename);
		}
		
		return $res;
	}


	function close()
	{
		$this->db->close();
	}

	function begin()
	{
		$query = "begin transaction";
		return $this->db->query($query);
	}

	function commit()
	{
		$query = "commit";
		return $this->db->query($query);
	}

	function rollack()
	{
		$query = "rollback";
		return $this->db->query($query);
	}

	// document

	function list_documents($from_date = null)
	{
		$query = "select id, meta, timestamp as 'time'";
		$query .= "	from document";
		if ($from_date !== null) {
			$query .= "	where timestamp >= '" . sqlite_escape_string($from_date) . "'";
		}
		$query .= "	order by timestamp";
		return $this->db->query($query);
	}

	function read_document($id = null)
	{
		$query = "select * from document";
		if ($id !== null) {
			$query .= "	where";
			$query .= "		id = '" . sqlite_escape_string($id) . "'";
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
			. "		meta = '". sqlite_escape_string($meta) . "',"
			. "		data = '" . sqlite_escape_string($json_data) . "'"
			. "	where id = " . sqlite_escape_string($id);
		$this->db->query($query);
		
		return $this->db->query($query);
	}

	function create_document($meta, $data)
	{
		$json_data = json_encode($data);

		$query[] = "insert into document "
				. "(meta, data) values ('"
				. sqlite_escape_string($meta) . "', '"
			       	. sqlite_escape_string($json_data) . "')";

		$query[] = "select id from document where id = last_insert_rowid()";
		
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
			$query .= "	where id = '" . sqlite_escape_string($id) . "'";
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

$d = new Storage();
//$d->open(':memory:');
$d->open('db.sqlite');

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
