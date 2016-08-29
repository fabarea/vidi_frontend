<?php
namespace Fab\VidiFrontend\MassAction;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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
