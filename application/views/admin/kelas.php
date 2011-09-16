<?php
$this->load->view($this->config->item('CL_header'));
?>

	<?php if ($avaidata != 0):?>
	<?php foreach($infosekolah as $record):
	$tipesekolah = $record->TipeSekolah;
	endforeach;?>
	<fieldset>
	
	<?php if ($tipesekolah == 1):
	$y=6; for ($x = 1; $x <= $y; $x++) {
		echo form_label('Jumlah Kelas '.$x.' = ')."<br />";
	}?>
	<?php endif;?>
	</fieldset>
	<?php else: ?>  
	<div class="warning_box">
        Tipe dan nama sekolah belum diatur.
    </div>
    <?php endif;?>

<?php
$this->load->view($this->config->item('CL_footer'));