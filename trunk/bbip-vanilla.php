<?php

/**
 * Vanilla BBIP Extension
 *
 * This class includes functions necessary for Vanilla to interface
 * with the BBIP class, allowing for importation of Vanilla data to
 * the database.
 */
class BBIP_Vanilla extends BBIP
{

	function BBIP_Vanilla ()
	{
		$this->BBIP ();
		$this->export_lib = new BBXP_Vanilla;
	}

	/**
	 * Fetches and prepares existing user data using subroutines.
	 */
	function fetch_existing_users ()
	{
		$users = $this->export_lib->fetch_users ();
		foreach ($users as $user)
		{
			$user = $this->export_lib->prep_user_data ($user);
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
			$forum = $this->export_lib->prep_forum_data ($forum);
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
			$topic_posts = $this->export_lib->fetch_posts ($topic['DiscussionID']);
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
			$data['UserID'] = $user['id'];
			$data['Name'] = $user['login'];
			if ('md5' == $user['pass']['type'])
			{
				$data['Password'] = $user['pass']['pass'];
			}
			$data['DateFirstVisit'] = $user['incept'];
			$meta = $user['meta'];
			$data['user_email'] = $meta['email'];
			$data['user_url'] = $meta['url'];
			$data['FirstName'] = $meta['first_name'];
			$data['LastName'] = $meta['last_name'];
			$data['Picture'] = $meta['picture'];
			$data['Icon'] = $meta['icon'];
			$data['RoleID'] = $meta['role_id'];
			$this->insert ($this->db->User, $data);
		}
	}

	function insert_forums ()
	{
		foreach ($this->forum_data['forums'] as $forum)
		{
			$data['CategoryID'] = $forum['id'];
			$data['Name'] = $forum['title'];
			$data['Description'] = $forum['content'];
			$this->insert ($this->db->Category, $data);
		}
	}

	function insert_topics ()
	{
		foreach ($this->forum_data['topics'] as $topic)
		{
			$data['DiscussionID'] = $topic['id'];
			$data['CategoryID'] = $topic['in'];
			$data['Name'] = $topic['title'];
			$data['AuthUserID'] = $topic['author'];
			$data['DateCreated'] = $topic['incept'];
			$meta = $topic['meta'];
			$data['Active'] = $meta['active'];
			$data['Closed'] = !$meta['open'];
			$data['Sticky'] = $meta['sticky'];
			$data['Sink'] = $meta['sink'];
			$this->insert ($this->db->Discussion, $data);
			$this->insert_posts ($topic['id'], $topic['in'], $topic['posts']);
		}
	}

	function insert_posts ($topic_id, $forum_id, $posts)
	{
		foreach ($posts as $post)
		{
			$data['CommentID'] = $post['id'];
			$data['DiscussionID'] = $topic_id;
			$data['AuthUserID'] = $post['author'];
			$data['Body'] = $post['content'];
			$data['DateCreated'] = $post['incept'];
			if (0 === $post['status'] || 1 === $post['status'])
			{
				$data['Deleted'] = $post['status'];
			}
			$meta = $post['meta'];
			$data['FormatType'] = $meta['format_type'];
			$this->insert ($this->db->Comment, $data);
		}
	}
	
}

?>


