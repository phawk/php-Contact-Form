<?php
/*
 *	Contact_form
 *	
 *	@author		Pete Hawkins <pete@phawk.co.uk>
 *	@created	26th June 2011
 *	@copyright	Pete Hawkins from 2011
 *
 */

class Contact_form
{
	
	/*
	 *	Instance Variables
	 */
	private $send_to = 'my_email@example.com';      //  The email address you want message sent to
	private $subject = 'An email from my website';  //  The subject to appear on those messages
	
	private $pst;
	private $message = '';
	
	private $db = null;
	private $db_host = 'localhost';
	private $db_user = 'USERNAME';
	private $db_pass = 'PASSWORD';
	private $db_name = 'DATABASE_NAME';
	private $db_table = 'DB_TABLE_NAME';
	
	
	/*  Array of fields expected from post and named in database table
	 *  Name email and message are required. Or edit the 'format_message' method to change.
	 */
	private $fields = array('name','email','message');
	
	
	
	/*
	 *	Pass $_POST array to constructor
	 */
	public function __construct( $postdata = null )
	{
		if (is_array($postdata))
		{
			$this->pst = array_map( array( $this, 'clean_post_array' ), $postdata );
			
			$this->db = mysql_connect( $this->db_host, $this->db_user, $this->db_pass );
			
			if( ! $this->db)
			{
				die( 'Could not connect to database: ' . mysql_error() );
			}
			else
			{
				mysql_select_db( $this->db_name );
			}
		}
		else
		{
			die( 'No postdata passed.' );
		}
	}
	
	
	
	/*
	 *	Close mysql connection on destruction of the object.
	 */
	public function __destruct()
	{
		mysql_close( $this->db );
	}
	
	
	
	/*
	 *	Callback Removes unwanted characters from the post array on construction
	 */
	private function clean_post_array( $data = null )
	{
		if( $data ) {
			return htmlentities( $data, ENT_QUOTES, 'UTF-8' );
		}
	}
	
	
	
	/*
	 *	Preps the message that is placed into the email
	 */
	private function format_message()
	{
		$msg = "Website Enquiry Form\n\n";
		$msg .= "From:\n" . $this->pst[ 'name' ] . "\n";
		$msg .= "Date:\n" . date('jS F Y') . "\n";
		$msg .= "Email:\n" . $this->pst[ 'email' ] . "\n\n";
		
		$msg .= "Message:\n";
		$msg .= $this->pst[ 'message' ] . "\n";
		
		$this->message = $msg;
		
		return true;
	}
	
	
	
	/*
	 *	Sends the email to the address listed at the top of this page
	 *
	 *	Return: BOOL
	 */
	public function send_mail()
	{
		$this->format_message();
		
		$headers = 	'From: '.$this->pst[ 'email' ]."\r\n".
					'Reply-To: '.$this->pst[ 'email' ]."\r\n".
					'X-Mailer: PHP/'.phpversion();
		
		return mail( $this->send_to, $this->subject, $this->message, $headers );
	}
	
	
	
	/*
	 *	Saves the enquiry to DB for future reference
	 *
	 *	Return: BOOL
	 */
	public function save_data()
	{
		$names = "";
		$values = "";
		
		// Loop through the post array and get the fieldnames / values.
		foreach ($this->fields as $name)
		{
			$names .= "$name, ";
			$values .= "'".mysql_escape_string($this->pst[$name])."', ";
		}
		
		// Add a datetime field called 'date'
		$names .= "date";
		$values .= "'".date('Y-m-d H:i:s')."'";
		
		$sql = "INSERT INTO ".$this->db_table." ($names) VALUES ($values)";
		
		return mysql_query($sql);
	}
	
	
	
	/*
	 *	Returns a query object of the saved messages
	 */
	public function get_messages($limit = null, $offset = null)
	{
		$limitor = ' LIMIT ';
		
		if ($limit && $offset)
		{
			$limitor .= $offset.', '.$limit;
		}
		elseif ($limit)
		{
			$limitor .= '0, '. $limit;
		}
		else
		{
			$limitor = '';
		}
		
		$sql = "SELECT * FROM ".$this->db_table.$limitor;
		
		$data = mysql_query($sql);
		
		$messages_array = array();
		
		while($m = mysql_fetch_array($data))
		{
			$messages_array[] = $m;
		}
		
		return $messages_array;
	}
	
}

?>