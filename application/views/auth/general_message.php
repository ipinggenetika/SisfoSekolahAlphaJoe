<?php
$this->load->view($this->config->item('CL_header'));
?>
<?php //$this->load->view($this->config->item('CL_leftbar'));
?>
<p class="message"><?php echo $this->cl_auth->message; ?></p>

<?php
$this->load->view($this->config->item('CL_footer'));
?>