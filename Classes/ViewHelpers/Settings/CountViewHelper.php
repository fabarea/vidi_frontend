<?php

namespace Fab\VidiFrontend\ViewHelpers\Settings;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CountViewHelper
 */
class CountViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('key', 'string', 'Settings key', true);
    }

    /**
     * @return int
     */
    public function render()
    {
        /** @var array $settings */
        $settings = $this->templateVariableContainer->get('settings');

        $key = $this->arguments['key'];

        $result = isset($settings[$key])
            ? GeneralUtility::trimExplode(',', $settings[$key], true)
            : [];

        return count($result);
    }

}
