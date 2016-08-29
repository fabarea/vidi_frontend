<?php
namespace Fab\VidiFrontend\Service;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Service related to the Content Element (tt_content.
 */
class ContentElementService
{

    /**
     * @var string
     */
    protected $dataType;

    /**
     * Constructor
     *
     * @param string $dataType
     * @return \Fab\VidiFrontend\Service\ContentElementService
     */
    public function __construct($dataType)
    {
        $this->dataType = $dataType;
    }

    /**
     * Return a Content Element object.
     *
     * @param array $contentData
     * @return ContentObjectRenderer
     */
    public function getContentObjectRender(array $contentData)
    {

        /** @var ContentObjectRenderer $contentObjectRenderer */
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $contentObjectRenderer->start($contentData, $this->dataType);
        return $contentObjectRenderer;
    }

}
