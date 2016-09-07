<?php
namespace Fab\VidiFrontend\MassAction;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class WriteFileTrait
 */
trait TemporaryFileTrait
{

    /**
     * @var string
     */
    protected $temporaryDirectory;

    /**
     * @var string
     */
    protected $exportFileNameAndPath;

    /**
     * @var string
     */
    protected $zipFileNameAndPath;

    /**
     * Initialize some properties
     *
     * @param array $objects
     * @return void
     */
    protected function initializeEnvironment(array $objects)
    {

        /** @var \Fab\Vidi\Domain\Model\Content $object */
        $object = reset($objects);

        $this->temporaryDirectory = PATH_site . 'typo3temp/' . uniqid('vidi-', true) . '/';
        GeneralUtility::mkdir($this->temporaryDirectory);

        // Compute file name and path variable
        $this->exportFileNameAndPath = $this->temporaryDirectory . $object->getDataType() . '-' . date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy']);

        // Compute file name and path variable for zip
        $zipFileName = $object->getDataType() . '-' . date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy']) . '.zip';
        $this->zipFileNameAndPath = $this->temporaryDirectory . $zipFileName;
    }

    /**
     * @return void
     */
    protected function cleanUpTemporaryFiles()
    {
        GeneralUtility::rmdir($this->temporaryDirectory, true);
    }

}
