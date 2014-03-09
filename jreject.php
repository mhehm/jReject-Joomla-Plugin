<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.jreject
 *
 * @copyright   Copyright (C) 2014 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class PlgSystemJreject extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	public function onBeforeRender()
	{
		$app = JFactory::getApplication();

		if($app->isAdmin())
		{
			return;
		}

		JHtml::_('jquery.framework');

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true) . '/media/jreject/css/jquery.reject.min.css');
		$document->addScript(JURI::base(true) . '/media/jreject/js/jquery.reject.min.js');

		$script = 'jQuery(document).ready(function(){jQuery.reject({';

		// Rejection flags for specific browsers
		if($reject = $this->params->get('reject', array()))
		{
			$script .= 'reject:{';

			if(in_array('all', $reject))
			{
				$script .= 'all:true';
			}
			else
			{
				foreach($reject as $r)
				{
					$script .= $r . ':true,';
				}
			}

			$script .= '},';
		}

		// What browsers to display and their order
		if($display = $this->params->get('display', array()))
		{
			$script .= 'display:[' . implode(',' ,$display) . '],'; 
		}
		else
		{
			$display = array('firefox','chrome','msie','safari','opera','gcf');
		}

		// Settings for which browsers to display
		$script .= 'browserInfo:{';
		foreach($display as $browser)
		{
			$browser = str_replace("'", '', $browser);
			$script .= $browser . ':{';
			$script .= "text:'" . JText::_('PLG_SYSTEM_JREJECT_BROWSER_' . strtoupper($browser)) . "',";

			if($browserURL = $this->params->get($browser . 'URL'))
			{
				$script .= "url:'" . $browserURL . "',";
			}

			$script .= '},';
		}
		$script .= '},';

		// Header of pop-up window
		$script .= "header:'" . JText::_('PLG_SYSTEM_JREJECT_HEADER') . "',";

		// Paragraph 1
		$script .= "paragraph1:'" . JText::_('PLG_SYSTEM_JREJECT_PARAGRAPH1') . "',";

		// Paragraph 2
		$script .= "paragraph2:'" . JText::_('PLG_SYSTEM_JREJECT_PARAGRAPH2') . "',";

		// DO not allow closing of window
		if(!$this->params->get('close', 1))
		{
			$script .= 'close:false,';
		}

		// Message displayed below closing link
		$script .= "closeMessage:'" . JText::_('PLG_SYSTEM_JREJECT_CLOSEMESSAGE') . "',";

		// Text for closing link
		$script .= "closeLink:'" . JText::_('PLG_SYSTEM_JREJECT_CLOSELINK') . "',";

		// Close URL. Default: #
		if($closeURL = $this->params->get('closeURL'))
		{
			$script .= "closeURL:'" . $closeURL . "',";
		}

		// Allow closing of window with esc key
		if(!$this->params->get('closeESC', 1))
		{
			$script .= 'closeESC:false,';
		 
		}

		// If cookies should be used to remmember if the window was closed
		if($this->params->get('closeCookie', 0))
		{
			$script .= 'closeCookie:true,';
			
			if($expires = (int) $this->params->get('expires'))
			{
				$script .= 'cookieSettings:{expires:' . $expires . '},';
			}
		}

		// Path where images are located
		$script .= "imagePath:'" . JUri::base(true) . "/media/jreject/images/',";
		
		// Background color for overlay
		if($overlayBgColor = $this->params->get('overlayBgColor'))
		{
			$script .= "overlayBgColor:'" . $overlayBgColor . "',";
		}

		// Background transparency (0-1)
		$overlayOpacity = $this->params->get('overlayOpacity', 80);
		$script .= 'overlayOpacity:' . $overlayOpacity / 100 . ',';

		// Fade in time on open ('slow','medium','fast' or integer in ms) 
		if(($fadeInTime = $this->params->get('fadeInTime')) && ($fadeInTime != 'fast'))
		{
			if($fadeInTime != 'custom')
			{
				$script .= "fadeInTime:'" . $fadeInTime . "',";
			}
			else
			{
				$script .= 'fadeInTime:' . (int) $this->params->get('fadeInTimeCustom') . ',';
			}
		}

		// Fade out time on close ('slow','medium','fast' or integer in ms) 
		if(($fadeOutTime = $this->params->get('fadeOutTime')) && ($fadeOutTime != 'fast'))
		{
			if($fadeOutTime != 'custom')
			{
				$script .= "fadeOutTime:'" . $fadeOutTime . "',";
			}
			else
			{
				$fadeOutTime .= 'fadeOutTime:' . (int) $this->params->get('fadeOutTimeCustom') . ',';
			}
		}

		$script .= '})});';

		$document->addScriptDeclaration($script);
	}
}
