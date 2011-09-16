<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IN ADMIN PANEL | Powered by INDEZINER</title>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>style/style.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/ddaccordion.js"></script>
<script type="text/javascript">
ddaccordion.init({
	headerclass: "submenuheader", //Shared CSS class name of headers group
	contentclass: "submenu", //Shared CSS class name of contents group
	revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false 
	defaultexpanded: [], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: false, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", ""], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["suffix", "<img src='images/plus.gif' class='statusicon' />", "<img src='images/minus.gif' class='statusicon' />"], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
})
</script>

<script type="text/javascript" src="<?php echo base_url(); ?>js/jconfirmaction.jquery.js"></script>
<script type="text/javascript">
	
	$(document).ready(function() {
		$('.ask').jConfirmAction();
	});
	
</script>

<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/niceforms.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url(); ?>style/niceforms-default.css" />

</head>
<body><?php
$form = $this->validation;

$username = array(
'name'		=> 'username',
'id'		=> 'username',
'size'		=> 20,
'maxlength'	=> $this->config->item('CL_login_maxlength'));


$password = array(
'name'		=> 'password',
'id'		=> 'password',
'size'		=> 20,
'maxlegnth'	=> $this->config->item('CL_password_max'));


$remember = array(
'name'	=> 'remember',
'id'	=> 'remember',
'value'	=> true,
'style' => 'margin: 0');

$confirmation_code = array(
'name'	=> 'captcha_code',
'id'	=> 'captcha_code',
'maxlength'	=> 8);?>
<div id="main_container">

	<div class="header_login">
    <div class="logo"><a href="#"><img src="<?php echo base_url(); ?>images/logo.gif" alt="" title="" border="0" /></a></div>
    
    </div>

     
         <div class="login_form">
         
         <h3>Admin Panel Login 2</h3>
         
         <?php echo anchor($this->config->item('CL_forgotten_uri'), 'Forgotten password ?', array('class' => 'forgot_pass')); ?>
         <?php $attributes = array('class' => 'niceform');
         echo form_open($this->uri->uri_string(), $attributes)?>
         
                <fieldset>
                    <dl>
                        <dt><?php echo form_label('Username ', $username['id']).":"; ?></dt>
                        <dd><?php echo form_input($username); ?><?php echo $form->username_error;?></dd>
                    </dl>
                    <dl>
                        <dt><?php echo form_label('Password ', $password['id']).":"; ?></label></dt>
                        <dd><?php echo form_password($password); ?><?php echo $form->password_error;?></dd>
                    </dl>
                    
                    <?php
                    if ($this->config->item('CL_captcha_login') AND $this->cl_auth->captcha == true): ?>
                    <dl>
                    <dt><?php echo form_label('Enter the code :'); ?><!-- exactly as it appears. All letters are case insensitive, there is no zero.--></dt>
                    <dd><?php echo $this->cl_auth->captcha_img;?></dd>
                    </dl>
                    
                    <dl>
                    <dt><?php echo form_label('Confirmation Code :', $confirmation_code['id']);?></dt>
                    <dd><?php echo form_input($confirmation_code);?>
                    <?php echo $form->captcha_code_error;?></dd>
                    <?php
                    endif;?>
                    </dl>
        
                    <dl>
                        <dt><label></label></dt>
                        <dd>
                    <?php echo form_checkbox($remember); ?>
                    <?php echo form_label('Remember me', $remember['id'], array('class' => 'check_label')); ?>
                        </dd>
                    </dl>
                    
                    <dl>
                    <?php if ( $this->cl_auth->user_error ): ?>
                    <p class="error"><?php echo $this->cl_auth->user_error; ?></p>
                    <?php endif;?>
                    </dl>
                    
                     <dl class="submit">
                    <input type="submit" name="submit" id="submit" value="Enter" />
                     </dl>
                    
                </fieldset>
                
         </form>
         </div>  
          
	
    
    <div class="footer_login">
    
    	<div class="left_footer_login">IN ADMIN PANEL | Powered by <a href="http://indeziner.com">INDEZINER</a></div>
    	<div class="right_footer_login"><a href="http://indeziner.com"><img src="images/indeziner_logo.gif" alt="" title="" border="0" /></a></div>
    
    </div>

</div>		
</body>
</html>