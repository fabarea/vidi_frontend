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
 * Return a possible column header.
 */
class HeaderViewHelper extends AbstractViewHelper
{

    /**
     * Returns a column header.
     *
     * @param string $name the column Name
     * @return boolean
     */
    public function render($name)
    {
        $dataType = $this->templateVariableContainer->get('dataType');
        return FrontendTca::grid($dataType)->getHeader($name);
    }

}
