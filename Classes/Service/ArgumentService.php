<?php
namespace Fab\VidiFrontend\Service;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class ArgumentService
 */
class ArgumentService implements SingletonInterface
{

    const PREFIX = 'tx_vidifrontend_templatebasedcontent';

    /**
     * @return $this|object
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        $arguments = GeneralUtility::_GP(self::PREFIX);
        return is_array($arguments)
            ? $arguments
            : [];
    }

    /**
     * @return mixed
     */
    public function getArgument($name)
    {
        $arguments = $this->getArguments();
        return isset($arguments[$name])
            ? $arguments[$name]
            : null;
    }

}
