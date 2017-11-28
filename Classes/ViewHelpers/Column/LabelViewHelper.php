<?php

namespace Fab\VidiFrontend\ViewHelpers\Column;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Domain\Model\Content;

/**
 * Class LabelViewHelper
 */
class LabelViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('column', 'string', 'Column name', false);
    }

    /**
     * @return string
     */
    public function render()
    {
        /** @var Content $object */
        $object = $this->templateVariableContainer->get('object');

        $column = $this->templateVariableContainer->exists('column')
            ? $this->templateVariableContainer->get('column')
            : $this->arguments['column'];

        return FrontendTca::grid($object)->getLabel($column);
    }

}
