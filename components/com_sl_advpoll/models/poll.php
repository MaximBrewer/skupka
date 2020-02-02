<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.modelitem');

/**
 * Poll Model.
 *
 * @package		Joomla.Site
 * @subpackage	Skyline.AdvPoll
 */
class SL_AdvPollModelPoll extends JModelItem {
	/** @var string	Model context string. */
	protected $_context	= 'com_sl_advpoll.poll';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState() {
		// initialize variables
		$app	= JFactory::getApplication('site');

		// Load state from the request.
		$pk		= $app->input->get('id');
		$this->setState('poll.id', $pk);

		// load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$user	= JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_sl_advpoll')) && (!$user->authorise('core.edit', 'com_sl_advpoll'))) {
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}

	/**
	 * Method to get poll data.
	 *
	 * @param	int		The id of the article.
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null) {
		// Initialize variables.
		$pk		= !empty($pk) ? $pk : (int) $this->getState('poll.id');
		$show_random = $this->getState('show_random');
		$cate_poll = $this->getState('cate_poll');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {
			try {
				$db		= $this->getDbo();
				$query	= $db->getQuery(true);

				$query->select($this->getState(
					'item.select', 'a.*'
				));

				$query->from('#__sl_advpoll_polls AS a');
				$query->select('SUM(c.votes) AS total_votes');
				$query->join('LEFT', '#__sl_advpoll_answers AS c ON a.id = c.pollid AND c.state = 1');
				$query->group('a.id');

				if ($show_random == 0) {
					$query->where('a.id = ' . (int) $pk);
				} else {
					$query->where('a.catid = ' . $cate_poll);
					$query->order('RAND( ) LIMIT 1');
				}

				// filter by published state.
				$published	= $this->getState('filter.published');
				$archived	= $this->getState('filter.archived');

				if (is_numeric($published)) {
					$query->where('(a.state = ' . (int) $published . ' OR a.state = ' . (int) $archived . ')');
				}

				$db->setQuery($query);
				$data	= $db->loadObject();

				if ($error = $db->getErrorMsg()) {
					throw new Exception($error);
				}

				if (empty($data)) {
					//return JError::raiseError(404, JText::_('COM_SL_ADVPOLL_ERROR_POLL_NOT_FOUND'));
					return false;
				}

				// check for published state if filter set.
				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived))) {
					//return JError::raiseError(404, JText::_('COM_SL_ADVPOLL_ERROR_POLL_NOT_FOUND'));
					return false;
				}

                if ($data->schedule == 1) {
                    if (time() >= strtotime($data->publish_up) && time() <= strtotime($data->publish_down)) {
                        $data->expired = 0;
                    } else {
                        $data->expired = 1;
                    }
                }

				// convert parameter fields to objects.
				$params		= JComponentHelper::getParams('com_sl_advpoll'); 
				$registry	= new JRegistry();
				$registry->loadString($data->params);		

				if ($registry->get('custom_style') == 0) {
					$registry->set('header_footer_bg', '');
					$registry->set('header_footer_text', '');
					$registry->set('body_bg', '');
					$registry->set('body_text', '');
				}

				$params->merge($registry);		


				//$data->params	= clone($this->getState('params'));
				//$data->params->merge($registry);

				$data->params	= $params;

				// get answer of this poll
				$query	= $db->getQuery(true);
				$query->select('*')
					->from('#__sl_advpoll_answers')
					->where('state = 1 AND pollid = ' . $data->id . ' AND type_answer = \'default\' ')
					->order('ordering')
				;
				$db->setQuery($query);
				$data->answers	= $db->loadObjectList();

				$this->_item[$pk] = $data;
			} catch (JException $e) {
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow redirect to work.
					// JError::raiseError(404, $e->getMessage());
					return false;
				} else {
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Method to vote a poll.
	 */
	public function vote($poll_id, $answers) {
		$app		= JFactory::getApplication('site');
		$user		= JFactory::getUser();
		$user_ip	= $app->input->server->get('REMOTE_ADDR');
		$db			= $this->getDbo();

		foreach ($answers as $answer) {
			$query	= 'UPDATE #__sl_advpoll_answers'
				. ' SET votes = votes + 1'
				. ' WHERE pollid = ' . (int) $poll_id
				. ' AND id = ' . (int) $answer
			;
			$db->setQuery($query);
			$db->query();

			$query	= 'UPDATE #__sl_advpoll_polls'
				. ' SET voters = voters + 1'
				. ' WHERE id = ' . (int) $poll_id
			;
			$db->setQuery($query);
			$db->query();

			$query	= 'INSERT INTO #__sl_advpoll_logs'
				. ' SET date = ' . $db->quote(JFactory::getDate()->toSQL())
				. ', poll_id = ' . (int) $poll_id
				. ', answer_id = ' . (int) $answer
				. ', user_id = ' . (int) $user->get('id')
				. ', ip = ' . $db->quote($user_ip)
			;
			$db->setQuery($query);
			$db->query();
		}
	}

