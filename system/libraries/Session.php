<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * DB2 Session
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright		Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 *
 * ************* DB2 Session VERSION *****************
 * @original author	ExpressionEngine Dev Team
 * @inspired by		Dready, Original DB Session mod - http://dready.jexiste.fr/dotclear/index.php?2006/09/13/19-reworked-session-handler-for-code-igniter
 * @author		Flash
 * @created		03/2008
 * @last updated	30/06/2008
 * @mod version		BETA v0.2
 */

// ------------------------------------------------------------------------

/**
 * Session Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
class Session {

	var $CI;
	var $now;
	var $encryption		= TRUE;
	var $use_database	= FALSE;
	var $session_table	= FALSE;
	var $sess_length	= 7200;
	var $sess_cookie	= 'ci_session';
	var $userdata		= array();
	var $gc_probability	= 5;
	var $flashdata_key 	= 'flash';
	var $time_to_update	= 300;

	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */
	function Session()
	{
		$this->CI =& get_instance();

		// CL_Auth config settings
		$this->track_activity = $this->CI->config->item('track_activity');
		$this->regen = $this->CI->config->item('regen');

		log_message('debug', "Session Class Initialized");
		$this->sess_run();
	}

	// --------------------------------------------------------------------

	/**
	 * Run the session routines
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_run()
	{
		/*
		 *  Set the "now" time
		 *
		 * It can either set to GMT or time(). The pref
		 * is set in the config file.  If the developer
		 * is doing any sort of time localization they
		 * might want to set the session time to GMT so
		 * they can offset the "last_activity" time
		 * based on each user's locale.
		 *
		 */

		if (is_numeric($this->CI->config->item('sess_time_to_update')))
		{
			$this->time_to_update = $this->CI->config->item('sess_time_to_update');
		}

		if (strtolower($this->CI->config->item('time_reference')) == 'gmt')
		{
			$now = time();
			$this->now = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));

			if (strlen($this->now) < 10)
			{
				$this->now = time();
				log_message('error', 'The session class could not set a proper GMT timestamp so the local time() value was used.');
			}
		}
		else
		{
			$this->now = time();
		}

		/*
		 *  Set the session length
		 *
		 * If the session expiration is set to zero in
		 * the config file we'll set the expiration
		 * two years from now.
		 *
		 */
		$expiration = $this->CI->config->item('sess_expiration');

		if (is_numeric($expiration))
		{
			if ($expiration > 0)
			{
				$this->sess_length = $this->CI->config->item('sess_expiration');
			}
			else
			{
				$this->sess_length = (60*60*24*365*2);
			}
		}

		// Do we need encryption?
		$this->encryption = $this->CI->config->item('sess_encrypt_cookie');

		if ($this->encryption == TRUE)
		{
			$this->CI->load->library('encrypt');
		}

		// Are we using a database?
		if ($this->CI->config->item('sess_use_database') === TRUE AND $this->CI->config->item('sess_table_name') != '')
		{
			$this->use_database = TRUE;
			$this->session_table = $this->CI->config->item('sess_table_name');
			$this->CI->load->database();
		}

		// Set the cookie name
		if ($this->CI->config->item('sess_cookie_name') != FALSE)
		{
			$this->sess_cookie = $this->CI->config->item('cookie_prefix').$this->CI->config->item('sess_cookie_name');
		}

		/*
		 *  Fetch the current session
		 *
		 * If a session doesn't exist we'll create
		 * a new one.  If it does, we'll update it.
		 *
		 */
		if ( ! $this->sess_read())
		{
			$this->sess_create();
		}
		else
		{
			// Activity Function for CL_Auth
			if ( $this->userdata['session_user_id'] != 0 AND $this->CI->config->item('CL_Auth') === TRUE AND $this->track_activity === TRUE )
			{
				$this->CI->load->model('cl_auth/users', 'users');

				$active = $this->now - $this->userdata['last_activity'];

				$this->CI->users->update_activity($this->userdata['session_user_id'], $active);
				$this->sess_update();
			}
			// We only update the session every five minutes
			elseif (($this->userdata['last_activity'] + $this->time_to_update) < $this->now)
			{
				$this->sess_update();
			}

		}

		// Delete expired sessions if necessary
		if ($this->use_database === TRUE)
		{
			$this->sess_gc();
		}

		// Delete 'old' flashdata (from last request)
	   	$this->_flashdata_sweep();

		// Mark all new flashdata as old (data will be deleted before next request)
	   	$this->_flashdata_mark();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_read()
	{
		// Fetch the cookie
		$session_id = $this->CI->input->cookie($this->sess_cookie);

		if ($session_id === FALSE)
		{
			log_message('debug', 'A session cookie was not found.');
			return FALSE;
		}

		// Is there a corresponding session in the DB?
		$this->CI->db->where('session_id', $session_id);

		// session should not have expired
		$this->CI->db->where('last_activity >', ($this->now - $this->sess_length) );

		// Does the IP Match?
		if ($this->CI->config->item('sess_match_ip') == TRUE)
		{
			$this->CI->db->where('ip_address', $this->CI->input->ip_address());
		}

		// Does the User Agent Match?
		if ($this->CI->config->item('sess_match_useragent') == TRUE)
		{
			$this->CI->db->where('user_agent', substr(htmlspecialchars((string) $this->CI->input->user_agent()), 0, 149));
		}

		// This section of code is new
		if ( $this->CI->config->item('CL_Auth') === TRUE )
		{
			$users_table = $this->CI->config->item('CL_table_prefix').$this->CI->config->item('CL_users_table');

			$this->CI->db->from($this->session_table);
			$this->CI->db->join($users_table, $users_table.'.id = '.$this->session_table.'.session_user_id', 'left');

			$query = $this->CI->db->get();
		}
		else
		{
			// Normal session query
			$query = $this->CI->db->get($this->session_table);
		}

		if ($query->num_rows() == 0)
		{
			$this->sess_destroy();
			return FALSE;
		}
		else
		{
			$row = $query->row();
			if (($row->last_activity + $this->sess_length) < $this->now)
			{
				$this->CI->db->where('session_id', $session_id);
				$this->CI->db->delete($this->session_table);
				$this->sess_destroy();
				return FALSE;
			} else {
				$session = @unserialize($row->session_data);
				if ( ! is_array($session) ) {
					$session = array();
				}
				$session['session_id'] = $session_id;
				$session['session_user_id'] = $row->session_user_id;
				$session['ip_address'] = $row->ip_address;
				$session['user_agent'] = $row->user_agent;
				$session['last_page'] = $row->last_page;
				$session['last_activity'] = $row->last_activity;

				// Overwrite important session_data vars with the DB record
				$session['user_id'] = $row->session_user_id;
				$session['username'] = $row->username;
				$session['group_id'] = $row->group_id;

				// Add your additional fields here...
			}
		}

		// Session is valid!
		$this->userdata = $session;
		unset($session);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Write the session cookie
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_write()
	{
		setcookie(
					$this->sess_cookie,
					$this->userdata['session_id'],
					$this->sess_length + time(),
					$this->CI->config->item('cookie_path'),
					$this->CI->config->item('cookie_domain'),
					0
				);
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_create()
	{
		$sessid = '';
		while (strlen($sessid) < 32)
		{
			$sessid .= mt_rand(0, mt_getrandmax());
		}

		$this->userdata = array(
							'session_id' 	=> md5(uniqid($sessid, TRUE)),
							'session_user_id' => 0,
							'ip_address' 	=> $this->CI->input->ip_address(),
							'user_agent' 	=> substr($this->CI->input->user_agent(), 0, 149),
							'last_page'		=> $this->CI->uri->uri_string(),
							'last_activity'	=> $this->now
							);


		$this->CI->db->query($this->CI->db->insert_string($this->session_table, $this->userdata));

		// Write the cookie
		$this->sess_write();
	}

	// --------------------------------------------------------------------

	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_update()
	{
		// Save the old session id so we know which record to
		// update in the database if we need it
		$old_sessid = $this->userdata['session_id'];
		if ( $this->regen == TRUE )
		{
			$new_sessid = '';
			while (strlen($new_sessid) < 32)
			{
				$new_sessid .= mt_rand(0, mt_getrandmax());
			}
			$new_sessid = md5(uniqid($new_sessid, TRUE));
		}
		else
		{
			$new_sessid = $old_sessid;
		}

		// Update the session data in the session data array
		$this->userdata['session_id'] = $new_sessid;
		$this->userdata['last_activity'] = $this->now;

		// format query array to update database
		$ud = $this->userdata;
		$sql_ary = array(
		'session_id'	=> $new_sessid,
		'session_user_id' => $ud['session_user_id'],
		'last_page' => $this->CI->uri->uri_string(), // Grab current page they are on
		'last_activity' => $ud['last_activity']);

		unset($ud['session_id'], $ud['session_user_id'], $ud['last_page'], $ud['last_activity'], $ud['user_agent'], $ud['ip_address']);

		$sql_ary['session_data'] = serialize($ud);
		$this->CI->db->query($this->CI->db->update_string($this->session_table, $sql_ary, array('session_id' => $old_sessid)));

		// Write the cookie
		$this->sess_write();
	}

	// --------------------------------------------------------------------

	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_destroy()
	{
		setcookie(
					$this->sess_cookie,
					'',
					($this->now - 31500000),
					$this->CI->config->item('cookie_path'),
					$this->CI->config->item('cookie_domain'),
					0
				);
	}

	// --------------------------------------------------------------------

	/**
	 * Garbage collection
	 *
	 * This deletes expired session rows from database
	 * if the probability percentage is met
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_gc()
	{
		srand(time());
		if ((rand() % 100) < $this->gc_probability)
		{
			$expire = $this->now - $this->sess_length;

			$this->CI->db->where("last_activity < {$expire}");
			$this->CI->db->delete($this->session_table);

			log_message('debug', 'Session garbage collection performed.');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a specific item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function userdata($item)
	{
		return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	mixed
	 */
	function all_userdata()
	{
		return ( ! isset($this->userdata)) ? FALSE : $this->userdata;
	}

	// --------------------------------------------------------------------

	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}

		$this->sess_update();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */
	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				unset($this->userdata[$key]);
			}
		}

		$this->sess_update();
	}

	// --------------------------------------------------------------------

	/**
	 * Strip slashes
	 *
	 * @access	public
	 * @param	mixed
	 * @return	mixed
	 */
	function strip_slashes($vals)
	{
		if (is_array($vals))
	 	{
	 		foreach ($vals as $key=>$val)
	 		{
	 			$vals[$key] = $this->strip_slashes($val);
	 		}
	 	}
	 	else
	 	{
	 		$vals = stripslashes($vals);
	 	}

	 	return $vals;
	}


	// ------------------------------------------------------------------------

	/**
	 * Add or change flashdata, only available
	 * until the next request
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_flashdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$flashdata_key = $this->flashdata_key.':new:'.$key;
				$new[$flashdata_key] = $val;
			}

			$this->set_userdata($new);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Keeps existing flashdata available to next request.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function keep_flashdata($key)
	{
		// 'old' flashdata gets removed.  Here we mark all
		// flashdata as 'new' to preserve it from _flashdata_sweep()
		// Note the function will return FALSE if the $key
		// provided cannot be found
		$old_flashdata_key = $this->flashdata_key.':old:'.$key;
		$value = $this->userdata($old_flashdata_key);

		$new_flashdata_key = $this->flashdata_key.':new:'.$key;
		$this->set_userdata($new_flashdata_key, $value);
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch a specific flashdata item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function flashdata($key)
	{
		$flashdata_key = $this->flashdata_key.':old:'.$key;
		return $this->userdata($flashdata_key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Identifies flashdata as 'old' for removal
	 * when _flashdata_sweep() runs.
	 *
	 * @access	private
	 * @return	void
	 */
	function _flashdata_mark()
	{
		$userdata = $this->all_userdata();
		foreach ($userdata as $name => $value)
		{
			$parts = explode(':new:', $name);
			if (is_array($parts) && count($parts) === 2)
			{
				$new_name = $this->flashdata_key.':old:'.$parts[1];
				$this->userdata[$new_name] = $value;
				unset($this->userdata[$name]);
			}
		}

		// Don't update session; Is flashdata exists, _flashdata_sweep will write the new data automatically anyway
	}

	// ------------------------------------------------------------------------

	/**
	 * Removes all flashdata marked as 'old'
	 *
	 * @access	private
	 * @return	void
	 */

	function _flashdata_sweep()
	{
		$i=0;
		$userdata = $this->all_userdata();
		foreach ($userdata as $key => $value)
		{
			if (strpos($key, ':old:'))
			{
				unset($this->userdata[$key]);
				$i++;
			}
		}

		if ($i > 0) {
			$this->sess_update();
		}
	}

}
// END Session Class
?>