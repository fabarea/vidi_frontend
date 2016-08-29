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
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Factory class related to Order object.
 */
class OrderFactory implements SingletonInterface
{

    /**
     * Gets a singleton instance of this class.
     *
     * @return OrderFactory
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(OrderFactory::class);
    }

    /**
     * Returns an order object.
     *
     * @param array $settings
     * @param string $dataType
     * @return Order
     */
    public function getOrder(array $settings, $dataType)
    {

        if (isset($settings['sorting']) && !empty($settings['sorting'])) {
            $direction = isset($settings['direction']) ? $settings['direction'] : 'ASC';
            $order = [$settings['sorting'] => $direction];
        } else {
            // Default ordering
            $order = Tca::table($dataType)->getDefaultOrderings();
        }

        // Retrieve a possible id of the column from the request
        $orderings = GeneralUtility::_GP('order');

        if (is_array($orderings) && isset($orderings[0])) {
            $columnPosition = $orderings[0]['column'];
            $direction = $orderings[0]['dir'];

            if ($columnPosition > 0) {
                $columns = GeneralUtility::trimExplode(',', $settings['columns'], TRUE);
                $field = $columns[$columnPosition];

                $order = array(
                    $field => strtoupper($direction)
                );
            }
        }

        return GeneralUtility::makeInstance(Order::class, $order);
    }

}