	public function voteOtherAnswer($args) {
		$app 		= JFactory::getApplication('stite');
		$user 		= JFactory::getUser();
		$user_ip 	= $app->input->server->get('REMOTE_ADDR');
		$db 		= $this->getDbo();

		$poll_id 		= $args['poll_id'];
		$title 			= $args['title'];
		$type_answer 	= $args['type_answer'];
		$state 			= $args['state'];
		$ordering 		= $args['ordering'];
		$votes 			= $args['votes'];

		$query = "INSERT INTO #__sl_advpoll_answers (pollid, title, type_answer, state, ordering, votes) VALUES".
			"(" . $poll_id . ", " . $db->quote($title) . ", " . $db->quote($type_answer) . ", " . $state . ", " . $ordering . ", " . $votes .")";
		$db->setQuery($query);
		$db->query();

		$answer_id = $db->insertid();

		$query = 'UPDATE #__sl_advpoll_polls SET voters = voters + 1 WHERE id = ' . $poll_id;
		$db->setQuery($query);
		$db->query();

		$query	= 'INSERT INTO #__sl_advpoll_logs'
				. ' SET date = ' . $db->quote(JFactory::getDate()->toSQL())
				. ', poll_id = ' . (int) $poll_id
				. ', answer_id = ' . (int) $answer_id
				. ', user_id = ' . (int) $user->get('id')
				. ', ip = ' . $db->quote($user_ip)
			;
		$db->setQuery($query);
		$db->query();
	}

	public function getAllAnswers($poll_id) {
		$db = $this->getDbo();
		$query = "SELECT * FROM #__sl_advpoll_answers WHERE state = 1 AND pollid = " .$poll_id ." ORDER BY ordering";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	public function getTotalAnswers($poll_id) {
		$db = $this->getDbo();
		$query = "SELECT COUNT(*) FROM #__sl_advpoll_answers WHERE pollid = $poll_id";
		$db->setQuery($query);
		$total = $db->loadResult();

		return $total;
	}

	public function getTotalOtherAnswer($poll_id) {
		$db = $this->getDbo();
		$query = "SELECT COUNT(*) FROM #__sl_advpoll_answers WHERE pollid = $poll_id AND type_answer = 'other'";
		$db->setQuery($query);
		$total = $db->loadResult();

		return $total;
	}

	public function customStyle($item, $id) {
		$css = '';
		$header_footer_color 	= $item->params->get('header_footer_bg', '#FFFFFF');
		$header_footer_text 	= $item->params->get('header_footer_text', '#111111');
		$body_color 			= $item->params->get('body_bg', '#EEEEDD');
		$body_text 				= $item->params->get('body_text', '#4D4D4D');
		$custom_css 			= $item->params->get('custom_css', '');
		$result_display_type	= $item->params->get('result_display_type', 1);

		if (!empty($header_footer_color)) {
			$css .= "
#$id .wrap_sl_advpoll_title,
#$id .sl_advpoll_buttons {
	background: $header_footer_color;
}";
		}

		if (!empty($header_footer_text)) {
			$css .= "
#$id .sl_advpoll_title,
#$id .sl_advpoll_buttons {
	color: $header_footer_text;
}";
		}

		if (!empty($body_color)) {
			$css .= "
#$id .sl_advpoll_body {
	background-color: $body_color;
}";
		}

		if (!empty($body_text)) {
			$css .= "
#$id .sl_advpoll_body {
	color: $body_text;
}";
		}

		if (!empty($custom_css)) {
			$css .= "\n";
			$css .= $custom_css;
		}

		if ($result_display_type == 0) {
			$css .= "\n";
			$css .= "
.sl_advpoll_result, .sl_advpoll_msg {
	padding: 0;
}
.sl_advpoll_result .sl_advpoll_graph .sl_advpoll_answer_title {
	width: 100%;
}
.sl_advpoll_result .sl_advpoll_graph div{
	line-height: 12px;
	padding: 0;
}
.sl_advpoll_result .sl_advpoll_question, .sl_advpoll_msg .sl_advpoll_message {
	margin: 0 0 15px 0;
}
.sl_advpoll_result .sl_advpoll_graph .sl_advpoll_answer_graph {
	width: 100%;
	padding: 0;
}
.sl_advpoll_result .sl_advpoll_answer_graph div.sl_advpoll_line_container {
	width: 100%;
}
.sl_advpoll_result .sl_advpoll_answer_graph div.sl_advpoll_full_line {
	width: 100%;
}
.sl_advpoll {
background: inherit;
border: none;
border-radius: 0;
}
.sl_advpoll_result .sl_advppoll_total, .sl_advpoll_msg .sl_advpoll_buttons {
margin: 0;
}
";
		}

		return $css;
	}

    public function checkPermission($pollId = '', $cookieName = '', $schedule = 0, $publish_up = '', $publish_down = '') {
        $user = JFactory::getUser();
        if ($user->authorise('frontend.vote', 'com_sl_advpoll') == 0) {
            return 0;
        }

        $app = JFactory::getApplication();
        $config = JComponentHelper::getParams('com_sl_advpoll');
        $checkType = $config->get('check_type');

        if ($checkType == 0) {
            if ($cookieName) {
                $pollCookie = $app->input->cookie->get($cookieName);
                if ($pollCookie) {
                    return 1;
                }
            }
        } else {
            if ($pollId) {
                $ip = $app->input->server->get('REMOTE_ADDR');
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('poll_id')
                    ->from('#__sl_advpoll_logs')
                    ->where('poll_id = ' . $pollId . ' AND ip = ' . $db->quote($ip));

                if($schedule) {
                    if ($publish_up) {
                        $query->where('date >= ' . $db->quote($publish_up));
                    }

                    if ($publish_down) {
                        $query->where('date <= ' . $db->quote($publish_down));
                    }
                }

                $db->setQuery($query);
                $pollVoted = $db->loadResult();

                if ($pollVoted) {
                    return 1;
                }
            }
        }

        return -1;

    }

}