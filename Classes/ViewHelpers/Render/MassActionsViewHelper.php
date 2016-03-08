<?php
namespace Fab\VidiFrontend\ViewHelpers\Render;

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

use Fab\VidiFrontend\MassAction\MassActionInterface;
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Module\ModuleLoader;

/**
 * View helper for rendering the mass-action components.
 */
class MassActionsViewHelper extends AbstractViewHelper
{

    /**
     * Renders the mass-action components
     *
     * @return string
     */
    public function render()
    {

        $dataType = $this->templateVariableContainer->get('dataType');
        $settings = $this->templateVariableContainer->get('settings');
        $contentElementIdentifier = $this->templateVariableContainer->get('contentElementIdentifier');

        $actionNames = GeneralUtility::trimExplode(',', $settings['actions']);
        $actions = FrontendTca::grid($dataType)->getMassActions();

        $lines = [];
        foreach ($actionNames as $actionName) {

            /** @var MassActionInterface $action */
            $action = $actions[$actionName];
            $lines[] = $action->setCurrentContentElement($contentElementIdentifier)->render();
        }

        return implode("\n", $lines);
    }
}
