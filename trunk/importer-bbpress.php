<?php
/*
Plugin Name: Import
Plugin URI: http://www.bbpress.org/
Description: Allows administrators to import forum data from a standard XML file.
Author: Dan Larkin
Version: 0.1 alpha
Author URI: http://www.stealyourcarbon.net/
*/

/**
 * Includes necessary files.
 */
function import_init ()
{
	require_once ('wf-parse.php');
	require_once ('wfip.php');
	require_once ('wfip-bbpress.php');
	require_once ('wfxp.php');
	require_once ('wfxp-bbpress.php');
}

/**
 * Executes all necessary functions to make the importation happen.
 */
function import_main ()
{
	global $bbdb;
	import_init ();
	$bbip = new WFIP_bbPress;
	$bbip->db &= $bbdb;

	if ('true' == $_POST['users'])
	{
		$bbip->import_users = true;
	}
	if ('true' == $_POST['content'])
	{
		$bbip->import_content = true;
	}
	if ('true' == $_POST['preserve'])
	{
		$bbip->preserve_ids = true;
		if ('true' == $_POST['current'])
		{
			$bbip->preserve_current_user = true;
		}
		if ('true' == $_POST['admins'])
		{
			$bbip->preserve_admins = true;
		}
	}

	$bbip->read_file ($_FILES['import_file']['tmp_name']);
	while ($bbip->file_contents)
	{
		$current = $bbip->find_element ($bbip->file_contents);
		if ('!--' != $current[0] && '?xml' != $current[0] && 'forums_data' != $current[0])
		{
			die ('Invalid top-level element (' . $current[0] . ').');
		}
		$bbip->call_element ($current);
		$bbip->file_contents = $bbip->remove_element ($current[1], $bbip->file_contents);
	}

	// Magic!
}

/**
 * Displays the admin import page.
 *
 * Gives a simple explanation of how the import process works and gives
 * users a form to choose their options and provide their file.
 */
function import_page ()
{
?>

	<h2><?php _e ('Import') ?></h2>
	<?php if (isset ($_POST['importing'])) : import_main (); else : ?>
	<p><?php _e ('bbPress can import forums data from a standard XML file you provide via the form below.  Such a file can be generated by another bbPress installation or another forum software with a compatible export feature.'); ?></p>
	<p><?php _e ('This file should contain data about users, forums, topics, and posts.  You can select which types of data to import below.'); ?></p>
	<form action="" method="post">
		<fieldset>
			<legend>Import File &raquo;</legend>
			<input type="file" name="import_file" />
		</fieldset>
		<fieldset>
			<legend>Options &raquo;</legend>
			<input type="checkbox" name="users" value="true" checked="true" /> Import user data.<br />
			<input type="checkbox" name="content" value="true" checked="true" /> Import forum, topic, and post data.<br />
			<input type="checkbox" name="preserve" value="true" checked="false" /> Preserve IDs.<br />
			<input type="checkbox" name="current" value="true" checked="true" /> Do not overwrite current user.<br />
			<input type="checkbox" name="admins" value="true" checked="true" /> Do not overwrite keymasters.
		</fieldset>
		<!-- Form options. -->
		<p class="submit">
			<input type="submit" name="submit" value="<?php _e ('Import Forums Data'); ?>" />
			<input type="hidden" name="importing" value="true" />
		</p>
	</form>

<?php
endif;
}

/**
 * Adds import link to admin menu.
 */
function import_add_admin ()
{
	global $bb_submenu;
	$bb_submenu['content.php'][] = array (__('Import'), 'use_keys', 'import_page', 'importer-bbpress.php');
}

add_action ('bb_admin_menu_generator', 'import_add_admin');

?>
