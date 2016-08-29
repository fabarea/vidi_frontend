<?php
namespace Fab\VidiFrontend\Backend;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class that adds the wizard icon.
 */
class Wizard
{

    /**
     * Processing the wizard items array
     *
     * @param array $wizardItems : The wizard items
     * @return array
     * @throws \BadFunctionCallException
     */
    public function proc($wizardItems)
    {
        $wizardItems['plugins_tx_vidifrontend_pi1'] = array(
            'icon' => ExtensionManagementUtility::extRelPath('vidi_frontend') . 'Resources/Public/Images/VidiFrontend.png',
            'title' => $this->getLanguageService()->sL('LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:wizard.title'),
            'description' => $this->getLanguageService()->sL('LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:wizard.description'),
            'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=vidifrontend_pi1'
        );

        return $wizardItems;
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

}
