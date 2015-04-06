<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>HALAMAN LOGIN :: SISTEM INFORMASI MANAJEMEN PROYEK :: SIMPRO :: &copy; PT. NINDYA KARYA</title>
        <meta name="description" content="Custom Login Form Styling with CSS3" />
        <meta name="keywords" content="css3, login, form, custom, input, submit, button, html5, placeholder" />
        <meta name="author" content="Codrops" />
        <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.gif" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/login_style/css/style.css" />
		<script src="<?php echo base_url(); ?>assets/login_style/js/modernizr.custom.63321.js"></script>
		<!--[if lte IE 7]><style>.main{display:none;} .support-note .note-ie{display:block;}</style><![endif]-->
    </head>
    <body>
        <div class="container">
		
			<!-- Codrops top bar -->

			
			<br><br><br><br><br><br>
			
			<section class="main">
			
				
				<form class="form-1" action="<?=base_url()?>main/login/cek_login" method="post"><div align="center"><img title="Sistem Informasi Manajemen Proyek" alt="Sistem Informasi Manajemen Proyek" src="<?php echo base_url(); ?>assets/login_style/images/logonk_new.png"><br><br>
					<p class="field">
						<input type="text" name="uname" placeholder="Username" title="username" autocomplete="off" autofocus required>
						 <i class="icon-user icon-large"></i>
					</p>
						<p class="field">
							<input type="password" name="upass" placeholder="Password" title="password" autocomplete="off" required>
							 <i class="icon-lock icon-large"></i>
					</p>
					<p class="submit">
						 <button type="submit" name="submit" title="Login SIMPRO"  ><i class="icon-arrow-right icon-large"></i></button>
					</p>
				</form>
			</section>
			  
        </div>
		
    </body>
</html>