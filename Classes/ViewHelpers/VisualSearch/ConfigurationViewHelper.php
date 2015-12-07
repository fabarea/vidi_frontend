<?php
namespace Fab\VidiFrontend\ViewHelpers\VisualSearch;

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

        /**
         * Set the defaults for DataTables initialisation
         *
         * l - Length changing
         * f - Filtering input
         * t - The table!
         * i - Information
         * p - Pagination
         * r - processing
         */
        $dom = sprintf(
            "<'row'%s<'col-xs-2 hidden-xs'l>r><'row'<'col-xs-12't>><'row'%s>",
            (bool)$settings['isVisualSearchBar'] ? $this->getTopBarWithVisualSearchEnabled() : $this->getTopBarDefault(),
            (bool)$settings['isVisualSearchBar'] ? $this->getBottomBarWithVisualSearchEnabled() : $this->getBottomBarDefault()
        );

        return $dom;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     */
    public function getTopBarWithVisualSearchEnabled()
    {

        $gridIdentifier = $this->templateVariableContainer->get('gridIdentifier');

        return sprintf("<'col-sm-10 visual-search-container-%s'>", $gridIdentifier);
    }

    /**
     * @return string
     */
    public function getBottomBarWithVisualSearchEnabled()
    {
        return "<'col-sm-4'i><'col-sm-4'f><'col-sm-4'p>";
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     */
    public function getTopBarDefault()
    {
        return "<'col-xs-10'f>";
    }

    /**
     * @return string
     */
    public function getBottomBarDefault()
    {
        return "<'col-xs-6'i><'col-xs-6'p>";
    }
}
