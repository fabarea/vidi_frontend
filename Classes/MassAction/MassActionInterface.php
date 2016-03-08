<?php
namespace Fab\VidiFrontend\MassAction;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
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
