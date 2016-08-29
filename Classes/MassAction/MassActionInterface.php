<?php
namespace Fab\VidiFrontend\MassAction;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Service\ContentService;


/**
 * Interface MassActionInterface
 */
interface MassActionInterface
{

    /**
     * Renders a mass action item in a drop down menu below the grid.
     *
     * @return string
     */
    public function render();

    /**
     * Execute the action
     *
     * @param ContentService $contentService
     * @return ResultActionInterface
     */
    public function execute(ContentService $contentService);

    /**
     * @param array $settings
     * @return $this
     */
    public function with(array $settings);

    /**
     * Return the name of this action.
     *
     * @return string
     */
    public function getName();

    /**
     * @param int $currentContentElement
     * @return $this
     */
    public function setCurrentContentElement($currentContentElement);
}
