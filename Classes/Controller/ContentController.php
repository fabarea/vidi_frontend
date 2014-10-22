<?php
namespace Fab\VidiFrontend\Controller;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Vidi\Domain\Model\Content;
use TYPO3\CMS\Vidi\Mvc\JsonView;
use TYPO3\CMS\Vidi\Mvc\JsonResult;
use TYPO3\CMS\Vidi\Persistence\MatcherObjectFactory;
use TYPO3\CMS\Vidi\Persistence\OrderObjectFactory;
use TYPO3\CMS\Vidi\Persistence\PagerObjectFactory;
use TYPO3\CMS\Vidi\Signal\ProcessContentDataSignalArguments;
use TYPO3\CMS\Vidi\Tca\TcaService;
use TYPO3\CMS\VidiFrontend\Persistence\MatcherFactory;
use TYPO3\CMS\VidiFrontend\Persistence\OrderFactory;

/**
 * Controller which handles actions related to Vidi in the Backend.
 */
class ContentController extends ActionController {

	/**
	 * List action for this controller.
	 *
	 * @return void
	 */
	public function indexAction() {

		$dataType = $this->settings['dataType'];
		if (empty($dataType)) {
			return 'Missing configuration in plugin Vidi Frontend. Please, give a Content Type.';
		}

		// Initialize some objects related to the query.
		$matcher = MatcherFactory::getInstance()->getMatcher(array(), $dataType);
		$order = OrderFactory::getInstance()->getOrder($dataType);
		$pager = PagerObjectFactory::getInstance()->getPager();

		// Fetch objects via the Content Service.
		$contentService = $this->getContentService($dataType)->findBy($matcher, $order, $pager->getLimit(), $pager->getOffset());
		$pager->setCount($contentService->getNumberOfObjects());

		// Assign values.
		$this->view->assign('settings', $this->settings);
		$this->view->assign('gridIdentifier', uniqid('grid-'));
		$this->view->assign('dataType', $dataType);
		$this->view->assign('columns', $this->getFrontendGridService($dataType)->getFields());

		$this->view->assign('objects', $contentService->getObjects());
		$this->view->assign('numberOfObjects', $contentService->getNumberOfObjects());
		$this->view->assign('pager', $pager);
	}

	/**
	 * List Row action for this controller. Output a json list of contents
	 *
	 * @param array $columns corresponds to columns to be rendered.
	 * @param array $matches
	 * @validate $columns TYPO3\CMS\Vidi\Domain\Validator\ColumnsValidator
	 * @validate $matches TYPO3\CMS\Vidi\Domain\Validator\MatchesValidator
	 * @return void
	 */
	public function listAction(array $columns = array(), $matches = array()) {

		// Initialize some objects related to the query.
		$matcher = MatcherObjectFactory::getInstance()->getMatcher($matches);
		$order = OrderObjectFactory::getInstance()->getOrder();
		$pager = PagerObjectFactory::getInstance()->getPager();

		// Fetch objects via the Content Service.
		$contentService = $this->getContentService()->findBy($matcher, $order, $pager->getLimit(), $pager->getOffset());
		$pager->setCount($contentService->getNumberOfObjects());

		// Assign values.
		$this->view->assign('columns', $columns);
		$this->view->assign('objects', $contentService->getObjects());
		$this->view->assign('numberOfObjects', $contentService->getNumberOfObjects());
		$this->view->assign('pager', $pager);
		$this->view->assign('response', $this->response);
	}

	/**
	 * Retrieve Content objects first according to matching criteria and then "copy" them.
	 *
	 * Possible values for $matches, refer to method "updateAction".
	 *
	 * @param string $target
	 * @param array $matches
	 * @throws \Exception
	 * @return string
	 */
	public function showAction($content) {
	}

	/**
	 * Get the Vidi Module Loader.
	 *
	 * @param string $dataType
	 * @return \TYPO3\CMS\VidiFrontend\Service\ContentService
	 */
	protected function getContentService($dataType) {
		return GeneralUtility::makeInstance('TYPO3\CMS\VidiFrontend\Service\ContentService', $dataType);
	}

	/**
	 * @return \TYPO3\CMS\Vidi\Resolver\ContentObjectResolver
	 */
	protected function getContentObjectResolver() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\ContentObjectResolver');
	}

	/**
	 * @return \TYPO3\CMS\Vidi\Resolver\FieldPathResolver
	 */
	protected function getFieldPathResolver () {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\FieldPathResolver');
	}

	/**
	 * Return a special view for handling JSON
	 * Goal is to have this view injected but require more configuration.
	 *
	 * @return JsonView
	 */
	protected function getJsonView() {
		if (!$this->view instanceof JsonView) {
			/** @var JsonView $view */
			$this->view = $this->objectManager->get('TYPO3\CMS\Vidi\Mvc\JsonView');
			$this->view->setResponse($this->response);
		}
		return $this->view;
	}

	/**
	 * @return JsonResult
	 */
	protected function getJsonResult() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Mvc\JsonResult');
	}

	/**
	 * Signal that is called for post-processing content data send to the server for update.
	 *
	 * @param Content $contentObject
	 * @param $fieldNameAndPath
	 * @param $contentData
	 * @param $counter
	 * @param $savingBehavior
	 * @param $language
	 * @return ProcessContentDataSignalArguments
	 * @signal
	 */
	protected function emitProcessContentDataSignal(Content $contentObject, $fieldNameAndPath, $contentData, $counter, $savingBehavior, $language) {

		/** @var \TYPO3\CMS\Vidi\Signal\ProcessContentDataSignalArguments $signalArguments */
		$signalArguments = GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Signal\ProcessContentDataSignalArguments');
		$signalArguments->setContentObject($contentObject)
			->setFieldNameAndPath($fieldNameAndPath)
			->setContentData($contentData)
			->setCounter($counter)
			->setSavingBehavior($savingBehavior)
			->setLanguage($language);

		$signalResult = $this->getSignalSlotDispatcher()->dispatch('TYPO3\CMS\Vidi\Controller\Backend\ContentController', 'processContentData', array($signalArguments));
		return $signalResult[0];
	}

	/**
	 * Get the SignalSlot dispatcher.
	 *
	 * @return \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 */
	protected function getSignalSlotDispatcher() {
		return $this->objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
	}

	/**
	 * @return \TYPO3\CMS\Vidi\Language\LanguageService
	 */
	protected function getLanguageService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Language\LanguageService');
	}

	/**
	 * @param string $dataType
	 * @return \Fab\VidiFrontend\Tca\FrontendGridService
	 */
	protected function getFrontendGridService($dataType) {
		return GeneralUtility::makeInstance('Fab\VidiFrontend\Tca\FrontendGridService', $dataType);
	}
}
