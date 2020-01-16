<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid\Column;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Return a possible column header.
 */
class HeaderViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'The column name', true);
    }

    /**
     * Returns a column header.
     *
     * @return string
     */
    public function render(): string
    {
        $dataType = $this->templateVariableContainer->get('dataType');
        return FrontendTca::grid($dataType)->getHeader($this->arguments['name']);
    }

}
