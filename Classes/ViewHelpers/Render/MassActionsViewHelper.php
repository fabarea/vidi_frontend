<?php
namespace Fab\VidiFrontend\ViewHelpers\Render;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\MassAction\MassActionInterface;
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Module\ModuleLoader;

/**
 * View helper for rendering the mass-action components.
 */
class MassActionsViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

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
