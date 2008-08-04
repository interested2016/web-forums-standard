<?php

/**
 * phpBB BBIP Extension
 *
 * This class includes functions necessary for phpBB to interface
 * with the BBIP class, allowing for importation of phpBB data to
 * the database.
 */
class BBIP_phpBB extends BBIP
{

	function BBIP_phpBB ()
	{
		$this->BBIP ();
		$this->export_lib = new BBXP_phpBB;
	}

	/**
	 * Fetches and prepares existing user data using subroutines.
	 */
	function fetch_existing_users ()
	{
		$users = $this->export_lib->fetch_users ();
		$group_names = $this->export_lib->fetch_group_names ();
		foreach ($users as $user)
		{
			$groups = $this->export_lib->fetch_user_groups ($user['user_id'], $group_names);
			$user = $this->export_lib->prep_user_data ($user, $groups);
			$this->existing_data['users'][] = $user;
		}
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
	}

	/**
	 * Fetches and prepares existing topic data using subroutines.
	 */
	function fetch_existing_topics ()
	{
		$topics = $this->export_lib->fetch_topics ();
		foreach ($topics as $topic)
		{
			$topic_posts = $this->export_lib->fetch_posts ($topic['topic_id']);
			$topic = $this->export_lib->prep_topic_data ($topic, $topic_posts);
			$this->existing_data['topics'][] = $topic;
		}
	}

	function is_current_user ($user)
	{
		return FALSE;
	}

	function is_admin ($user)
	{
		return FALSE;
	}

	function insert_users ()
	{
		foreach ($this->forum_data['users'] as $user)
		{
			$data['user_id'] = $user['id'];
			$data['username'] = $user['login'];
			// Check type!
			$data['user_password'] = $user['pass']['pass'];
			$data['user_regdate'] = $user['incept'];
			$meta = $user['meta'];
			$data['user_type'] = $user['type']);
			$data['user_permissions'] = $user['permissions_phpBB']);
			$data['user_ip'] = $user['ip_address'];
			$data['username_clean'] = $user['nice_name'];
			$data['user_email'] = $user['email'];
			$data['user_email_hash'] = $user['email_hash'];
			$data['user_birthday'] = $user['birthday'];
			$data['user_options'] = $user['options'];
			$data['user_avatar'] = $user['avatar'];
			$data['user_sig'] = $user['signature'];
			$data['user_from'] = $user['location'];
			$data['user_icq'] = $user['icq'];
			$data['user_aim'] = $user['aim'];
			$data['user_yim'] = $user['yim'];
			$data['user_msnm') = $user['msn'];
			$data['user_jabber'] = $user['jabber'];
			$data['user_website'] = $user['url'];
			$data['user_form_salt'] = $user['form_salt'];
			$this->insert_groups ($user['id'], $user['meta']['groups']);
			$this->insert ($this->db->users, $data);
		}
	}

	function insert_forums ()
	{
		foreach ($this->forum_data['forums'] as $forum)
		{
			$data['forum_id'] = $forum['id'];
			$data['forum_parent'] = $forum['in'];
			$data['forum_name'] = $forum['title'];
			$data['forum_desc'] = $forum['content'];
			$meta = $forum['meta'];
			$data['forum_slug'] = $meta['forum_slug'];
			$data['forum_order'] = $meta['forum_order'];
			$this->insert ($this->db->forums, $data);
		}
	}

	function insert_topics ()
	{
		foreach ($this->forum_data['topics'] as $topic)
		{
			$data['topic_id'] = $topic['id'];
			$data['forum_id'] = $topic['in'];
			$data['topic_title'] = $topic['title'];
			$data['topic_poster'] = $topic['author'];
			$data['topic_time'] = $topic['incept'];
			$data['topic_status'] = $topic['status'];
			$meta = $topic['meta'];
			$data['topic_type'] = $meta['type'];
			$this->insert ($this->db->topics, $data);
			$this->insert_posts ($topic['id'], $topic['in'], $topic['posts']);
		}
	}

	function insert_posts ($topic_id, $forum_id, $posts)
	{
		foreach ($posts as $post)
		{
			$data['post_id'] = $post['id'];
			$data['forum_id'] = $forum_id;
			$data['topic_id'] = $topic_id;
			$data['poster_id'] = $post['author'];
			$data['post_text'] = $post['content'];
			$data['post_time'] = $post['incept'];
			$meta = $post['meta'];
			$data['poster_ip'] = $meta['ip_address'];
			$this->insert ($this->db->posts, $data);
		}
	}

	function insert_groups ($user_id, $groups)
	{
		$group_names = explode ('||', $groups);
		foreach ($group_names as $group)
		{
			if (!$this->group_exists ($group))
			{
				$this->insert ($this->db->groups, array ('group_name' => $group));
			}
			$group_id = $this->fetch ('SELECT group_id FROM ' . $this->db->groups . ' WHERE group_name="' . $group . '" LIMIT 1');
			$data = array ('group_id' => $group_id, 'user_id' => $user_id);
			$this->insert ($this->db->user_group, $data);
		}
	}

	function group_exists ($group_name)
	{
		return $this->fetch ('SELECT group_name FROM ' . $this->db->groups . ' WHERE group_name="' . $group_name . '" LIMIT 1');
	}
	
}

?>

