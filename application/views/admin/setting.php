<?php
$this->load->view($this->config->item('CL_header'));
?>
<?php 
	$nama_sekolah = array(
	'name'		=> 'nama_sekolah',
	'id'		=> 'nama_sekolah',
	'size'		=> 21,
	'maxlength'	=> 30);
?>
  <h2>Pengaturan Tipe dan Nama Sekolah</h2>
  <div class="form">
  <?php $attributes = array('class' => 'niceform');
  echo form_open('auth/setting_handle', $attributes); ?>
  <fieldset>
   <dl>
   <dt><label for="tipesekolah">Tipe Sekolah :</label></dt>
   <dd>
   <?php if ($avaidata != 0): foreach ($infosekolah as $row):
   $tipesekolah = $row->TipeSekolah;
   endforeach; ?>
   <input type="radio" <?php if ($tipesekolah == '3'):?>checked="checked"<?php endif;?>name="tipe_sekolah" id="" value="3" /><label class="check_label">SMA</label>
   <input type="radio" <?php if ($tipesekolah == '2'):?>checked="checked"<?php endif;?>name="tipe_sekolah" id="" value="2" /><label class="check_label">SMP</label>
   <input type="radio" <?php if ($tipesekolah == '1'):?>checked="checked"<?php endif;?>name="tipe_sekolah" id="" value="1" /><label class="check_label">SD</label>
   <?php echo form_error('tipe_sekolah'); ?>
   <?php else:?>
   
   <input type="radio" name="tipe_sekolah" id="" value="3" /><label class="check_label">SMA</label>
   <input type="radio" name="tipe_sekolah" id="" value="2" /><label class="check_label">SMP</label>
   <input type="radio" name="tipe_sekolah" id="" value="1" /><label class="check_label">SD</label>
   <?php echo form_error('tipe_sekolah'); ?>
   <?php endif;?>
   </dd>
   </dl>
   
   <dl>
   <dt><label for="namasekolah">Nama Sekolah :</label></dt>
   <?php if ($avaidata != 0): 
   foreach ($infosekolah as $row):
   $namasekolah = $row->NamaSekolah;
   endforeach;
   ?>
   <dd><?php $data = array(
              'name'        => 'nama_sekolah',
              'id'          => 'nama_sekolah',
              'value'       => $namasekolah,
              'maxlength'   => '30',
              'size'        => '21',
            );

   echo form_input($data); ?><?php echo form_error('nama_sekolah'); ?></dd>
   <?php else:?>
   <dd><?php echo form_input($nama_sekolah); ?><?php echo form_error('nama_sekolah'); ?></dd>
   <?php endif;?>
   </dl>
   
   <dl class="submit">
   <?php if ($avaidata != 0): 
   echo form_submit('submit', 'Perbaharui');
   else: echo form_submit('submit', 'Simpan'); endif;?>
   </dl>
   </fieldset>
  <?php echo form_close();?>
</div>
<?php
$this->load->view($this->config->item('CL_footer'));