<?php
namespace Fab\VidiFrontend\Persistence;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
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
     * @var array
     */
    protected $settings = array();

    /**
     * Constructor
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Gets a singleton instance of this class.
     *
     * @param array $settings
     * @return OrderFactory
     */
    static public function getInstance(array $settings)
    {
        return GeneralUtility::makeInstance(OrderFactory::class, $settings);
    }

    /**
     * Returns an order object.
     *
     * @param string $dataType
     * @return Order
     */
    public function getOrder($dataType)
    {
        // Default ordering
        $order = Tca::table($dataType)->getDefaultOrderings();

        // Retrieve a possible id of the column from the request
        $orderings = GeneralUtility::_GP('order');

        if (is_array($orderings) && isset($orderings[0])) {
            $columnPosition = $orderings[0]['column'];
            $direction = $orderings[0]['dir'];

            if ($columnPosition > 0) {
                $columns = GeneralUtility::trimExplode(',', $this->settings['columns'], TRUE);
                $field = $columns[$columnPosition];

                $order = array(
                    $field => strtoupper($direction)
                );
            }
        }

        return GeneralUtility::makeInstance(Order::class, $order);
    }

}
