<?php

namespace Fab\VidiFrontend\Persistence;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Persistence\Order;
use Fab\Vidi\Tca\Tca;
use Fab\VidiFrontend\Service\ArgumentService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Factory class related to Order object.
 */
class TemplateBasedContentOrderFactory implements SingletonInterface
{

    /**
     * Gets a singleton instance of this class.
     *
     * @return $this|Object
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * Returns an order object.
     *
     * @param array $settings
     * @param string $dataType
     * @return Order|object
     */
    public function getOrder(array $settings, $dataType)
    {
        $argumentService = ArgumentService::getInstance();
        $possibleDirections = [QueryInterface::ORDER_ASCENDING, QueryInterface::ORDER_DESCENDING];
        $order = [];
        if (is_array($argumentService->getArgument('orderings'))) {
            foreach ($argumentService->getArgument('orderings') as $fieldName => $direction) {
                if (Tca::table($dataType)->hasField($fieldName)
                    && in_array(strtoupper($direction), $possibleDirections, true)) {
                    $order[$fieldName] = $direction;
                }
            }
        } elseif (isset($settings['sorting']) && !empty($settings['sorting'])) {
            $direction = isset($settings['direction']) ? $settings['direction'] : 'ASC';
            $order = [$settings['sorting'] => $direction];
        } else {
            // Default ordering
            $order = Tca::table($dataType)->getDefaultOrderings();
        }

        return GeneralUtility::makeInstance(Order::class, $order);
    }

}
