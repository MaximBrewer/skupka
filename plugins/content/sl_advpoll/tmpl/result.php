<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_sl_advpoll/models');
$model  = JModelLegacy::getInstance('poll', 'SL_AdvPollModel', array('ignore_request' => true));
$total_other_answer = $model->getTotalOtherAnswer($item->id);

if ($item->params->get('graph_type', 0) == 1) {
	// get votes max
	$max_votes	= 0;

	foreach ($item->answers as $answer) {
		if ($answer->votes > $max_votes) {
			$max_votes	= $answer->votes;
		}
	}
}

if($item->params->get('display_other_answer', 0) == 1) {
	$item->answers = $model->getAllAnswers($item->id);
}

$config	= JComponentHelper::getParams('com_sl_advpoll');
$message = JHtml::_('date', 'now', $config->get('date_format', 'l, F d, Y g:i:s A'));
?>

<div class="sl_advpoll_result <?php echo ($item->params->get('result_display_type', 1) == 0 ? 'sl_hide_result' : ''); ?>">
	<?php if ($item->params->get('show_result', 1)) : ?>
	<div class="sl_advpoll_date">
		<?php echo $message; ?>
	</div>
	<div class="sl_advpoll_question">
		<?php echo $item->title; ?>
	</div>

<?php if ($item->params->get('graph_type') == 2 || $item->params->get('graph_type') == 3) : // Google Chart ?>
	<?php
	$data	= array("['Answer', 'Votes']");

	foreach ($item->answers as $answer) {
		$data[]	= "['" . addslashes($answer->title) . "',	$answer->votes]";
	}

	if($item->params->get('other_answer', 0) == 1 && $item->params->get('display_other_answer', 0) == 0 && $total_other_answer > 0) {
		$data[]	= "['" . addslashes('Other') . "',	$total_other_answer]";
	}

	if ($item->params->get('graph_type') == 2) {
		$chartType	= 'PieChart';
	} else if ($item->params->get('graph_type') == 3) {
		$chartType	= 'BarChart';
	}
	?>

	<div id="advpolls-gchart" style="<?php echo $item->params->get('result_display_type') == 0 ? 'width: 100%;' : '';?>"></div>

	<script>
		var data	= google.visualization.arrayToDataTable([
			<?php echo implode(', ', $data); ?>
		]);

		var option	= {
			is3D: true,
			chartArea: {height: 300}
			<?php if (!$item->params->get('show_votes', 1)) : ?>
				<?php echo ', tooltip: {trigger: \'none\'}'; ?>
			<?php endif; ?>
		};

		var chart = new google.visualization.<?php echo $chartType; ?>(document.getElementById('advpolls-gchart'));
		chart.draw(data, option);
	</script>

<?php else : ?>

	<ul class="sl_advpoll_graph">
		<?php $total_percent_default = 0;
			  $total_calculated_percent_default = 0;
		?>

		<?php foreach ($item->answers as $answer) : ?>
			<?php $percent	= $item->total_votes ? round(100 * $answer->votes / $item->total_votes, 2) : 0; ?>
			<?php $calculated_percent	= $item->params->get('graph_type', 0) == 0 ? $percent : ($max_votes ? round(100 * $answer->votes / $max_votes, 2) : 0); ?>

			<?php $total_percent_default += $percent;?>

			<li>
				<div class="sl_advpoll_answer_title">
					<?php echo $answer->title; ?>
				</div>
				<div class="sl_advpoll_answer_graph" title="<?php echo $percent; ?>%" data-percent="<?php echo $calculated_percent; ?>%">
					<div class="sl_advpoll_line_container">
						<div class="sl_advpoll_full_line">
							<div class="sl_advpoll_line" style="width: <?php echo $calculated_percent; ?>%"></div>
						</div>
						<div class="sl_advpoll_percent" style="width: <?php echo $calculated_percent; ?>%">
							<?php echo $percent; ?>%
						</div>
					</div>
				</div>

				<?php if ($item->params->get('show_votes')) : ?>
				<div class="sl_advpoll_answer_votes">
					<?php
						if ($answer->votes  == 0) {
							$text	= 'PLG_SL_ADVPOLL_ZERO_VOTES';
						} else {
							if ($answer->votes > 0) {
								$text	= ($answer->votes ==1) ? 'PLG_SL_ADVPOLL_ONE_VOTES' : 'PLG_SL_ADVPOLL_N_VOTES';
							}
						}
					?>
					<?php echo JText::sprintf($text, $answer->votes); ?>
				</div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>

		<?php if($item->params->get('display_other_answer', 0) == 0) : ?>
			<?php $total_calculated_percent_default = $item->params->get('graph_type', 0) == 0 ? (100 - $total_percent_default) : round(100 * $total_other_answer / $max_votes, 2); ?>
			<li>
				<div class="sl_advpoll_answer_title">
					<?php echo JText::sprintf('PLG_SL_ADVPOLL_OTHER_ANSWERS') ?>
				</div>

				<div class="sl_advpoll_answer_graph" title="<?php echo 100 - $total_percent_default; ?>%" data-percent="<?php echo $total_calculated_percent_default; ?>%">
					<div class="sl_advpoll_line_container">
						<div class="sl_advpoll_full_line">
							<div class="sl_advpoll_line" style="width: <?php echo $total_calculated_percent_default; ?>%"></div>
						</div>
						<div class="sl_advpoll_percent" style="width: <?php echo $total_calculated_percent_default; ?>%">
							<?php echo 100 - $total_percent_default; ?>%
						</div>
					</div>
				</div>

				<?php if ($item->params->get('show_votes')) : ?>
				<div class="sl_advpoll_answer_votes">
					<?php
						if ($total_other_answer == 0) {
							$text	= 'PLG_SL_ADVPOLL_ZERO_VOTES';
						} else {
							if ($total_other_answer > 0) {
								$text	= ($total_other_answer ==1) ? 'PLG_SL_ADVPOLL_ONE_VOTES' : 'PLG_SL_ADVPOLL_N_VOTES';
							}
						}
					?>
					<?php echo JText::sprintf($text, $total_other_answer); ?>
				</div>
				<?php endif; ?>

			</li>
		<?php endif; ?>

	</ul>
<?php endif; ?>

<?php if ($item->params->get('show_votes')) : ?>
	<div class="sl_advppoll_total">
		<?php echo JText::_('PLG_SL_ADVPOLL_TOTAL'); ?>
		<span>
			<?php if ($item->total_votes <= 1) : ?>
				<?php echo JText::sprintf('PLG_SL_ADVPOLL_N_VOTE', $item->total_votes); ?>
			<?php else : ?>
				<?php echo JText::sprintf('PLG_SL_ADVPOLL_N_VOTES', $item->total_votes); ?>
			<?php endif; ?>
		</span>
	</div>
<?php endif; ?>

	<?php else : ?>
	<div class="sl_advpoll_msg">
		<div class="sl_advpoll_title">
			<?php echo $item->title; ?>
		</div>
		<div class="sl_advpoll_message">
			<?php echo $this->message; ?>
		</div>
		<div class="sl_advpoll_buttons">
			<button class="sl_advpoll_button" onclick="jQuery.fancybox.close();">
				<?php echo JText::_('PLG_SL_ADVPOLL_CLOSE'); ?>
			</button>
		</div>
	</div>
	<?php endif; ?>
</div>