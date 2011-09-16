<?php
/*
*
* @package  CL_Auth
* @type     Controller
* @author   Jason Ashdown aka Flash
* @version  BETA v0.2
*
*/

class Auth extends Controller
{
	function Auth()
	{
		parent::Controller();

		$this->load->helper('form');
		$this->load->library('validation');
	}

/*
*
*	Form Validation Functions
*
*/

	function username_check($username)
	{
		if ($this->cl_auth->username_check($username))
		{
			$this->validation->set_message('username_check', 'The username '.$username.' is not available.');
			return false;
		}
		else
		{
			return true;
		}
	}

	function username_format($username)
	{
		if ($this->cl_auth->username_format($username))
		{
			$this->validation->set_message('username_format', 'Your username can only contain letters or numbers.');
			return false;
		}
		else
		{
			return true;
		}
	}

	function email_check($email)
	{
		if ($this->cl_auth->email_check($email))
		{
			$this->validation->set_message('email_check', 'The email '.$email.' has already been used.');
			return false;
		}
		else
		{
			return true;
		}
	}


	function captcha_check($code)
	{
		// Captcha Expired
		list($usec, $sec) = explode(" ", microtime());
		$now = ((float)$usec + (float)$sec);

		if ( ($this->session->flashdata('captcha_time') + $this->config->item('CL_captcha_expire')) < $now )
		{
			// Will replace this error msg with $lang
			$this->validation->set_message('captcha_check', 'Your confirmation code has expired. Please try again.');
			//$this->cl_auth->captcha();
			return false;
		}
		elseif ( $code != $this->session->flashdata('captcha_word') )
		{
			$this->validation->set_message('captcha_check', 'Your confirmation code does not match the one in the image. Try again.');
			//$this->cl_auth->captcha();
			return false;
		}

		return true;
	}
/*
* End of Validation Function
* -------------------------------------------------------------------
*
*/

/*
*
*	Auth Controller Functions
*	BETA v0.2
*
*/

	function index()
	{
		$this->login();
	}

	function login()
	{
		if ( $this->input->post('redirect_url') )
		{
			$this->cl_auth->redirect_url = $this->input->post('redirect_url');
		}
		$this->cl_auth->login_form();
	}

	function logout()
	{
		$this->cl_auth->logout();
		$this->cl_auth->message = "You have been logged out.";
		$this->load->view($this->config->item('CL_logout_page'));
	}

	function register()
	{
		$this->cl_auth->register_form();
	}

	function activate()
	{
		$username = $this->uri->segment(3);
		$key = $this->uri->segment(4);

		if ( $this->cl_auth->activate($username, $key) ) // Activate Function
		{
			$this->cl_auth->message = "You have been successfully activated! Proceed to ".anchor(site_url($this->config->item('CL_login_uri')), 'Login');
			$this->load->view($this->config->item('CL_activate_success'));
		}
		else
		{
			$this->cl_auth->message = "The activation code you entered was incorrect. Please check your email again.";
			$this->load->view($this->config->item('CL_activate_failed'));
		}
	}

	function forgotten()
	{
		$val = $this->validation;

		$rules['login'] = "trim|required|xss_clean";

		$val->set_rules($rules);

		$fields['login'] = 'Username/Email Address';

		$val->set_fields($fields);

		if ($val->run() == TRUE AND $this->cl_auth->forgotten_pass($val->login))
		{
			$this->cl_auth->message = "An email has been sent to you with instructions with how to activate your new password.";
			$this->load->view($this->config->item('CL_forgotten_success'));
		}
		else
		{
			$this->load->view($this->config->item('CL_forgotten_page'));
		}
	}

	function reset()
	{
		$user_id = $this->uri->segment(3);
		$key = $this->uri->segment(4);

		if ( !$this->cl_auth->reset_pass($user_id, $key) )
		{
			$this->cl_auth->message = "You have successfully reset you password, ".anchor(site_url($this->config->item('CL_login_uri')), 'Login');
			$this->load->view($this->config->item('CL_reset_success'));
		}
		else
		{
			$this->cl_auth->message = "Reset failed. Your username and key are incorrect. Please check your email again and follow the instructions.";
			$this->load->view($this->config->item('CL_reset_failed'));
		}
	}

