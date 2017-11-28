<?php
namespace Fab\VidiFrontend\ViewHelpers\Pager;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Persistence\Pager;
use Fab\VidiFrontend\Service\ArgumentService;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class LinkViewHelper
 */
class LinkViewHelper extends AbstractViewHelper
{

    /**
     * @param mixed $pageIndex
     * @param mixed $pageOffset
     * @return array
     */
    public function render($pageIndex = null, $pageOffset = null)
    {
        /** @var Pager $pager */
        if ($pageIndex === null) {
            $pager = $this->templateVariableContainer->get('pager');
            $pageIndex = $pager->getPage();
        }

        if ($pageOffset === 'previous') {
            $pageOffset = -1;
        }

        if ($pageOffset === 'next') {
            $pageOffset = 1;
        }

        if ($pageOffset !== null) {
            $pageIndex += $pageOffset;
        }

        return $this->getUriBuilder()
            ->setArguments($this->getArguments($pageIndex))
            ->build();
    }

    /**
     * @param int $pageIndex
     * @return array
     */
    protected function getArguments($pageIndex)
    {
        if ($this->getArgument('matches')) {
            $arguments[ArgumentService::PREFIX]['matches'] = $this->getArgument('matches');
        }

        if ($this->getArgument('additionalMatches')) {
            $arguments[ArgumentService::PREFIX]['additionalMatches'] = $this->getArgument('additionalMatches');
        }

        if ($this->getArgument('orderings')) {
            $arguments[ArgumentService::PREFIX]['orderings'] = $this->getArgument('orderings');
        }

        $arguments[ArgumentService::PREFIX]['page'] = $pageIndex;
        return $arguments;
    }

    /**
     * @param string $matchName
     * @return array
     */
    protected function getArgument($matchName)
    {
        $arguments = $this->templateVariableContainer->get('arguments');

        return is_array($arguments[$matchName])
            ? array_filter($arguments[$matchName])
            : [];
    }

    /**
     * @return UriBuilder|object
     */
    protected function getUriBuilder()
    {
        return $this->objectManager->get(UriBuilder::class);
    }

}
