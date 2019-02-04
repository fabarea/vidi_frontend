<?php

namespace Fab\VidiFrontend\ViewHelpers\Column;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
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
        $this->registerArgument('dataType', 'string', 'Data type', false);
    }

    /**
     * @return string
     */
    public function render()
    {
        $column = $this->templateVariableContainer->exists('column')
            ? $this->templateVariableContainer->get('column')
            : $this->arguments['column'];


        if (isset($this->arguments['dataType'])) {
            $dataType = $this->arguments['dataType'];
        } else {
            /** @var Content $object */
            $object = $this->templateVariableContainer->get('object'); // must exists
            $dataType = $object->getDataType();
        }

        return FrontendTca::grid($dataType)->getLabel($column);
    }

}
