<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');

class plgContentVRvote extends JPlugin
{

    function plgContentVRvote(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->_plugin = JPluginHelper::getPlugin('content', 'vrvote');

    }

    function VRvote($params)
    {
        global $mainframe;

        if (isset($params)) {
            $doc = &JFactory::getDocument();
            $doc->addStyleSheet(JURI::base() . 'plugins/content/vrvote/assets/vrvote.css');
            $doc->addScript(JURI::base() . 'plugins/content/vrvote/assets/vrvote.js');
        }
        $db =& JFactory::getDBO();
        $app = &JFactory::getApplication();
        $currip = $_SERVER['REMOTE_ADDR'];
        $query = 'select * from `#__content_vrvote` where content_id = ' . $params . ' ';
        $result = $db->setQuery($query);
        $result = $db->loadObject();
        $rand_conteer=rand();
        if (empty($result)) {
            $result->rating_sum = 0;
            $result->rating_count = 0;
        } else {
            //$query_insert = "INSERT INTO #__content_vrvote ( content_id, extra_id, lastip, rating_count, rating_sum )"
            //		. "\n VALUES ( " . $result . ", " . $result . ", " . $db->Quote( $currip ) . ", 1, " . $user_rating . " )";
            //$db->setQuery( $query_insert );
            //$db->query() or die( $db->getErrorMsg() );
            $percent = number_format((intval($result->rating_sum) / intval($result->rating_count)) * 20, 2);
            $rating = $result->rating_sum / $result->rating_count;
        }
        echo '<div class="vrvote-body">
				<ul class="vrvote-ul">
					<li id="rating_' . $params . '" class="current-rating" style="width:' . $percent . '%;"></li>
					<li>
						<a class="vr-one-star" onclick="javascript:JSVRvote(' . $params . ',1,' . $result->rating_sum . ',' . $result->rating_count . ',0,'.$rand_conteer.')" href="javascript:void(null)">1</a>
					</li>
					<li>
						<a class="vr-two-stars" onclick="javascript:JSVRvote(' . $params . ',2,' . $result->rating_sum . ',' . $result->rating_count . ',0,'.$rand_conteer.')" href="javascript:void(null)">2</a>
					</li>
					<li>
						<a class="vr-three-stars" onclick="javascript:JSVRvote(' . $params . ',3,' . $result->rating_sum . ',' . $result->rating_count . ',0,'.$rand_conteer.')" href="javascript:void(null)">3</a>
					</li>
					<li>
						<a class="vr-four-stars" onclick="javascript:JSVRvote(' . $params . ',4,' . $result->rating_sum . ',' . $result->rating_count . ',0,'.$rand_conteer.')" href="javascript:void(null)">4</a>
					</li>
					<li>
						<a class="vr-five-stars" onclick="javascript:JSVRvote(' . $params . ',5,' . $result->rating_sum . ',' . $result->rating_count . ',0,'.$rand_conteer.')" href="javascript:void(null)">5</a>
					</li>
				</ul>
			</div>
			<span  id="vrvote_' . $rand_conteer . '" class=" vrvote-count">
			     <small>';
        if ($result->rating_count != -1) {
            if ($result->rating_count != 0) {
                echo "(";
                if ($result->rating_count != 1) {
                    echo round($rating, 2) . ' - ' . $result->rating_count . ' голосов';
                } else {
                    echo round($rating, 2) . ' - ' . $result->rating_count . ' голос';
                }
                echo ")";
            }
        }
        echo "</small></span>";
        echo '	</span>';
        return true;
    }

}

?>