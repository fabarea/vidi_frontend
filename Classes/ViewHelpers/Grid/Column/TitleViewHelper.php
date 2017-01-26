<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid\Column;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for rendering a column title in the grid.
 */
class TitleViewHelper extends AbstractViewHelper
{

    /**
     * Returns a column title.
     *
     * @return string
     */
    public function render()
    {
        $dataType = $this->templateVariableContainer->get('dataType');
        $columnName = $this->templateVariableContainer->get('columnName');

        return FrontendTca::grid($dataType)->getLabel($columnName);
    }

}
