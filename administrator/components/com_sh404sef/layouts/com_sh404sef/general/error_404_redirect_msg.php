<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 */

defined('_JEXEC') or die;

/**
 * This layout displays an information message when an automatic 301 redirect happens instead of a 404 error
 *
 * Input:
 * $displayData['title'] string The popup title
 * $displayData['content'] string The page content, with similar URLs if any
 */
?>

<!-- sh404SEF: display 404 auto-redirect notification -->
<style type="text/css">
	.wb-sh404sef-404-redirect {
		z-index: 1000;
		position: fixed;
		font-size: 1em;
		padding: 1em;
		color: <?php echo $displayData['color']; ?>;
		background: <?php echo $displayData['background_color']; ?>;
		opacity: <?php echo $displayData['opacity']; ?>;
		text-align: center;
		box-sizing: border-box;
		left: 0;
		bottom: 0;
		right: 0;
		max-height: 40%;
		overflow-y: auto;
		transform: translate(0, 100%);
	}

	.wb-sh404sef-404-redirect-show {
		transition: transform 1s ease;
		transform: translate(0, -0%);
	}

	.wb-sh404sef-404-redirect .wb-sh404sef-404-redirect-content {
		display: inline-block;
		text-align: justify;
	}

	.wb-sh404sef-404-redirect-content h1 {
		display: none;
	}

	.wb-sh404sef-404-redirect-content a {
		text-decoration: underline;
		color: <?php echo $displayData['color']; ?>;
	}

	.wb-sh404sef-404-redirect-content a:hover {
		color: #DDD;
	}

	.wb-sh404sef-404-redirect-content ul {
		margin-top: 0.5em;
		margin-bottom: 0.5em;
	}

	.wb-sh404sef-404-redirect h1 {
		font-size: 1.5em;
		line-height: 1em;
		color: <?php echo $displayData['color']; ?>;
		margin: 0 0 0.4em 0;
		padding: 0;
		font-weight: 300;
		text-align: left;
	}

	.wb-sh404sef-404-redirect .wb-sh404sef-404-redirect-close {
		cursor: pointer;
		position: absolute;
		right: 0;
		top: 0;
		color: <?php echo $displayData['color']; ?>;
		padding: 0.5em 0.75em;
		font-size: 1em;
		text-decoration: none;
	}

	@media only screen and (min-width: 992px) {
		.wb-sh404sef-404-redirect-content {
			max-width: 992px;
		}
	}

	@media only screen and (max-width: 991px) {
		.wb-sh404sef-404-redirect-content {
			max-width: 90%;
			padding-right: 1em;
		}
	}

	@media only screen and (max-width: 768px) {
		.wb-sh404sef-404-redirect-content {
			max-width: 100%;
		}
	}

</style>

<section id='wb-sh404sef-404-redirect' class='wb-sh404sef-404-redirect'>
	<a class='wb-sh404sef-404-redirect-close' id='wb-sh404sef-404-redirect-close'>X</a>

	<div class='wb-sh404sef-404-redirect-content'>
		<h1><?php echo $displayData['title']; ?></h1>
		<?php echo $displayData['content']; ?>
	</div>
</section>

<script type="text/javascript">
	(function (d) {
		d.getElementById("wb-sh404sef-404-redirect-close").addEventListener("click", function () {
			d.getElementById("wb-sh404sef-404-redirect").style.display = "none";
		});
		setTimeout(function () {
			var div = d.getElementById("wb-sh404sef-404-redirect")
			div.className += ' ' + 'wb-sh404sef-404-redirect-show';
		}, 2000);
	}(document));
</script>
<!-- sh404SEF: display 404 auto-redirect notification -->
