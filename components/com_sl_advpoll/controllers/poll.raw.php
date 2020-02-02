<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * Skyline Software - Advanced Poll Controller.
 *
 */
class SL_AdvPollControllerPoll extends JControllerLegacy {

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 * @param	array	$config	Configuration array for model. Optional.
	 *
	 * @return	object	The model.
	 */
	public function getModel($name = 'Poll', $prefix = 'Sl_AdvPollModel', $config = array('ignore_request' => false)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Method to vote a poll.
	 */
	public function vote() {
		// initialize variables
		$app		= JFactory::getApplication();
		$poll_id	= $app->input->get('id', 0, 'post');
		$answers	= $app->input->get('answers', array(), 'post', 'array');
		$model		= $this->getModel();
		$item		= $model->getItem();
		$key		= 'error_msg' . $poll_id;

		$maxChoices	= $item->params->get('maxChoices');
		$other_answer_value = $app->input->get('other_answer_value', '', 'post');
		$total_answers_submit = (empty($answers) ? 0 : count($answers)) + (empty($other_answer_value) ? 0 : count($other_answer_value));

		// check if poll is published
		if (!$item || $item->state != 1) {
			$app->setUserState($key, JText::_('COM_SL_ADVPOLL_VOTE_ERROR'));
			return false;
		}

		// check if user has already vote
		$cokkieName	= JApplication::getHash($app->getName() . 'sl_advpoll' . $poll_id);
        $votePermission = $model->checkPermission($poll_id, $cokkieName, $item->schedule, $item->publish_up, $item->publish_down);

		if ($votePermission == 1) {
			$app->setUserState($key, JText::_('COM_SL_ADVPOLL_VOTE_ERROR'));
			$this->display();
			return false;
		} elseif ($votePermission == 0) {
            $app->setUserState($key, JText::_('COM_SL_ADVPOLL_NOT_ALLOW_VOTE'));
            $this->display();
            return false;
        }

		if ($total_answers_submit > 0 && $total_answers_submit <= $maxChoices) {
			if ($other_answer_value) {
				$args_other = array(
					'poll_id' 		=> $poll_id,
					'title'			=> $other_answer_value,
					'type_answer' 	=> 'other',
					'state' 		=>  1,
					'ordering' 		=> $model->getTotalAnswers($poll_id) + 1,
					'votes' 		=> 1
				);
				setcookie($cokkieName, '1', time() + $item->params->get('lag', '86400'));
				$model->voteOtherAnswer($args_other);
			}

			if ($answers) {
				// check if answer is valid
				$panswers	= array();

				foreach ($item->answers as $answer) {
					if (in_array($answer->id, $answers)) {
						$panswers[]	= $answer->id;

						if ($maxChoices > 0 && count($panswers) >= $maxChoices) {
							break;
						}
					}
				}

				if (!count($panswers)) {
					$app->setUserState($key, JText::_('COM_SL_ADVPOLL_VOTE_ERROR'));
					$this->display();
					return false;
				}

				// vote
				setcookie($cokkieName, '1', time() + $item->params->get('lag', '86400'));
				$model->vote($poll_id, $panswers);
			}

		}

		// go to result view
		$app->setUserState($key, JText::_('COM_SL_ADVPOLL_VOTE_SUCCESS'));
		$this->display();
	}

}