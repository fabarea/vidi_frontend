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

        $settings = $this->templateVariableContainer->get('settings');

        $gridConfiguration = $settings['gridConfiguration'];
        if ((bool)$settings['isVisualSearchBar']) {
            $gridIdentifier = $this->templateVariableContainer->get('gridIdentifier');

            $gridConfiguration = str_replace('visual-search-container', 'visual-search-container-' . $gridIdentifier, $gridConfiguration);
        }

        return $gridConfiguration;
    }
}
