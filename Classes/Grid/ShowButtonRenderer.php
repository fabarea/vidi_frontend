<?php
namespace Fab\VidiFrontend\Grid;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Fab\VidiFrontend\Plugin\PluginParameter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Vidi\Grid\GridRendererAbstract;

/**
 * Class for editing mm relation between objects.
 */
class ShowButtonRenderer extends GridRendererAbstract {

	/**
	 * Render a representation of the relation on the GUI.
	 *
	 * @return string
	 */
	public function render() {

		/** @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder $tagBuilder */
		$tagBuilder = $this->getObjectManager()->get('TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder');
		$tagBuilder->reset();
		$tagBuilder->setTagName('a');
		$tagBuilder->setContent('<span class="glyphicon glyphicon-eye-open"></span>');

		$arguments = array(
			PluginParameter::PREFIX => array(
				'content' => $this->getObject()->getUid(),
				'action' => 'show',
				'contentElement' => $this->getCurrentContentElement()->data['uid'],
				'controller' => 'Content',
			),
		);
		$this->getUriBuilder()->setArguments($arguments);
		$uri = $this->getUriBuilder()->build();

		$tagBuilder->addAttribute('href', $uri);
		$tagBuilder->addAttribute('class', 'link-show');
		$tagBuilder->addAttribute('title', LocalizationUtility::translate('showDetail', 'vidi_frontend'));
		return $tagBuilder->render();
	}


	/**
	 * @return \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected function getObjectManager() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
	 */
	public function getUriBuilder() {
		$configuration = $this->getGridRendererConfiguration();
		return $configuration['uriBuilder'];
	}

	/**
	 * @return \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	public function getCurrentContentElement() {
		$configuration = $this->getGridRendererConfiguration();
		return $configuration['contentElement'];
	}

}
