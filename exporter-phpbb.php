<?php

/**
 * Includes necessary files.
 */
function export_init ()
{
	if (!class_exists ('BBXP'))
	{
		require_once ('exporter/bbxp.php');
		require_once ('exporter/bbxp-phpbb.php');
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
	$bbxp = new BBXP_phpBB;
	$bbxp->db = new BPDB;
	$bbxp->initialize_db ($_POST['prefix']);
	$filename = 'phpbb' . date ('Y-m-d') . '.xml';

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

	<h2><?php _e ('Export') ?></h2>
	<p><?php _e ('When you submit the form below, phpBB will generate a BBXF file for you to save to your computer.'); ?></p>
	<p><?php _e ('This file will contain data about your users, forums, topics, and posts.  You can use the Import function of another phpBB installation or another compatible web forums software to import this data.'); ?></p>
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
			<input type="text" name="password" />
		</fieldset>
		<fieldset><legend>MySQL Table Prefix</legend>
			<input type="text" name="prefix" />
		</fieldset>
		<p class="submit">
			<input type="submit" name="submit" value="<?php _e ('Download Export File'); ?>" />
			<input type="hidden" name="exporting" value="true" />
		</p>
	</form>

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

