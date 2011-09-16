<?php

$this->load->view($this->config->item('CL_header'));

$form = $this->validation;

$username = array(
'name'	=> 'username',
'id'	=> 'username',
'maxlength'	=> $this->config->item('CL_username_max'),
'size'	=> 30,
'value' => isset($form->username) ? $form->username : '');

$password = array(
'name'	=> 'password',
'id'	=> 'password',
'maxlength'	=> $this->config->item('CL_password_max'),
'size'	=> 30,
'value' => isset($form->password) ? $form->password : '');

$password_conf = array(
'name'	=> 'password_confirm',
'id'	=> 'password_confirm',
'maxlength'	=> $this->config->item('CL_password_max'),
'size'	=> 30,
'value' => isset($form->password_confirm) ? $form->password_confirm : '');

$email = array(
'name'	=> 'email',
'id'	=> 'email',
'maxlength'	=> 80,
'size'	=> 30,
'value'	=> isset($form->email) ? $form->email : '');

$confirmation_code = array(
'name'	=> 'captcha_code',
'id'	=> 'captcha_code',
'maxlength'	=> 6);

$terms = array(
'name' => 'terms',
'id' => 'terms',
'checked' => (!empty($form->error_string) ? $form->set_checkbox('terms', true) : false),
'value' => 'true');

?>

<fieldset><legend accesskey="D" tabindex="1">Register</legend>
<?php echo form_open($this->uri->uri_string())?>

<dl>
	<!--USERNAME-->
	<dt><?php echo form_label('Username', $username['id']);?></dt>
	<dd><?php echo form_input($username)?>
    <?php echo $form->username_error;?></dd>

    <!--PASSWORD-->
	<dt><?php echo form_label('Password', $password['id']);?></dt>
	<dd><?php echo form_password($password)?>
    <?php echo $form->password_error;?></dd>

	<dt><?php echo form_label('Confirm Password', $password_conf['id']);?></dt>
	<dd><?php echo form_password($password_conf);?>
	<?php echo $form->password_confirm_error;?></dd>

	<dt><?php echo form_label('Email Address', $email['id']);?></dt>
	<dd><?php echo form_input($email);?>
	<?php echo $form->email_error;?></dd>

<?php
if ($this->config->item('CL_captcha_registration') AND $this->cl_auth->captcha == true): ?>
	<dt>Enter the code exactly as it appears. All letters are case insensitive, there is no zero.</dt>
	<dd><?php echo $this->cl_auth->captcha_img;?></dd>

	<dt><?php echo form_label('Confirmation Code', $confirmation_code['id']);?></dt>
	<dd><?php echo form_input($confirmation_code);?>
	<?php echo $form->captcha_code_error;?></dd>
<?php
endif;
?>

	<dt></dt>
	<dd>
	<div style="display: block; overflow: auto; width: 80%; height: 150px; padding: 15px; border: 1px solid #555;">
<?php echo $this->load->view($this->config->item('CL_terms_page')); ?>
	</div>
	</dd>

	<dt></dt>
	<dd><?php echo form_checkbox($terms)." ".form_label('I Agree to the Terms &amp; Conditions stated above', $terms['id']); ?>
	<?php if ($form->terms_error) :?>
	<p class="error">You must agree to the Terms &amp; Conditions before you can continue.</p>
	<?php endif; ?></dd>

	<dt></dt>
	<dd style="text-align: center"><?php echo form_submit('register','Register');?></dd>
</dl>

<?php echo form_close()?>
</fieldset>

<?php
$this->load->view($this->config->item('CL_footer'));
?>