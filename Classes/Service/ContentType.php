<?php
namespace Fab\VidiFrontend\Service;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use Fab\Vidi\Service\DataService;
use Fab\VidiFrontend\Plugin\PluginParameter;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service related to the Content type.
 */
class ContentType implements SingletonInterface
{

    /**
     * @var array
     */
    protected $contentTypes = [];

    /**
     * Return the current content type.
     *
     * @throws \Exception
     * @return string
     */
    public function getCurrentType()
    {

        $parameters = GeneralUtility::_GP(PluginParameter::PREFIX)
            ? GeneralUtility::_GP(PluginParameter::PREFIX)
            : GeneralUtility::_GP(PluginParameter::PREFIX_TEMPLATE_BASED_CONTENT);

        if ($parameters === null) {
            throw new \Exception('Missing parameter or wrong prefix...', 1579098327);
        }

        if (empty($parameters['contentElement'])) {
            throw new \Exception('Missing parameter "contentElement"...', 1414713537);
        }

        $contentElementIdentifier = (int)$parameters['contentElement'];

        if (empty($this->contentTypes[$contentElementIdentifier])) {

            $contentElement = $this->getDataService()->getRecord(
                'tt_content',
                [
                    'uid' => $contentElementIdentifier,
                ]
            );

            $xml = GeneralUtility::xml2array($contentElement['pi_flexform']);

            if (!empty($xml['data']['general']['lDEF']['settings.dataType']['vDEF'])) {
                $dataType = $xml['data']['general']['lDEF']['settings.dataType']['vDEF'];
            } else {
                throw new \Exception('I could find data type in Content Element: ' . $contentElementIdentifier, 1413992029);
            }
            $this->contentTypes[$contentElementIdentifier] = $dataType;
        }

        return $this->contentTypes[$contentElementIdentifier];
    }

    /**
     * @return object|DataService
     */
    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    /**
     * Returns an instance of the page repository.
     *
     * @return PageRepository
     */
    static protected function getPageRepository()
    {
        return $GLOBALS['TSFE']->sys_page;
    }
}
