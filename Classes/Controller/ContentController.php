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

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Vidi\Domain\Model\Content;
use TYPO3\CMS\Vidi\Persistence\PagerObjectFactory;
use TYPO3\CMS\VidiFrontend\Persistence\MatcherFactory;
use TYPO3\CMS\VidiFrontend\Persistence\OrderFactory;

/**
 * Controller which handles actions related to Vidi in the Backend.
 */
class ContentController extends ActionController {

	/**
	 * @throws \TYPO3\CMS\Media\Exception\StorageNotOnlineException
	 */
	public function initializeAction() {

		if ($this->arguments->hasArgument('content')) {

			/** @var \Fab\VidiFrontend\TypeConverter\ContentConverter $typeConverter */
			$typeConverter = $this->objectManager->get('Fab\VidiFrontend\TypeConverter\ContentConverter');

			$this->arguments->getArgument('content')
				->getPropertyMappingConfiguration()
				->setTypeConverter($typeConverter);
		}
	}

	/**
	 * List action for this controller.
	 *
	 * @return void
	 */
	public function indexAction() {

		$dataType = empty($this->settings['dataType']) ? 'fe_users' : $this->settings['dataType'];

		// Assign values.
		$this->view->assign('settings', $this->settings);
		$this->view->assign('gridIdentifier', $this->configurationManager->getContentObject()->data['uid']);
		$this->view->assign('dataType', $dataType);
		$this->view->assign('columns', FrontendTca::grid($dataType)->getFields());
	}

	/**
	 * List Row action for this controller. Output a json list of contents
	 *
	 * @param array $columns corresponds to columns to be rendered.
	 * @param array $matches
	 * @validate $columns Fab\VidiFrontend\Domain\Validator\ColumnsValidator
	 * @validate $matches Fab\VidiFrontend\Domain\Validator\MatchesValidator
	 * @return void
	 */
	public function listAction(array $columns = array(), $matches = array()) {

		$dataType = GeneralUtility::_GP('dataType');

		// Initialize some objects related to the query.
		$matcher = MatcherFactory::getInstance()->getMatcher(array(), $dataType);
		$order = OrderFactory::getInstance()->getOrder($dataType);
		$pager = PagerObjectFactory::getInstance()->getPager();

		// Fetch objects via the Content Service.
		$contentService = $this->getContentService($dataType)->findBy($matcher, $order, $pager->getLimit(), $pager->getOffset());
		$pager->setCount($contentService->getNumberOfObjects());

		// Set format.
		$this->request->setFormat('json');

		// Assign values.
		$this->view->assign('columns', $columns);
		$this->view->assign('objects', $contentService->getObjects());
		$this->view->assign('numberOfObjects', $contentService->getNumberOfObjects());
		$this->view->assign('pager', $pager);
		$this->view->assign('response', $this->response);
	}

	/**
	 * @param Content $content
	 */
	public function showAction(Content $content) {

		// Configure the template path according to the Plugin settings.
		$pathAbs = GeneralUtility::getFileAbsFileName($this->settings['template']);
		if (!is_file($pathAbs)) {
			return sprintf('I could not find the template file <strong>%s</strong>', $pathAbs);
		}

		$variableName = 'object';
		$dataType = $this->getContentType()->getCurrentType();
		if (isset($this->settings['fluidVariables'][$dataType]) && basename($this->settings['template']) !== 'Show.html') {
			$variableName = $this->settings['fluidVariables'][$dataType];
		}

		$this->view->setTemplatePathAndFilename($pathAbs);
		$this->view->assign($variableName, $content);
	}

	/**
	 * Get the Vidi Module Loader.
	 *
	 * @param string $dataType
	 * @return \Fab\VidiFrontend\Service\ContentService
	 */
	protected function getContentService($dataType) {
		return GeneralUtility::makeInstance('Fab\VidiFrontend\Service\ContentService', $dataType);
	}

	/**
	 * @return \Fab\VidiFrontend\Service\ContentType
	 */
	protected function getContentType() {
		return GeneralUtility::makeInstance('Fab\VidiFrontend\Service\ContentType');
	}

//	/**
//	 * @return \TYPO3\CMS\Vidi\Resolver\ContentObjectResolver
//	 */
//	protected function getContentObjectResolver() {
//		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\ContentObjectResolver');
//	}
//
//	/**
//	 * @return \TYPO3\CMS\Vidi\Resolver\FieldPathResolver
//	 */
//	protected function getFieldPathResolver () {
//		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\FieldPathResolver');
//	}
//
//	/**
//	 * Return a special view for handling JSON
//	 * Goal is to have this view injected but require more configuration.
//	 *
//	 * @return JsonView
//	 */
//	protected function getJsonView() {
//		if (!$this->view instanceof JsonView) {
//			/** @var JsonView $view */
//			$this->view = $this->objectManager->get('TYPO3\CMS\Vidi\Mvc\JsonView');
//			$this->view->setResponse($this->response);
//		}
//		return $this->view;
//	}
//
//	/**
//	 * @return JsonResult
//	 */
//	protected function getJsonResult() {
//		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Mvc\JsonResult');
//	}
//
//	/**
//	 * Signal that is called for post-processing content data send to the server for update.
//	 *
//	 * @param Content $contentObject
//	 * @param $fieldNameAndPath
//	 * @param $contentData
//	 * @param $counter
//	 * @param $savingBehavior
//	 * @param $language
//	 * @return ProcessContentDataSignalArguments
//	 * @signal
//	 */
//	protected function emitProcessContentDataSignal(Content $contentObject, $fieldNameAndPath, $contentData, $counter, $savingBehavior, $language) {
//
//		/** @var \TYPO3\CMS\Vidi\Signal\ProcessContentDataSignalArguments $signalArguments */
//		$signalArguments = GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Signal\ProcessContentDataSignalArguments');
//		$signalArguments->setContentObject($contentObject)
//			->setFieldNameAndPath($fieldNameAndPath)
//			->setContentData($contentData)
//			->setCounter($counter)
//			->setSavingBehavior($savingBehavior)
//			->setLanguage($language);
//
//		$signalResult = $this->getSignalSlotDispatcher()->dispatch('TYPO3\CMS\Vidi\Controller\Backend\ContentController', 'processContentData', array($signalArguments));
//		return $signalResult[0];
//	}
//
//	/**
//	 * Get the SignalSlot dispatcher.
//	 *
//	 * @return \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
//	 */
//	protected function getSignalSlotDispatcher() {
//		return $this->objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
//	}
//
//	/**
//	 * @return \TYPO3\CMS\Vidi\Language\LanguageService
//	 */
//	protected function getLanguageService() {
//		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Language\LanguageService');
//	}

}
