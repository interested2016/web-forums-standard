<?php

/**
 * bbPress WFXP Extension
 *
 * This class includes functions necessary for bbPress to interface
 * with the WFIP class, allowing for importation of bbPress data to
 * the database.
 */
class WFIP_bbPress extends WFIP
{

	function WFIP_bbPress ()
	{
		$this->WFIP ();
		require_once ('wfxp.php');
		require_once ('wfxp-bbpress.php');
		$this->export_lib = new WFXP_bbPress;
	}

	/**
	 * Fetches and prepares existing user data using subroutines.
	 */
	function fetch_existing_users ()
	{
		$users = $this->export_lib->fetch_users ();
		foreach ($users as $user)
		{
			$user_meta = $this->export_lib->fetch_user_meta ($user['ID']);
			$user = $this->export_lib->prep_user_data ($user, $user_meta);
			$this->existing_data['users'][] = $user;
		}
		$this->prep_existing_user_data ();
	}

	/**
	 * Fetches and prepares existing forum data using subroutines.
	 */
	function fetch_existing_forums ()
	{
		$forums = $this->export_lib->fetch_forums ();
		foreach ($forums as $forum)
		{
			$forum = $this->export_lib->prep_forum_data ($forum, $forum_meta);
			$this->existing_data['forums'][] = $forum;
		}
		$this->prep_existing_forum_data ();
	}

	/**
	 * Fetches and prepares existing topic data using subroutines.
	 */
	function fetch_existing_topics ()
	{
		$topics = $this->export_lib->fetch_topics ();
		foreach ($topics as $topic)
		{
			$topic_meta = $this->export_lib->fetch_topic_meta ($topic['topic_id']);
			$topic_tags = $this->export_lib->fetch_topic_tags ($topic['topic_id']);
			$topic_posts = $this->export_lib->fetch_posts ($topic['topic_id']);
			$topic = $this->export_lib->prep_topic_data ($topic, $topic_meta, $topic_tags, $topic_posts);
			$this->existing_data['topics'][] = $topic;
		}
		$this->prep_existing_topic_data ();
	}
	
}

?>
