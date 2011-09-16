<?php
class Admin Extends Model {

	function Admin()
	{
		parent::Model();
		
		// Other stuff
		$this->_prefix = $this->config->item('CL_table_prefix');
		$this->_is = $this->_prefix.'InfoSekolah';
	}
	
	function insrt_info_sekolah($nama_sekolah, $tipe_sekolah)
	{
		$data = array(
		'NamaSekolah' => $nama_sekolah,
		'TipeSekolah' => $tipe_sekolah
		);
		
		$this->db->insert($this->_is, $data);
	}
	
	function get_info_sekolah()
	{
		$q = $this->db->get($this->_is);
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $value) {
				$data[] = $value;
			}
			return $data;
		}
	}
	
	function updt_info_sekolah($nama_sekolah, $tipe_sekolah)
	{
		$data = array(
		'NamaSekolah' => $nama_sekolah,
		'TipeSekolah' => $tipe_sekolah
		);
		
		$this->db->where('IdInfoSekolah', 1);
		$this->db->update($this->_is, $data);
	}
	
	
	
}