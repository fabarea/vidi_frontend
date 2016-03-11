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


/**
 * Class AbstractMassAction
 */
abstract class AbstractMassAction implements MassActionInterface
{
    /**
     * @var int
     */
    protected $currentContentElement = 0;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @return string
     */
    protected function getMassActionUrl()
    {

        return $this->getFrontendObject()->cObj->typoLink_URL([
            'parameter' => $this->getFrontendObject()->id . ',' . 1457381088,
            'additionalParams' => $this->getArgumentList()
        ]);
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function with(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return string
     */
    protected function getArgumentList()
    {
        $arguments = [
            'action' => 'execute',
            'contentData' => $this->currentContentElement,
            'actionName' => $this->getName(),
        ];

        $argumentList = '';
        foreach ($arguments as $key => $value) {
            $argumentList .= sprintf('&tx_vidifrontend_pi1[%s]=%s', $key, $value) . $argumentList;
        }

        return $argumentList;
    }

    /**
     * @param int $currentContentElement
     * @return $this
     */
    public function setCurrentContentElement($currentContentElement)
    {
        $this->currentContentElement = $currentContentElement;
        return $this;
    }

    /**
     * Returns an instance of the Frontend object.
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendObject()
    {
        return $GLOBALS['TSFE'];
    }

}
