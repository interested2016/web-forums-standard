<?php
/*
Plugin Name: Export
Plugin URI: http://www.bbpress.org/
Description: Allows administrators to export forum data.
Author: Dan Larkin
Version: 0.1 alpha
Author URI: http://www.stealyourcarbon.net/
*/

/**
 * Includes necessary files.
 */
function export_init ()
{
	require_once ('wfxp.php');
	require_once ('wfxp-bbpress.php');
}

/**
 * Executes all necessary functions to make the exportation happen.
 */
function export_main ()
{
	global $bbdb;
	export_init ();
	$bbxp = new WFXP_bbPress ($bbdb);
	$bbxp->db = $bbdb;
	$filename = 'bbpress' . date ('Y-m-d') . '.xml';

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

<div class="wrap">
	<h2><?php _e ('Export') ?></h2>
	<p><?php _e ('When you click the button below, bbPress will generate an XML file for you to save to your computer.'); ?></p>
	<p><?php _e ('This file will contain data about your users, forums, topics, and posts.  You can use the Import function of another bbPress installation or another compatible web forums software to import this data.'); ?></p>
	<form action="" method="get">
		<p class="submit">
			<input type="submit" name="submit" value="<?php _e ('Download Export File'); ?>" />
			<input type="hidden" name="exporting" value="true" />
		</p>
	</form>
</div>

<?php
}

/**
 * Adds export link to admin menu.
 */
function export_add_admin ()
{
	global $bb_submenu;
	$bb_submenu['content.php'][] = array (__('Export'), 'use_keys', 'export_page', 'exporter-bbpress.php');
}

if ('true' == $_GET['exporting'] )
{
	add_action ('bb_init', 'export_main');
}

add_action ('bb_admin_menu_generator', 'export_add_admin');

?>