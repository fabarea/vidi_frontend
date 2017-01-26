<?php
namespace Fab\VidiFrontend\Controller;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Configuration\ColumnsConfiguration;
use Fab\VidiFrontend\Configuration\ContentElementConfiguration;
use Fab\VidiFrontend\MassAction\MassActionInterface;
use Fab\VidiFrontend\Persistence\PagerFactory;
use Fab\VidiFrontend\Service\ContentElementService;
use Fab\VidiFrontend\Service\ContentService;
use Fab\VidiFrontend\Service\ContentType;
use Fab\VidiFrontend\Tca\FrontendTca;
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

/**
 * Controller which handles actions related to Vidi in the Backend.
 */
class ContentController extends ActionController
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

        if (empty($settings['dataType'])) {
            return '<strong style="color: red">Please select a content type to be displayed!</strong>';
        }
        $dataType = $settings['dataType'];

        // Set dynamic value for the sake of the Visual Search.
        if ($settings['isVisualSearchBar']) {
            $settings['loadContentByAjax'] = 1;
        }

        // Handle columns case
        $columns = ColumnsConfiguration::getInstance()->get($dataType, $settings['columns']);
        if (count($columns) === 0) {
            return '<strong style="color: red">Please select at least one column to be displayed!</strong>';
        }

        // Assign values.
        $this->view->assign('columns', $columns);
        $this->view->assign('settings', $settings);
        $this->view->assign('gridIdentifier', $this->getGridIdentifier($settings));
        $this->view->assign('contentElementIdentifier', $this->configurationManager->getContentObject()->data['uid']);
        $this->view->assign('dataType', $dataType);
        $this->view->assign('objects', []);
        $this->view->assign('numberOfColumns', count($columns));

        if (!$settings['loadContentByAjax']) {

            // Initialize some objects related to the query.
            $matcher = MatcherFactory::getInstance()->getMatcher($settings, $matches, $dataType);
            $order = OrderFactory::getInstance()->getOrder($settings, $dataType);

            // Fetch objects via the Content Service.
            $contentService = $this->getContentService()
                ->setDataType($dataType)
                ->setSettings($settings)
                ->findBy($matcher, $order, (int)$settings['limit']);
            $this->view->assign('objects', $contentService->getObjects());
        }

        // Initialize Content Element settings to be accessible across the request life cycle.
        $contentObjectRenderer = $this->configurationManager->getContentObject();
        ContentElementConfiguration::getInstance($contentObjectRenderer->data);
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
     * List Row action for this controller. Output a json list of contents
     *
     * @param array $contentData
     * @param string $actionName
     * @param array $matches
     * @validate $contentData Fab\VidiFrontend\Domain\Validator\ContentDataValidator
     * @return string
     */
    public function executeAction(array $contentData, $actionName, array $matches = [])
    {
        $settings = ContentElementConfiguration::getInstance($contentData)->getSettings();
        $settings = $this->computeFinalSettings($settings);

        $dataType = $settings['dataType'];

        $massActions = FrontendTca::grid($dataType)->getMassActions();

        if (empty($massActions[$actionName])) {
            return '<strong style="color: red">Action Name is not valid.</strong>';
        }

        // In the context of Ajax, we must define manually the current Content Element object.
        $contentObjectRenderer = $this->getContentElementService($dataType)->getContentObjectRender($contentData);
        $this->configurationManager->setContentObject($contentObjectRenderer);

        // Initialize some objects related to the query.
        $matcher = MatcherFactory::getInstance()->getMatcher($settings, $matches, $dataType);
        $order = OrderFactory::getInstance()->getOrder($settings, $dataType);

        // Fetch objects via the Content Service.
        $contentService = $this->getContentService()
            ->setDataType($dataType)
            ->setSettings($settings)
            ->findBy($matcher, $order);

        // Assign values.
        $this->view->assign('objects', $contentService->getObjects());
        $this->view->assign('numberOfObjects', $contentService->getNumberOfObjects());

        /** @var MassActionInterface $action */
        $action = $massActions[$actionName];
        $result = $action->with($settings)->execute($contentService);

        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Response $response */
        $response = $this->response;
        foreach ($result->getHeaders() as $name => $value) {
            $response->setHeader($name, $value);
        }
        $response->sendHeaders();

        if ($result->hasFile()) {
            readfile($result->getFile());
            $task = $result->getCleanUpTask();
            $task();
            exit();
        } else {
            return $result->getOutput();
        }
    }

    /**
     * @param Content $content
     * @return string
     */
    public function showAction(Content $content)
    {
        $settings = $this->computeFinalSettings($this->settings);

        // Configure the template path according to the Plugin settings.
        $pathAbs = GeneralUtility::getFileAbsFileName($settings['templateDetail']);
        if (!is_file($pathAbs)) {
            return '<strong style="color: red">I could not find the appropriate template file.</strong>';
        }

        $variableName = 'object';
        $dataType = $this->getContentType()->getCurrentType();
        if (isset($settings['fluidVariables'][$dataType]) && basename($settings['templateDetail']) !== 'Show.html') {
            $variableName = $settings['fluidVariables'][$dataType];
        }

        $this->view->setTemplatePathAndFilename($pathAbs);
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
     * Take some specific values and transform as as unique md5 identifier.
     *
     * @param array $settings
     * @return string
     */
    protected function getGridIdentifier(array $settings)
    {

        $key = $this->configurationManager->getContentObject()->data['uid'];
        $key .= $settings['dataType'];
        $key .= $settings['columns'];
        $key .= $settings['sorting'];
        $key .= $settings['direction'];
        $key .= $settings['defaultNumberOfItems'];
        $key .= $settings['loadContentByAjax'];
        $key .= $settings['facets'];
        $key .= $settings['isVisualSearchBar'];
        return md5($key);
    }

    /**
     * Get the Vidi Module Loader.
     *
     * @return ContentService
     * @throws \InvalidArgumentException
     */
    protected function getContentService()
    {
        return GeneralUtility::makeInstance(ContentService::class);
    }

    /**
     * Get the Vidi Module Loader.
     *
     * @param string $dataType
     * @return ContentElementService
     * @throws \InvalidArgumentException
     */
    protected function getContentElementService($dataType)
    {
        return GeneralUtility::makeInstance(ContentElementService::class, $dataType);
    }

    /**
     * @return ContentType
     * @throws \InvalidArgumentException
     */
    protected function getContentType()
    {
        return GeneralUtility::makeInstance(ContentType::class);
    }

}
