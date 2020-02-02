<?php
/**
 * JUNewsUltra Pro
 *
 * @version 	6.x
 * @package 	UNewsUltra Pro
 * @author 		Denys D. Nosov (denys@joomla-ua.org)
 * @copyright 	(C) 2007-2016 by Denys D. Nosov (http://joomla-ua.org)
 * @license 	GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
 **/

/******************* PARAMS (update 28.04.2016) ************
*
* $params->get('moduleclass_sfx') - module class suffix
*
* $item->link           - article link for [href="..."] attribute
* $item->title          - title
* $item->title_alt      - for attribute title or alt
*
* $item->cattitle       - category title
* $item->catlink		- category link for [href="..."] attribute
*
* $item->image          - display image thumb
* $item->imagelink      - image thumb link for [src="..."] attribute
* $item->imagesource    - raw image source (original image)
*
* $item->sourcetext		- display raw intro and fulltext
*
* $item->introtext      - display introtex
* $item->fulltext       - display fulltext
*
* $item->author         - display author or created by alias
*
* $item->sqldate		- raw date [display format: 0000-00-00 00:00:00]
* $item->date           - display date & time with date format
* $item->df_d           - display day from date
* $item->df_m           - display mounth from date
* $item->df_y           - display year from date
*
* $item->hits           - display hits
*
* $item->rating         - display rating with stars
*
* $item->comments		- display comments couner
* $item->commentslink   - comment link for [href="..."] attribute
* $item->commentstext   - display comments text
* $item->commentscount  - comments couner (alias)
*
* $item->readmore       - display 'Read more...' or other text
* $item->rmtext         - display 'Read more...' or other text
*
************************************************************/

defined('_JEXEC') or die('Restricted access');

?>
<ul class="junewsultra <?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach ($list as $item) :  ?>
	<li class="jn-list">
        <?php if($params->get('show_title')): ?>
    	<strong><a href="<?php echo $item->link; ?>" title="<?php echo $item->title_alt; ?>"><?php echo $item->title; ?></a></strong>
        <?php endif; ?>
        <div class="jn-list-info">
            <?php if($params->get('showRating')): ?>
            <div class="left">
                <?php echo $item->rating; ?>
                <div>
                <?php if($params->get('showRatingCount')): ?>
                <?php echo $item->rating_count; ?>
                <?php endif; ?>
                <?php if($params->get('showHits')): ?>
                <?php echo JText::_('JGLOBAL_HITS'); ?>: <?php echo $item->hits; ?>
                <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="right">
            <?php if($params->get('show_date')): ?>
            <span><?php echo $item->date; ?></span>
            <?php endif; ?>
            <?php if($params->get('showcat')): ?>
            <span><?php echo $item->cattitle; ?></span>
            <?php endif; ?>
            <?php if($params->get('juauthor')): ?>
            <span><?php echo $item->author; ?></span>
            <?php endif; ?>
            <?php if($params->get('use_comments')): ?>
			<span><a class="jn-comment-link" href="<?php echo $item->link; ?><?php echo $item->commentslink; ?>"><?php echo $item->commentstext; ?></a></span>
            <?php endif; ?>
            </div>
        </div>
	</li>
<?php endforeach; ?>
</ul>