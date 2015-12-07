<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid;

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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns the configuration for the length menu in the Grid.
 */
class LengthMenuViewHelper extends AbstractViewHelper
{

    /**
     * Returns the configuration for the length menu in the Grid.
     *
     * @return boolean
     */
    public function render()
    {
        $settings = $this->templateVariableContainer->get('settings');

        $configuration = '';
        if (!empty($settings['defaultNumberOfItems'])) {
            $values = trim($settings['defaultNumberOfItems'], ',');

            $label = sprintf("'%s'", LocalizationUtility::translate('grid.lengthMenu.all', 'vidi_frontend'));
            $labels = str_replace('-1', $label, $values);
            $configuration = sprintf('[[%s], [%s]]', $values, $labels);
        }

        return $configuration;
    }

}
