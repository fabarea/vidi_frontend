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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which render the configuration for the Visual Search bar.
 */
class ConfigurationViewHelper extends AbstractViewHelper
{

    /**
     * Returns the configuration for the Visual Search bar.
     *
     * @return string
     */
    public function render()
    {
        $gridConfiguration = $this->getGridConfiguration();

        $settings = $this->templateVariableContainer->get('settings');
        if ((bool)$settings['isVisualSearchBar']) {
            $gridIdentifier = $this->templateVariableContainer->get('gridIdentifier');

            $gridConfiguration = str_replace('visual-search-container', 'visual-search-container-' . $gridIdentifier, $gridConfiguration);
        }

        return $gridConfiguration;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     */
    public function getGridConfiguration()
    {
        $settings = $this->templateVariableContainer->get('settings');

        if (empty($settings['gridConfiguration'])) {
            $gridConfiguration = "<'row'<'col-xs-10'f><'col-xs-2 hidden-xs'l>r><'row'<'col-xs-12't>><'row'<'col-xs-6'i><'col-xs-6'p>>";
            if ((bool)$settings['isVisualSearchBar']) {
                $gridConfiguration = "<'row'<'col-sm-10 visual-search-container'><'col-xs-2 hidden-xs'l>r><'row'<'col-xs-12't>><'row'<'col-sm-4'i><'col-sm-4'f><'col-sm-4'p>>";
            }
        } else {
            $gridConfiguration = $settings['gridConfiguration'];
        }

        return $gridConfiguration;
    }
}
