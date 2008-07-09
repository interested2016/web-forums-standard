<?php
// Web Forum Export/Import Standard Validator

/**
 * Displays the import form.
 */	
function display_form ()
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 STRICT//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Web Forum Export/Import Standard Validator</title>
	</head>
	<body>
		<form enctype="multipart/form-data" action="" method="POST">
			<p>
				<input type="file" name="import_file" /><br />
				<input type="submit" name="validate" value="Validate File" />
			</p>
			<p>
				<em>Note: due to the debugging nature of this version of this software, the data contained in this file will be displayed upon successful validation for human verification purposes.</em>
			</p>
		</form>
	</body>
</html>
<?php
}

/**
 * Displays the result of importation.
 */
function display_result ($result, $data)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 STRICT//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Web Forum Export/Import Standard Validator</title>
	</head>
	<body>
		<p>
			<?php print ($result); ?>
		</p>
		<pre>
<?php print_r ($data); ?>
		</pre>
	</body>
</html>
<?php
}

/**
 * Parses uploaded file and checks for errors.
 */
function validate ()
{
	require_once ('wf-parse.php');
	$parse = new WF_Parse;
	$parse->read_file ($_FILES['import_file']['tmp_name']);
	while ($parse->file_contents)
	{
		$current = $parse->find_element ($parse->file_contents);
		if ('!--' != $current[0] && '?xml' != $current[0] && 'forums_data' != $current[0])
		{
			die ('Invalid top-level element (' . $current[0] . ').');
		}
		$parse->call_element ($current);
		$parse->file_contents = $parse->remove_element ($current[1], $parse->file_contents);
	}
	$result = 'This is a valid export file.  You should be able to import this properly using a compatible software plugin.';
	display_result ($result, $parse->forum_data);
}

if (isset ($_POST['validate']))
{
	validate ();
}
else
{
	display_form ();
}

?>
