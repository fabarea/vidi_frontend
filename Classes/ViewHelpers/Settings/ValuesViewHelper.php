<?php

namespace Fab\VidiFrontend\ViewHelpers\Settings;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ValuesViewHelper
 */
class ValuesViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('key', 'string', 'Settings key', true);
        $this->registerArgument('index', 'int', 'Settings index', false);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        /** @var array $settings */
        $settings = $this->templateVariableContainer->get('settings');

        $key = $this->arguments['key'];
        $index = $this->arguments['index'];


        $result = isset($settings[$key])
            ? GeneralUtility::trimExplode(',', $settings[$key], true)
            : [];

        if ($index !== null) {
            $result = isset($result[$index])
                ? $result[$index]
                : null;
        }

        return $result;
    }

}
