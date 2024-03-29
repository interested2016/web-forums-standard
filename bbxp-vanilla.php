<?php

/**
 * Vanilla BBXP Extension
 *
 * This class includes functions necessary for Vanilla to interface
 * with the BBXP class, allowing for exportation of Vanilla data to
 * a BBXF file.
 */
class BBXP_Vanilla extends BBXP
{

	/**
	 * Sets up table names for database.
	 */
	function initialize_db ($prefix)
	{
		$this->db->tables['User'] = FALSE;
		$this->db->tables['Category'] = FALSE;
		$this->db->tables['Discussion'] = FALSE;
		$this->db->tables['Comment'] = FALSE;
		$this->db->set_prefix ($prefix);
	}

	/**
	 * Alias for BPDB's get_results that eliminates a parameter.
	 */
	function fetch ($query)
	{
		return $this->db->get_results ($query, 'ARRAY_A');
	}
	
	/**
	 * Fetches users from the database.
	 */
	function fetch_users ()
	{
		return $this->fetch ('SELECT * FROM ' . $this->db->User . ' WHERE 1');
	}

	/**
	 * Fetches forums from the database.
	 */
	function fetch_forums ()
	{
		return $this->fetch ('SELECT * FROM ' . $this->db->Category . ' WHERE 1');
	}

	/**
	 * Fetches topics from the database.
	 */
	function fetch_topics ()
	{
		return $this->fetch ('SELECT * FROM ' . $this->db->Discussion . ' WHERE 1');
	}

	/**
	 * Fetches posts from the database.
	 */
	function fetch_posts ($topic_id)
	{
		return $this->fetch ('SELECT * FROM ' . $this->db->Comment . ' WHERE DiscussionID="' . $topic_id . '"');
	}

	/**
	 * Prepares retrieved user data for output.
	 */
	function prep_user_data ($raw_user)
	{
		$user['id'] = $raw_user['UserID'];
		$user['login'] = $raw_user['Name'];
		$user['pass']['type'] = 'md5';
		$user['pass']['pass'] = $raw_user['Password'];
		$user['incept'] = $raw_user['DateFirstVisit'];
		$user['meta']['role_id'] = $raw_user['RoleID'];
		$user['meta']['first_name'] = $raw_user['FirstName'];
		$user['meta']['last_name'] = $raw_user['LastName'];
		$user['meta']['email'] = $raw_user['Email'];
		$user['meta']['picture'] = $raw_user['Picture'];
		$user['meta']['icon'] = $raw_user['Icon'];
		return $user;
	}

	/**
	 * Prepares retrieved forum data for output.
	 */
	function prep_forum_data ($raw_forum)
	{
		$forum['id'] = $raw_forum['CategoryID'];
		$forum['in'] = 0;
		$forum['title'] = $raw_forum['Name'];
		$forum['content'] = $raw_forum['Description'];
		$forum['meta']['order'] = $raw_forum['Priority'];
		return $forum;
	}

	/**
	 * Prepares retrieved topic data for output.
	 */
	function prep_topic_data ($raw_topic, $raw_posts)
	{
		$topic['id'] = $raw_topic['DiscussionID'];
		$topic['author'] = $raw_topic['AuthUserID'];
		$topic['in'] = $raw_topic['CategoryID'];
		$topic['title'] = $raw_topic['Name'];
		$topic['incept'] = $raw_topic['DateCreated'];
		$topic['meta']['open'] = !$raw_topic['Closed'];
		$topic['meta']['sticky'] = $raw_topic['Sticky'];
		$topic['meta']['active'] = $raw_topic['Active'];
		$topic['meta']['sink'] = $raw_topic['Sink'];
		foreach ($raw_posts as $raw_post)
		{
			$topic['posts'][] = $this->prep_post_data ($raw_post);
		}
		return $topic;
	}

	/**
	 * Prepares retrieved post data for output.
	 */
	function prep_post_data ($raw_post)
	{
		$post['id'] = $raw_post['CommentID'];
		$post['author'] = $raw_post['AuthUserID'];
		$post['title'] = '';
		$post['content'] = $raw_post['Body'];
		$post['incept'] = $raw_post['DateCreated'];
		$post['status'] = $raw_post['Deleted'];
		$post['meta']['format'] = $raw_post['FormatType'];
		return $post;
	}

	/**
	 * Fetches, prepares, and outputs user data using subroutines.
	 */
	function write_users ()
	{
		$users = $this->fetch_users ();
		foreach ($users as $user)
		{
			$user = $this->prep_user_data ($user, $user_meta);
			$this->add_user ($user);
		}
	}

	/**
	 * Fetches, prepares, and outputs forum data using subroutines.
	 */
	function write_forums ()
	{
		$forums = $this->fetch_forums ();
		foreach ($forums as $forum)
		{
			$forum = $this->prep_forum_data ($forum, $forum_meta);
			$this->add_forum ($forum);
		}
	}

	/**
	 * Fetches, prepares, and outputs topic data using subroutines.
	 */
	function write_topics ()
	{
		$topics = $this->fetch_topics ();
		foreach ($topics as $topic)
		{
			$topic_posts = $this->fetch_posts ($topic['DiscussionID']);
			$topic = $this->prep_topic_data ($topic, $topic_posts);
			$this->add_topic ($topic);
		}
	}

}

?>
