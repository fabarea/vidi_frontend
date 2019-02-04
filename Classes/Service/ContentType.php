<?php
namespace Fab\VidiFrontend\Service;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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

        $parameters = GeneralUtility::_GP(PluginParameter::PREFIX);
        if (empty($parameters['contentElement'])) {
            throw new \Exception('Missing parameter...', 1414713537);
        }

        $contentElementIdentifier = (int)$parameters['contentElement'];

        if (empty($this->contentTypes[$contentElementIdentifier])) {

            $clause = sprintf('uid = %s ', $contentElementIdentifier);
            $clause .= self::getPageRepository()->enableFields('tt_content');
            $clause .= self::getPageRepository()->deleteClause('tt_content');
            $contentElement = self::getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'tt_content', $clause);

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
     * Returns a pointer to the database.
     *
     * @return \Fab\Vidi\Database\DatabaseConnection
     */
    static protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Returns an instance of the page repository.
     *
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    static protected function getPageRepository()
    {
        return $GLOBALS['TSFE']->sys_page;
    }
}
