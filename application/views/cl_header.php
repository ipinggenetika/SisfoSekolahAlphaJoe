<?php
/*
* Login Bar
* ---------
* If visitor is not a logged in user, display our Login Bar
*/
if ( !$this->cl_auth->isValidUser() ) :
$this->load->view($this->config->item('CL_LoginPage'));
else:?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IN ADMIN PANEL | Powered by INDEZINER</title>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>style/style.css" />
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/ddaccordion.js"></script>
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
	togglehtml: ["suffix", "<img src='<?php echo base_url();?>/images/plus.gif' class='statusicon' />", "<img src='<?php echo base_url();?>images/minus.gif' class='statusicon' />"], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
})
</script>
<script src="<?php echo base_url();?>js/jquery.jclock-1.2.0.js.txt" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jconfirmaction.jquery.js"></script>
<script type="text/javascript">
	
	$(document).ready(function() {
		$('.ask').jConfirmAction();
	});
	
</script>
<script type="text/javascript">
$(function($) {
    $('.jclock').jclock();
});
</script>

<script language="javascript" type="text/javascript" src="<?php echo base_url();?>js/niceforms.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url();?>style/niceforms-default.css" />

</head>

<body>



<div id="main_container">
    <div class="header">
    <div class="logo"><a href="#"><img src="<?php echo base_url();?>images/logo.gif" alt="" title="" border="0" /></a></div>
    
    <div class="right_header">Hai <?php echo $this->cl_auth->getUsername(); ?>, <?php echo anchor($this->config->item('CL_changepass_uri'), 'Ganti Password');?> | <!--<a href="#" class="messages">(3) Messages</a> | --><?php echo anchor($this->config->item('CL_logout_uri'), 'Keluar', array('class' => 'logout'));?></div>
    <div class="jclock"></div>
    </div>
    
     <div class="main_content">
    
                    <div class="menu">
                    <ul>
                    <li><a href="<?php echo base_url(); ?>">Admin Home</a></li>
                    <li><a href="list.html">Administrasi Sekolah<!--[if IE 7]><!--></a><!--<![endif]-->
                    <!--[if lte IE 6]><table><tr><td><![endif]-->
                        <ul>
          				<li><a href="" title="">Kepala Sekolah dan Wakil</a></li>
                        <li><a href="" title="">Guru</a></li>
                        <li><a href="" title="">Tata Usaha</a></li>
                        </ul>
                    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
                    </li>
                    <li><a href="login.html">Administrasi Murid<!--[if IE 7]><!--></a><!--<![endif]-->
                    <!--[if lte IE 6]><table><tr><td><![endif]-->
                    </li>
                    <li><a href="login.html">PKBM<!--[if IE 7]><!--></a><!--<![endif]-->
                    <!--[if lte IE 6]><table><tr><td><![endif]-->
                        <ul>
                        <li><?php echo anchor($this->config->item('CL_KelasControl'), 'Kelas');?></li>
                        <li><a href="" title="">Mata Pelajaran</a></li>
                        <li><a href="" title="">Nilai Siswa</a></li>
                        <li><a href="" title="">Absensi Siswa</a></li>
                        </ul>
                    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
                    <!--[if lte IE 6]><table><tr><td><![endif]-->
                    </li>
                    <li><a href="login.html">Inventaris Sekolah<!--[if IE 7]><!--></a><!--<![endif]-->
                    <!--[if lte IE 6]><table><tr><td><![endif]-->
                    </li>
                    <li><a href="">Administrasi</a></li>
                    <li><a href="" title="">Pengaturan</a>
                     <!--[if lte IE 6]><table><tr><td><![endif]-->
                        <ul>
                        <li><?php echo anchor($this->config->item('CL_SettingControl'), 'Tipe dan Nama Sekolah');?></li>
                        <li><?php echo anchor($this->config->item('CL_SettingKelas'), 'Pengaturan Kelas');?></li>
                        </ul>
                    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
                    </li>
                    </ul>
                    </div> 
         
                   <div class="center_content">  
    
    
    
    <div class="left_content">
    
    		<div class="sidebar_search">
          
            <input type="text" name="" class="search_input" value="search keyword" onclick="this.value=''" />
            <input type="image" class="search_submit" src="<?php echo base_url();?>images/search.png" />
                        
            </div>
    
            <div class="sidebarmenu">
            
                <a class="menuitem submenuheader" href="">Subcategories</a>
                <div class="submenu">
                    <ul>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    </ul>
                </div>
                <a class="menuitem submenuheader" href="" >Sidebar Settings</a>
                <div class="submenu">
                    <ul>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    </ul>
                </div>
                <a class="menuitem submenuheader" href="">Add new products</a>
                <div class="submenu">
                    <ul>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    <li><a href="">Sidebar submenu</a></li>
                    </ul>
                </div>
                <a class="menuitem" href="">User Reference</a>
                <a class="menuitem" href="">Blue button</a>
                
                <a class="menuitem_green" href="">Green button</a>
                
                <a class="menuitem_red" href="">Red button</a>
                    
            </div>
            
            
            <div class="sidebar_box">
                <div class="sidebar_box_top"></div>
                <div class="sidebar_box_content">
                <h3>User help desk</h3>
                <img src="<?php echo base_url();?>images/info.png" alt="" title="" class="sidebar_icon_right" />
                <p>
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>                
                </div>
                <div class="sidebar_box_bottom"></div>
            </div>
            
            <div class="sidebar_box">
                <div class="sidebar_box_top"></div>
                <div class="sidebar_box_content">
                <h4>Important notice</h4>
                <img src="<?php echo base_url();?>images/notice.png" alt="" title="" class="sidebar_icon_right" />
                <p>
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>                
                </div>
                <div class="sidebar_box_bottom"></div>
            </div>
            
            <div class="sidebar_box">
                <div class="sidebar_box_top"></div>
                <div class="sidebar_box_content">
                <h5>Download photos</h5>
                <img src="<?php echo base_url();?>images/photo.png" alt="" title="" class="sidebar_icon_right" />
                <p>
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>                
                </div>
                <div class="sidebar_box_bottom"></div>
            </div>  
            
            <div class="sidebar_box">
                <div class="sidebar_box_top"></div>
                <div class="sidebar_box_content">
                <h3>To do List</h3>
                <img src="<?php echo base_url();?>images/info.png" alt="" title="" class="sidebar_icon_right" />
                <ul>
                <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</li>
                 <li>Lorem ipsum dolor sit ametconsectetur <strong>adipisicing</strong> elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
                  <li>Lorem ipsum dolor sit amet, consectetur <a href="#">adipisicing</a> elit.</li>
                   <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</li>
                    <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</li>
                     <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</li>
                </ul>                
                </div>
                <div class="sidebar_box_bottom"></div>
            </div>
              
    
    </div>  
    <div class="right_content">  

    <?php
    endif;
    ?>