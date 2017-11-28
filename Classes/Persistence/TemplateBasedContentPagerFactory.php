<?php

namespace Fab\VidiFrontend\Persistence;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Persistence\Pager;
use Fab\VidiFrontend\Service\ArgumentService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TemplateBasedContentPagerFactory
 */
class TemplateBasedContentPagerFactory implements SingletonInterface
{

    /**
     * Gets a singleton instance of this class.
     *
     * @return $this|Object
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * @param array $settings
     * @return Pager
     */
    public function getPager(array $settings)
    {
        /** @var $pager Pager */
        $pager = GeneralUtility::makeInstance(Pager::class);

        $argumentService = ArgumentService::getInstance();

        $pager->setLimit(
            $argumentService->getArgument('limit') === null
                ? (int)$settings['limit']
                : (int)$argumentService->getArgument('limit')
        );

        $pager->setPage(
            $argumentService->getArgument('page') === null
                ? 0
                : (int)$argumentService->getArgument('page')
        );

        $pager->setOffset($pager->getLimit() * $pager->getPage());

        return $pager;
    }
}
