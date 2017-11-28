<?php
namespace Fab\VidiFrontend\ViewHelpers\Object;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Domain\Model\Content;

/**
 * Class ShowViewHelper
 */
class ShowViewHelper extends AbstractViewHelper
{


    /**
     * Display the object
     *
     * @return string
     */
    public function render()
    {
        /** @var Content $object */
        $object = $this->templateVariableContainer->get('object');

        $output = [];
        foreach ($object->toArray() as $fieldName => $value) {
            $output[] = sprintf(
                '<tr><td>%s</td><td>%s</td></tr>',
                $fieldName,
                $value
            );
        }

        return '<table class="table table-striped table-hover">' . implode("\n", $output) . '</table>';
    }

}
