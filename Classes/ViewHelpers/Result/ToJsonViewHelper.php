<?php

namespace Fab\VidiFrontend\ViewHelpers\Result;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\ViewHelpers\Grid\RowsViewHelper;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for rendering a JSON response.
 */
class ToJsonViewHelper extends AbstractViewHelper
{

    /**
     * Render a Json response
     *
     * @return string
     */
    public function render()
    {
        $objects = $this->templateVariableContainer->get('objects');
        $output = array(
            'sEcho' => $this->getNextTransactionId(),
            'iTotalRecords' => $this->templateVariableContainer->get('numberOfObjects'),
            'iTotalDisplayRecords' => $this->templateVariableContainer->get('numberOfObjects'),
            'iNumberOfRecords' => count($objects),
            'aaData' => $this->getRowsViewHelper()->render($objects),
        );

        $this->setHttpHeaders();
        return json_encode($output);
    }

    /**
     * @return int
     */
    protected function getNextTransactionId()
    {
        $transaction = 0;
        if (GeneralUtility::_GET('sEcho')) {
            $transaction = (int)GeneralUtility::_GET('sEcho') + 1;
        }
        return $transaction;
    }

    /**
     * @return void
     */
    protected function setHttpHeaders()
    {
        /** @var Response $response */
        $response = $this->templateVariableContainer->get('response');
        $response->withHeader('Content-Type', 'application/json');
    }

    protected function getRowsViewHelper(): RowsViewHelper
    {
        return GeneralUtility::makeInstance(RowsViewHelper::class);
    }
}
