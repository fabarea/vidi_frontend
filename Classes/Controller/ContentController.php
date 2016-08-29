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
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    public function indexAction(array $matches = [])
    {
        $settings = $this->computeFinalSettings($this->settings);

        if (empty($settings['dataType'])) {
            $this->redirect('warn', null, null, ['code' => 1457540575]);
        }
        $dataType = $settings['dataType'];

        // Set dynamic value for the sake of the Visual Search.
        if ($settings['isVisualSearchBar']) {
            $settings['loadContentByAjax'] = 1;
        }

        // Handle columns case
        $columns = ColumnsConfiguration::getInstance()->get($dataType, $settings['columns']);
        if (count($columns) === 0) {
            $this->redirect('warn', null, null, ['code' => 1457540589]);
        }

        // Assign values.
        $this->view->assign('columns', $columns);
        $this->view->assign('settings', $settings);
        $this->view->assign('gridIdentifier', $this->getGridIdentifier($settings));
        $this->view->assign('contentElementIdentifier', $this->configurationManager->getContentObject()->data['uid']);
        $this->view->assign('dataType', $dataType);
        $this->view->assign('objects', array());
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
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
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

        // Restrict number of records.
        if ((int)$settings['limit'] > 0) {
            $pager->setLimit((int)$settings['limit']);
            $pager->setOffset((int)$settings['limit']);
        }

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
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    public function executeAction(array $contentData, $actionName, array $matches = [])
    {
        $settings = ContentElementConfiguration::getInstance($contentData)->getSettings();
        $settings = $this->computeFinalSettings($settings);

        $dataType = $settings['dataType'];

        $massActions = FrontendTca::grid($dataType)->getMassActions();

        if (empty($massActions[$actionName])) {
            $this->redirect('warn', null, null, ['code' => 1457540597]);
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

        return $result->getOutput();
    }

    /**
     * @param int $code
     * @return string
     */
    public function warnAction($code = null)
    {
        $message = 'An unknown error happened';
        if ((int)$code === 1457540575) {
            $message = 'Please select a content type to be displayed!';
        } elseif ((int)$code === 1457540589) {
            $message = 'Please select at least one column to be displayed!';
        } elseif ((int)$code === 1457540597) {
            $message = 'Action Name is not valid';
        } elseif ((int)$code === 1457551693) {
            $message = 'I could not find the appropriate template file';
        }

        return sprintf(
            '<strong style="color: red">%s%s</strong>',
            $message,
            GeneralUtility::getApplicationContext()->isDevelopment() ? ', code: ' . $code : ''
        );
    }

    /**
     * @param Content $content
     * @return void
     */
    public function showAction(Content $content)
    {
        $settings = $this->computeFinalSettings($this->settings);

        // Configure the template path according to the Plugin settings.
        $pathAbs = GeneralUtility::getFileAbsFileName($settings['templateDetail']);
        if (!is_file($pathAbs)) {
            return ''; // Prevent bug if two vidi plugins are installed on the same page and one has not template detail.
            $this->redirect('warn', null, null, ['code' => 1457551693]);
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
