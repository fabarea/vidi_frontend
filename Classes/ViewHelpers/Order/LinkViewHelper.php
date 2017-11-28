<?php
namespace Fab\VidiFrontend\ViewHelpers\Order;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Service\ArgumentService;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class LinkViewHelper
 */
class LinkViewHelper extends AbstractViewHelper
{

    /**
     * @param string $order
     * @param string $direction
     * @return array
     */
    public function render($order, $direction)
    {
        return $this->getUriBuilder()
            ->setArguments($this->getArguments($order, $direction))
            ->build();
    }

    /**
     * @param string $order
     * @param string $direction
     * @return array
     */
    protected function getArguments($order, $direction)
    {
        if ($this->getArgument('matches')) {
            $arguments[ArgumentService::PREFIX]['matches'] = $this->getArgument('matches');
        }

        if ($this->getArgument('additionalMatches')) {
            $arguments[ArgumentService::PREFIX]['additionalMatches'] = $this->getArgument('additionalMatches');
        }

        $arguments[ArgumentService::PREFIX]['orderings'] = [$order => $direction];
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
