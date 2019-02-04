<?php

namespace Fab\VidiFrontend\ViewHelpers\Pager;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Persistence\Pager;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class HasNextViewHelper
 */
class HasNextViewHelper extends AbstractViewHelper
{

    /**
     * @return bool
     */
    public function render()
    {
        /** @var Pager $pager */
        $pager = $this->templateVariableContainer->get('pager');
        return $pager->getCount() > $pager->getOffset() + $pager->getLimit();
    }

}
