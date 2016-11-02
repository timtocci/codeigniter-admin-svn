<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Login Page</title>
    </head>
    <body style="">
		<div style="position:absolute; top: 35%; left: 45%; ">


	    <?php
		echo form_open('login/dologin');
		$data = array(
              'name'        => 'username',
              'id'          => 'username',
              'value'       => '',
              'maxlength'   => '100',
              'size'        => '25',
              
            );
		echo "Name<br>";
		echo form_input($data);
		echo "<br> Password<br>";
		
				$data = array(
              'name'        => 'password',
              'id'          => 'password',
              'value'       => '',
              'maxlength'   => '100',
              'size'        => '25',
              
            );

		echo form_password($data);
		echo "<br>";
		echo form_submit('login', 'Login');
		echo form_close();
        ?>
		</div>
    </body>
</html>
