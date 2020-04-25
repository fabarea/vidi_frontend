<?php

namespace Fab\VidiFrontend\Backend;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Service\DataService;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * A class to interact with TCEForms.
 */
class DisplayCondition
{

    /**
     * @return bool
     */
    public function hasSelection()
    {
        $records = $this->getDataService()->getRecords('tx_vidi_selection');
        return !empty($records);
    }

    /**
     * @return object|DataService
     */
    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }
}