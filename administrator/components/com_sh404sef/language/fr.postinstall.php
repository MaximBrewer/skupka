<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

if (file_exists(JPATH_ROOT . '/plugins/system/sh404sef/sh404sef.php')) :

	?>
	<div style="text-align: justify;">
		<h1>sh404SEF a été installé avec succès! merci de lire ce qui suit :</h1>

		<p>Cette extension
		<ul>
			<li>re-écrit les URL de Joomla! pour améliorer l'ergonomie et le référencement</li>
			<li>apporte de nombreuses améliorations à Joomla! au niveau référencement</li>
			<li>ajoute des fonctions de sécurité</li>
			<li>insère un code Google Analytics dans les pages, et affiche des rapports Analytics dans son panneau de
				contrôle
			</li>
		</ul>
		</p>

		<p>
			Si c'est la première fois que vous installez sh404SEF, il a bien été installé, mais la plupart de ses
			fonctions
			sont <strong>désactivées</strong> pour l'instant.
			Vous devez aller sur le panneau de contrôle (depuis le menu <a href="index.php?option=com_sh404sef">Composants
				/
				sh404SEF</a> de Joomla!),
			<strong>activer ce que vous souhaitez utiliser et valider</strong> avant que sh404SEF ne soit activé.
			Avant que vous ne fassiez cela, merci de lire les quelques paragraphes qui suivent, dans lesquels se
			trouvent
			des informations importantes.
			Si vous effectuez une mise à jour depuis une version précédente de sh404SEF,
			alors tous vos réglages ont été préservés, le composant est actif et vous pouvez recommencer à naviguer sur
			votre site normalement.
		</p>

		<h2>URL Rewriting</h2>

		<p>Si vous utilisez l'URL rewriting sur votre site (voir configuration globale de Joomla!), vous devez mettre en
			place un ficher .htaccess (pour serveur web Apache) ou équivalent pour le serveur que vous utilisez. Si
			votre serveur n'est pas correctement configuré, la page d'accueil de votre site fonctionnera normalement,
			mais toutes les autres pages génèreront une erreur 404 - Page non trouvée</p>

		<p>C'est une configuration nécessaire de votre serveur, et ni Joomla! ni sh404SEF n'y peuvent rien changer.</p>

		<p>Joomla! propose un fichier .htaccess très générique. Il fonctionnera probablement immédiatement sur votre
			serveur, mais peut quelquefois requérir des ajustements.
			Le fichier de Joomla! est par défaut appelé htaccess.txt, se situe à la racine de votre site, et doit être
			renommé en .htaccess pour qu'il prenne effet.
			Vous trouverez des informations supplémentaires en Anglais sur les fichiers .htaccess dans la <a
				target="_blank"
				href="https://weeblr.com/documentation">documentation</a>.</p>

		<h2>Extensions</h2>

		<p>sh404SEF peut produire des URL SEF pour beaucoup de
			composants Joomla!.
			Il utilise pour cela un système de <strong>"plugin"</strong> (ou greffon), et est livré avec un plugin pour
			chacun des composants standards de Joomla! (Contact, Weblinks, Newsfeed, Articles,...).
			Il dispose également de plugins pour des composants courants comme Community Builder, JomSocial, Kunena ou
			Virtuemart.
		</p>

		<p>sh404SEF peut également automatiquement utiliser les plugins conçus pour le système SEF de Joomla: les
			fichiers
			router.php.
			La plupart du temps, les plugins sont installés automatiquement quand vous installez une extension.
			Veuillez noter que lorsque vous utilisez l'un de ces plugins non "natif", le fonctionnement peut être
			dégradé.
		</p>

		<p>
			Malgré tout, Joomla! disposant de plusieurs milliers d'extensions, il n'est pas possible d'avoir des plugins
			pour chacune d'entre elles, et dans une telle situation, sh404SEF se rabattra vers une URL simplifiée du
			type:
			monsite.fr/component/option,com_sample/task,view/id,23/Itemid,45/.
			C'est normal et ne peut être amélioré que si un plugin pour l'extension en question est créé.
		</p>

		<h2>Documentation</h2>

		<p>Vous trouverez une documentation détaillée sur <a target="_blank" href="https://weeblr.com/documentation">notre
				site</a>,
			y compris une vidéo <strong>Getting Started</strong> (le tout en Anglais)
		</p>

		<p></p>

		<p></p>
	</div>

	<?php

else :

	?>

	<h1>Désolé, une erreur s'est produite pendant l'installation de sh404SEF sur votre
		site.</h1>
	<p>
		Essayez dans un premier temps de désinstaller l'extension, vérifier que les permissions d'accès aux fichiers
		permettent l'écriture, en particulier ue Joomla! puisse écrire dans le dossier /plugins.
		Ou bien contacter votre administrateur technique pour de l'aide.
	</p>
	<p>Vous pouvez vous rendre également sur notre site et décrire votre problème, en Anglais, <a target="_blank"
	                                                                                              href="https://weeblr.com/helpdesk">sur
			le centre de support technique</a>
	</p>

	<p></p>
	<?php

endif;

