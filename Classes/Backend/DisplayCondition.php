<?php
namespace Fab\VidiFrontend\Backend;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Utility\BackendUtility;


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
        $tableName = 'tx_vidi_selection';
        $clause = '1 = 1' . BackendUtility::deleteClause($tableName);
        $records = $this->getDatabaseConnection()->exec_SELECTgetRows('*', $tableName, $clause);
        return !empty($records);
    }

    /**
     * Returns a pointer to the database.
     *
     * @return \Fab\Vidi\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}