<?php

namespace Fab\VidiFrontend\ViewHelpers\Pager;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Persistence\Pager;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class PagesViewHelper
 */
class PagesViewHelper extends AbstractViewHelper
{

    /**
     * @var int
     */
    protected $maxNumberOfPages = 5;

    /**
     * @var int
     */
    protected $shiftPage = 2;

    /**
     * @return array
     */
    public function render()
    {
        /** @var Pager $pager */
        $pager = $this->templateVariableContainer->get('pager');

        $maxNumberOfPages = ceil($pager->getCount() / $pager->getLimit());

        $pages = [];

        $start = $pager->getPage() + 1 >= $this->maxNumberOfPages
            ? $pager->getPage() + 1 - $this->maxNumberOfPages + 1
            : 0;

        $end = $pager->getPage() + $this->maxNumberOfPages <= $maxNumberOfPages
            ? $start + $this->maxNumberOfPages
            : $maxNumberOfPages;


        if ($maxNumberOfPages > $end + $this->shiftPage) {
            $end += $this->shiftPage;
        } elseif ($end - $start < $this->maxNumberOfPages + $this->shiftPage && $start - ($this->maxNumberOfPages + $this->shiftPage - ($end - $start)) > 0) {
            $start -= $this->maxNumberOfPages + $this->shiftPage - ($end - $start);
        }

        for (; $start < $end; $start++) {
            $pages[$start] = $start + 1;
        }

        return $pages;
    }

}
