<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which tells whether the click on row should be activated.
 */
class HasClickOnRowViewHelper extends AbstractViewHelper
{

    /**
     * Returns whether the click on row should be activated.
     *
     * @return string
     */
    public function render()
    {
        $settings = $this->templateVariableContainer->get('settings');
        return (bool)$settings['hasClickOnRow'] && !empty($settings['templateDetail']);
    }

}
