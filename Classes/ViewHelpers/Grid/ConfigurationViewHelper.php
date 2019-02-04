<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which render the configuration for the Visual Search bar.
 */
class ConfigurationViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

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