	function changepassword()
	{
		// Protect this function
		$this->cl_auth->check();

		$val = $this->validation;

		$rules['old_pass'] = "trim|required|xss_clean";
		$rules['new_pass'] = "trim|required|matches[confirm_pass]|xss_clean";
		$rules['confirm_pass'] = "trim|required|xss_clean";

		$val->set_rules($rules);

		$fields['old_pass'] = 'Old Password';
		$fields['new_pass'] = 'New Password';
		$fields['confirm_pass'] = ' Confirm New Password';

		$val->set_fields($fields);

		if ( $val->run() AND $this->cl_auth->change_password($val->old_pass, $val->new_pass) )
		{
			$this->cl_auth->message = "Your password has successfully been changed.";
			$this->load->view($this->config->item('CL_changepass_success'));
		}
		else
		{
			$this->load->view($this->config->item('CL_changepass_page'));
		}
	}

	function banned()
	{
		$this->cl_auth->message = "No Access allowed. You have been banned.";
		$this->load->view($this->config->item('CL_banned_page'));
	}

	function deny()
	{
		$this->cl_auth->message = "DENIED!! You don't have enough privileges to access this area.";
		$this->load->view($this->config->item('CL_deny_page'));
	}

	function terms()
	{
		$this->load->view($this->config->item('CL_header'));
		$this->load->view($this->config->item('CL_terms_page'));
		$this->load->view($this->config->item('CL_footer'));
	}
	
	// FUNGSI VALIDASI ALPHA DASH SPACE
	function alpha_dash_space($str)
    {
        return ( ! preg_match("/^([-a-z0-9_ ])+$/i", $str)) ? FALSE : TRUE;
    } 
    
    // ********** BEGIN OF CONTROLER FOR ADMIN ********************/
    
	function setting()
	{
		$this->cl_auth->check();
		$this->cl_auth->admin_dbconf();
		$data['infosekolah'] = $this->admin->get_info_sekolah();
		$data['avaidata'] = $this->db->affected_rows($data['infosekolah']);
		$this->load->view($this->config->item('CL_SettingPage'), $data);
	}
	
 
	
	function setting_handle()
	{
		$this->cl_auth->check();
		$this->cl_auth->admin_dbconf();
		$this->load->library('form_validation');
		$rules= array(
            array(
                'field' => 'nama_sekolah',
                'label' => 'Nama Sekolah',
                'rules' => 'trim|required|min_length[2]|max_length[30]|xss_clean|alpha_dash_space'
            ),
            
             array(
                'field' => 'tipe_sekolah',
                'label' => 'Tipe Sekolah',
                'rules' => 'required'
            )
            		);
        $this->form_validation->set_rules($rules); 
       
		if ($this->form_validation->run() == FALSE) {
			$data['infosekolah'] = $this->admin->get_info_sekolah();
			$data['avaidata'] = $this->db->affected_rows($data['infosekolah']);
			$this->load->view($this->config->item('CL_SettingPage'), $data);
		}
		else {
			$nama_sekolah = $this->input->post('nama_sekolah');
			$tipe_sekolah = $this->input->post('tipe_sekolah');
			$data['infosekolah'] = $this->admin->get_info_sekolah();
			$avaidata = $this->db->affected_rows($data['infosekolah']);
			if ($avaidata == 0):
			$this->admin->insrt_info_sekolah($nama_sekolah, $tipe_sekolah);
			else :
			$this->admin->updt_info_sekolah($nama_sekolah, $tipe_sekolah);
			endif;
			$data['infosekolah'] = $this->admin->get_info_sekolah();
			$data['avaidata'] = $this->db->affected_rows($data['infosekolah']);
			$this->load->view($this->config->item('CL_SettingPage'), $data);
			}
	}
	
	   // ********** END OF CONTROLER FOR ADMIN ********************/
	   
	
	   // ********** BEGIN OF CONTROLER FOR PKBM ********************/
	   function setting_kelas()
	   {
	   	
	   	$this->cl_auth->check();
		$this->cl_auth->admin_dbconf();
		$data['infosekolah'] = $this->admin->get_info_sekolah();
		$data['avaidata'] = $this->db->affected_rows($data['infosekolah']);
		$this->load->view($this->config->item('CL_SettingKelasPage'), $data);
		
	   }
	   
		function kelas()
		{
			$this->cl_auth->check();
			$this->load->view($this->config->item('CL_KelasPage'));
		}
	   
		// ********** BEGIN OF CONTROLER FOR PKBM ********************/
        
        
}