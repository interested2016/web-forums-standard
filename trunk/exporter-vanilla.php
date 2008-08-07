<?php

/**
 * Includes necessary files.
 */
function export_init ()
{
	if (!class_exists ('BBXP'))
	{
		require_once ('exporter/bbxp.php');
		require_once ('exporter/bbxp-vanilla.php');
		require_once ('exporter/bpdb.php');
	}
}

/**
 * Executes all necessary functions to make the exportation happen.
 */
function export_main ()
{
	global $bbdb;
	export_init ();
	$bbxp = new BBXP_Vanilla;
	$bbxp->db = new BPDB;
	$bbxp->initialize_db ($_POST['prefix']);
	$filename = 'vanilla' . date ('Y-m-d') . '.xml';

	$bbxp->write_header ($filename);
	$bbxp->write_users ();
	$bbxp->write_forums ();
	$bbxp->write_topics ();
	$bbxp->write_footer ();

	die ();
		
}

/**
 * Displays the admin export page.
 *
 * Gives a simple explanation of how the export file works and gives
 * users a nice shiny button to click.
 */
function export_page ()
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 STRICT//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Vanilla BBXF Exporter</title>
	</head>
	<body>

		<h2>Export') ?></h2>
		<p>When you submit the form below, Vanilla will generate a BBXF file for you to save to your computer.</p>
		<p>This file will contain data about your users, forums, topics, and posts.  You can use the Import function of another Vanilla installation or another compatible web forums software to import this data.</p>
		<form action="" method="post">
			<fieldset><legend>MySQL Hostname</legend>
				<input type="text" name="host" />
			</fieldset>
			<fieldset><legend>MySQL Database</legend>
				<input type="text" name="database" />
			</fieldset>
			<fieldset><legend>MySQL Username</legend>
				<input type="text" name="user" />
			</fieldset>
			<fieldset><legend>MySQL Password</legend>
				<input type="password" name="password" />
			</fieldset>
			<fieldset><legend>MySQL Table Prefix</legend>
				<input type="text" name="prefix" />
			</fieldset>
			<p class="submit">
				<input type="submit" name="submit" value="Download Export File" />
				<input type="hidden" name="exporting" value="true" />
			</p>
		</form>

	</body>
</html>
<?php
}

if ('true' == $_POST['exporting'] )
{
	export_main ();
}
else
{
	export_page ();
}	

?>

