<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use Fab\VidiFrontend\Configuration\ContentElementConfiguration;
use Fab\VidiFrontend\View\Grid\Row;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for rendering multiple rows.
 */
class RowsViewHelper extends AbstractViewHelper
{

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('objects', 'array', '', false, []);
    }

    /**
     * Returns rows of content as array.
     *
     * @return array
     */
    public function render($objects = [])
    {
        $objects = $this->arguments['objects'] ?? $objects;
        $rows = [];
        $columns = ContentElementConfiguration::getInstance()->getColumns();
        /** @var Row $row */
        $row = GeneralUtility::makeInstance(Row::class, $columns);
        foreach ($objects as $index => $object) {
            $rows[] = $row->render($object, $index);
        }
        return $rows;
    }

}
