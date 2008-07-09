<?php

/**
 * Web Forums Data Import Class
 *
 * This class contains a number of functions designed to take
 * formatted input data and insert it into a database.  It also
 * contains functions for conflict resolution between existing
 * data and the data that is to be imported.
 */
class WFIP extends WF_Parse
{

	var $db;
	var $export_lib;

	var $existing_data;

	var $id_mappings;

	var $next_forum_id;
	var $next_post_id;
	var $next_topic_id;
	var $next_user_id;

	var $import_content = TRUE;
	var $import_users = TRUE;
	var $preserve_admins = TRUE;
	var $preserve_current_user = TRUE;

	function WFIP
	{
		$this->WF_Parse ();
	}

	/**
	 * Pseudonym for BPDB's get_results.
	 * 
	 * This is a renaming of BPDB's get_results method to eliminate the need
	 * for the second parameter by always returning an associative array.
	 */
	function fetch ($query)
	{
		return $this->db->get_results ($query, 'ARRAY_A');
	}

	/**
	 * Pseudonym for BPDB's insert.
	 */
	function insert ($table, $data)
	{
		$this->db->insert ($table, $data);
	}

	function init_id_mappings ()
	{
		foreach ($this->data['users'] as $user)
		{
			$this->id_mappings['users'][$user['id']] = $user['id'];
		}
		foreach ($this->data['forums'] as $forum)
		{
			$this->id_mappings['forums'][$forum['id']] = $forum['id'];
		}
		foreach ($this->data['topics'] as $topic)
		{
			$this->id_mappings['topics'][$topic['id']] = $topic['id'];
			foreach ($topic['posts'] as $post)
			{
				$this->id_mappings['posts'][$post['id']] = $post['id'];
			}
		}

	}

	function init_next_ids ()
	{
		$users = array_merge ($this->existing_data['user_ids'], $this->id_mappings['users']);
		$users = array_values (arsort ($users));
		$this->next_user_id = $users[0];
		
		$forums = array_merge ($this->existing_data['forum_ids'], $this->id_mappings['forums']);
		$forums = array_values (arsort ($forums));
		$this->next_forum_id = $forums[0];
		
		$topics = array_merge ($this->existing_data['topic_ids'], $this->id_mappings['topics']);
		$topics = array_values (arsort ($topics));
		$this->next_topic_id = $topics[0];
		
		$posts = array_merge ($this->existing_data['post_ids'], $this->id_mappings['posts']);
		$posts = array_values (arsort ($posts));
		$this->next_post_id = $posts[0];
	}

	function prep_existing_user_data ()
	{
		foreach ($this->existing_data['users'] as $user)
		{
			$this->existing_data['user_ids'][] = $user['id'];
			$this->existing_data['user_logins'][] = $user['login'];
		}
	}

	function prep_existing_content_data ()
	{
		foreach ($this->existing_data['forums'] as $forum)
		{
			$this->existing_data['forum_ids'][] = $forum['id'];
			$this->existing_data['forum_titles'][$forum['in']][] = $forum['title'];
		}
		foreach ($this->existing_data['topics'] as $topic)
		{
			$this->existing_data['topic_ids'][] = $topic['id'];
			foreach ($topic['posts'] as $post)
			{
				$this->existing_data['post_ids'][] = $post['id'];
			}
			foreach ($topic['tags'] as $tag)
			{
				$this->existing_data['tags'][] = $tag;
			}
		}
	}

	function user_conflict ($user)
	{
		if (in_array ($user['id'], $this->existing_data['user_ids']))
		{
			return 'id';
		}
		if (in_array ($user['login'], $this->existing_data['user_logins']))
		{
			return 'login';
		}
		return FALSE;
	}

	function forum_conflict ($forum)
	{
		if (in_array ($forum['id'], $this->existing_data['forum_ids']))
		{
			return 'id';
		}
		if (in_array ($forum['title'], $this->existing_data['forum_titles'][$forum['in']]))
		{
			return 'title';
		}
		return FALSE;
	}

	function topic_conflict ($topic)
	{
		if (in_array ($topic['id'], $this->existing_data['topic_ids']))
		{
			return 'id';
		}
		return FALSE;
	}

	function post_conflict ($post)
	{
		if (in_array ($post['id'], $this->existing_data['post_ids']))
		{
			return 'id';
		}
		return FALSE;
	}

	function resolve_users ()
	{
		foreach ($this->data['users'] as $user)
		{
			if ('id' == $this->user_conflict ($user))
			{
				$this->id_mappings['users'][$user['id']] = $this->next_user_id++;
				$user['id'] = $this->id_mappings['users'][$user['id']];
			}
			elseif ('login' == $this->user_conflict ($user))
			{
				// WHAT AM I GOING TO DO?!?!?
			}
		}
	}

	function resolve_forums ()
	{
		foreach ($this->data['forums'] as $forum)
		{
			if ('id' == $this->forum_conflict ($forum))
			{
				$this->id_mappings['forums'][$forum['id']] = $this->next_forum_id++;
				$forum['id'] = $this->id_mappings['forums'][$forum['id']];
			}
			elseif ('title' == $this->forum_conflict ($forum))
			{
				// WHAT AM I GOING TO DO?!?!?
			}
		}
	}
	
	function resolve_topics ()
	{
		foreach ($this->data['topic'] as $topic)
		{
			if ('id' == $this->topic_conflict ($topic))
			{
				$this->id_mappings['topics'][$topic['id']] = $this->next_topic_id++;
				$topic['id'] = $this->id_mappings['topics'][$topic['id']];
			}
			foreach ($topic['posts'] as $post)
			{
				if ('id' == $this->post_conflict ($post))
				{
					$this->id_mappings['posts'][$post['id']] = $this->next_topic_id++;
					$post['id'] = $this->id_mappings['posts'][$post['id']];
				}
			}
		}
	}

	function tag_exists ($tag)
	{
		return in_array ($tag, $this->existing_data['tags']))
	}

}

?>
