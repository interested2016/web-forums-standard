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

	function is_current_user ($user)
	{
		$id = bb_get_current_user_info ('id');
		if ($id == $user['id'])
		{
			return TRUE;
		}
		return FALSE;
	}

	function is_admin ($user)
	{
		if (FALSE !== strpos ($user['meta']['capabilities'], 'keymaster'))
		{
			return TRUE;
		}
		return FALSE;
	}

	function insert_users ()
	{
		foreach ($this->data['users'] as $user)
		{
			$data = array ();
			$data['ID'] = $user['id'];
			$data['user_login'] = $user['login'];
			// Check type!
			$data['user_pass'] = $user['pass']['pass'];
			$data['user_registered'] = $user['incept'];
			if (0 === $user['status'] || 1 === $user['status'])
			{
				$data['user_status'] = $user['status'];
			}
			$meta = $user['meta'];
			$data['user_nicename'] = $meta['user_nicename'];
			$data['user_email'] = $meta['user_email'];
			$data['user_url'] = $meta['user_url'];
			$data['display_name'] = $meta['display_name'];
			unset ($meta['user_nicename'], $meta['user_email'], $meta['user_url'], $meta['display_name']);
			$this->insert ($this->db->users, $data);
			if ($meta)
			{
				$this->insert_user_meta ($meta);
			}
		}
	}

	function insert_forums ()
	{

	}

	function insert_topics ()
	{

	}

	function insert_posts ()
	{

	}

	function insert_tags ($topic_id)
	{
		foreach ($this->data['topics'][$topic_id]['tags'] as $tag)
		{
			if (!tag_exists)
			{
				add_topic_tag ($topic_id, $tag);
			}
		}
	}

	function insert_user_meta ($user_id, $user_meta)
	{
		foreach ($user_meta as $meta)
		{
			$data = array ('user_id' => user_meta, 'meta_key' => $meta['key'], 'meta_value' => $meta['value']);
			$this->insert ($this->db->user_meta, $data);
		}
	}

	function insert_topic_meta ($topic_id, $topic_meta)
	{
		foreach ($topic_meta as $meta)
		{
			$data = array ('object_type' => 'bb_topic', 'object_id' => $topic_id, 'meta_key' => $meta['key'], 'meta_value' => $meta['value']);
			$this->insert ($this->db->meta, $data);
		}
	}

	
}

?>
