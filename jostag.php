<?php
/**
* @copyright infograf768 and various GPL sources
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Joomla 3.x
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class plgContentJosTag extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page=0 )
	{		
		// Simple performance check to determine whether plugin should process further
		if (strpos($article->text, 'tag') === false && isset($article->introtext) ? strpos($article->introtext, 'tag') === false : '')
		{
			return true;
		}

		// Define the regular expression for the plugin
		$regex = "#{tag}(.*?){/tag}#s";

		// Perform the replacement
		if ($context === 'com_content.article' || $context === 'mod_custom.content' || $context === 'com_virtuemart.productdetails')
		{
			if (isset($article->introtext))
			{
				$article->introtext = preg_replace_callback($regex, array(&$this,'plgJosTag_replacer'), $article->introtext);
			}

			$article->text = preg_replace_callback($regex, array(&$this,'plgJosTag_replacer'), $article->text );

			return true;
		}
		// Take off the regex in any other context
		else
		{
			if (isset($article->introtext))
			{
				$article->introtext == preg_replace($regex, '', $article->introtext);
			}

			$article->text = preg_replace($regex, '', $article->text);

			return true;
		}
	}

	/**
	 * Replaces the matched tags.
	 *
	 * @param	array	An array of matches (see preg_match_all)
	 * @return	string
	 */
	protected function plgJosTag_replacer (&$matches) 
	{
		$document = JFactory::getDocument();
		$plugin   = JPluginHelper::getPlugin('content', 'jostag');
		$tag      = $matches[1];

		// strip out unwanted HTML elements
		$html_entities_match   = array("|\<br \/\>|", "#<#", "#>#", "|&#39;|", '#&quot;#', '#&nbsp;#' );
		$html_entities_replace = array("\n", '', '', "'", '"', ' ' );
		$tag = preg_replace($html_entities_match, $html_entities_replace, $tag );
		$tag = str_replace("\t", '  ', $tag);
		$tag = str_replace("&lt;", '<', $tag);
		$tag = str_replace("&gt;", '>', $tag);
		if (strpos($tag, 'script') !== false)
		{
			$tag = '<'.$tag.'>';
		}
		else
		{
			$tag = '<'.$tag.' />';
		}

		$document->addCustomTag($tag);
	}
}