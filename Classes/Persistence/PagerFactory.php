<?php
namespace Fab\VidiFrontend\Persistence;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Persistence\Pager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Factory class related to Pager object.
 */
class PagerFactory implements SingletonInterface
{

    /**
     * Gets a singleton instance of this class.
     *
     * @return PagerFactory
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(PagerFactory::class);
    }

    /**
     * Returns a pager object.
     *
     * @return Pager
     */
    public function getPager()
    {

        /** @var $pager Pager */
        $pager = GeneralUtility::makeInstance(Pager::class);

        // Set items per page
        if (GeneralUtility::_GET('length') !== NULL) {
            $limit = (int)GeneralUtility::_GET('length');
            $pager->setLimit($limit);
        }

        // Set offset
        $offset = 0;
        if (GeneralUtility::_GET('start') !== NULL) {
            $offset = (int)GeneralUtility::_GET('start');
        }
        $pager->setOffset($offset);

        // set page
        $page = 1;
        if ($pager->getLimit() > 0) {
            $page = round($pager->getOffset() / $pager->getLimit());
        }
        $pager->setPage($page);

        return $pager;
    }

}
