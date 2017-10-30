<?php
namespace Fab\VidiFrontend\Controller;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Configuration\ContentElementConfiguration;
use Fab\VidiFrontend\Persistence\PagerFactory;
use Fab\VidiFrontend\Service\ContentElementService;
use Fab\VidiFrontend\Service\ContentService;
use Fab\VidiFrontend\Service\ContentType;
use Fab\VidiFrontend\TypeConverter\ContentConverter;
use Fab\VidiFrontend\TypeConverter\ContentDataConverter;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Fab\Vidi\Domain\Model\Content;
use Fab\VidiFrontend\Persistence\MatcherFactory;
use Fab\VidiFrontend\Persistence\OrderFactory;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class TemplateBasedContentController
 */
class TemplateBasedContentController extends ActionController
{

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeAction()
    {
        if ($this->arguments->hasArgument('content')) {

            /** @var ContentConverter $typeConverter */
            $typeConverter = $this->objectManager->get(ContentConverter::class);

            $this->arguments->getArgument('content')
                ->getPropertyMappingConfiguration()
                ->setTypeConverter($typeConverter);
        }

        if ($this->arguments->hasArgument('contentData')) {

            /** @var ContentConverter $typeConverter */
            $typeConverter = $this->objectManager->get(ContentDataConverter::class);

            $this->arguments->getArgument('contentData')
                ->getPropertyMappingConfiguration()
                ->setTypeConverter($typeConverter);
        }
    }

    /**
     * List action for this controller.
     *
     * @param array $matches
     * @return string
     */
    public function indexAction(array $matches = [])
    {
        $settings = $this->computeFinalSettings($this->settings);

        $contentObjectRender = $this->configurationManager->getContentObject();

        /** @var StandaloneView $view */
        $view = $this->objectManager->get(StandaloneView::class);

        // Configure the template path according to the Plugin settings.
        $fileNameAndPath = GeneralUtility::getFileAbsFileName($settings['templateList']);
        if (!is_file($fileNameAndPath)) {
            return '<strong style="color: red">I could not find the appropriate template file.</strong>';
        }
        $view->setTemplatePathAndFilename($fileNameAndPath);

        $settings = $this->computeFinalSettings($this->settings);

        // Assign values.
        $view->assign('settings', $settings);
        $view->assign('page', $this->getTypoScriptFrontendController()->page);
        $view->assign('contentElement', $contentObjectRender->data);
        $view->assign('dataType', $settings['dataType']);
        $view->assign('objects', []);

        if (!$settings['loadByAjax']) {

            // Initialize some objects related to the query.
            $matcher = MatcherFactory::getInstance()->getMatcher($settings, $matches, $settings['dataType']);
            $order = OrderFactory::getInstance()->getOrder($settings, $settings['dataType']);

            // Fetch objects via the Content Service.
            $contentService = $this->getContentService()
                ->setDataType($settings['dataType'])
                ->setSettings($settings)
                ->findBy($matcher, $order, (int)$settings['limit']);
            $view->assign('objects', $contentService->getObjects());
        }

        return $view->render();
    }

    /**
     * List Row action for this controller. Output a json list of contents
     *
     * @param array $matches
     * @param array $contentData
     * @validate $contentData Fab\VidiFrontend\Domain\Validator\ContentDataValidator
     * @return void
     */
    public function listAction(array $contentData, array $matches = [])
    {
        $settings = ContentElementConfiguration::getInstance($contentData)->getSettings();
        $settings = $this->computeFinalSettings($settings);

        $dataType = $settings['dataType'];

        // In the context of Ajax, we must define manually the current Content Element object.
        $contentObjectRenderer = $this->getContentElementService($dataType)->getContentObjectRender($contentData);
        $this->configurationManager->setContentObject($contentObjectRenderer);

        // Initialize some objects related to the query.
        $matcher = MatcherFactory::getInstance()->getMatcher($settings, $matches, $dataType);
        $order = OrderFactory::getInstance()->getOrder($settings, $dataType);
        $pager = PagerFactory::getInstance()->getPager();

        $length = GeneralUtility::_GET('length');
        if ($length !== null && MathUtility::canBeInterpretedAsInteger($length)) {
            $length = (int)$length;
            if ($length > -1) {
                $settings['limit'] = $length;
            }
        }

        // Set a default value. It wasn't a default value in FlexForm at first
        // and we want an integer value in any case.
        if ($settings['limit'] === '') {
            $settings['limit'] = 10;
        }

        $pager->setLimit((int)$settings['limit']);

        // Fetch objects via the Content Service.
        $contentService = $this->getContentService()
            ->setDataType($dataType)
            ->setSettings($settings)
            ->findBy($matcher, $order, $pager->getLimit(), $pager->getOffset());
        $pager->setCount($contentService->getNumberOfObjects());

        // Set format.
        $this->request->setFormat('json');

        // Assign values.
        $this->view->assign('objects', $contentService->getObjects());
        $this->view->assign('numberOfObjects', $contentService->getNumberOfObjects());
        $this->view->assign('pager', $pager);
        $this->view->assign('response', $this->response);
    }

    /**
     * @param Content $content
     * @return string
     */
    public function showAction(Content $content)
    {
        $settings = $this->computeFinalSettings($this->settings);

        // Configure the template path according to the Plugin settings.
        $fileNameAndPath = GeneralUtility::getFileAbsFileName($settings['templateDetail']);
        if (!is_file($fileNameAndPath)) {
            return '<strong style="color: red">I could not find the appropriate template file.</strong>';
        }

        $variableName = 'object';
        $dataType = $this->getContentType()->getCurrentType();
        if (isset($settings['fluidVariables'][$dataType]) && basename($settings['templateDetail']) !== 'Show.html') {
            $variableName = $settings['fluidVariables'][$dataType];
        }

        $this->view->setTemplatePathAndFilename($fileNameAndPath);
        $this->view->assign($variableName, $content);
    }

    /**
     * Merge with "raw" TypoScript configuration into Flexform settings.
     *
     * @param array $settings
     * @return array
     */
    protected function computeFinalSettings(array $settings) {

        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $ts = GeneralUtility::removeDotsFromTS($configuration['plugin.']['tx_vidifrontend.']['settings.']);
        ArrayUtility::mergeRecursiveWithOverrule($settings, $ts);

        return $settings;
    }

    /**
     * Returns an instance of the Frontend object.
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * Get the Vidi Module Loader.
     *
     * @return object|ContentService
     */
    protected function getContentService()
    {
        return GeneralUtility::makeInstance(ContentService::class);
    }

    /**
     * Get the Vidi Module Loader.
     *
     * @param string $dataType
     * @return object|ContentElementService
     */
    protected function getContentElementService($dataType)
    {
        return GeneralUtility::makeInstance(ContentElementService::class, $dataType);
    }

    /**
     * @return object|ContentType
     */
    protected function getContentType()
    {
        return GeneralUtility::makeInstance(ContentType::class);
    }

}
