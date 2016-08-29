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
 * View which renders a "divider" menu item to be placed in the grid menu.
 */
class DividerMenuItem extends AbstractMassAction
{

    /**
     * @var string
     */
    protected $name = 'divider';

    /**
     * Renders a "divider" menu item to be placed in the grid menu.
     *
     * @return string
     */
    public function render()
    {
        return '<li role="separator" class="divider"></li>';
    }

    /**
     * Return the name of this action..
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Execute the action.
     *
     * @param ContentService $contentService
     * @return ResultActionInterface
     */
    public function execute(ContentService $contentService)
    {
        return new JsonResultAction();
    }

}
