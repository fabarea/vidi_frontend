<?php
namespace Fab\VidiFrontend\MassAction;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Domain\Model\Content;
use Fab\VidiFrontend\Resolver\FieldPathResolver;
use Fab\Vidi\Tca\Tca;
use Fab\VidiFrontend\Configuration\ColumnsConfiguration;
use Fab\VidiFrontend\Service\ContentService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Action for exporting download files.
 */
class DownloadAction extends AbstractMassAction
{

    use TemporaryFileTrait;

    /**
     * @var string
     */
    protected $name = 'export_download';

    /**
     * Renders an "xml export" item to be placed in the menu.
     * Only the admin is allowed to export for now as security is not handled.
     *
     * @return string
     */
    public function render()
    {
        $result = sprintf('<li><a href="%s" class="btn-export export-download" data-format="xml"><i class="fa fa-file-zip-o"></i> %s</a></li>',
            $this->getMassActionUrl(),
            LocalizationUtility::translate('export.download', 'vidi_frontend')
        );
        return $result;
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
     * @throws \TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function execute(ContentService $contentService)
    {
        $result = new GenericResultAction();
        $objects = $contentService->getObjects();

        // Make sure we have something to process...
        if ($objects) {
            $this->initializeEnvironment($objects);

            $collectedFiles = [];
            foreach ($objects as $object) {
                $file = ResourceFactory::getInstance()->getFileObject($object->getUid(), $object->toArray());
                $collectedFiles[$file->getUid()] = $file;
            }

            $this->writeZipFile($collectedFiles);

            // Prepare output.
            $result->setFile($this->zipFileNameAndPath);
            $result->setCleanUpTask(function() {
                GeneralUtility::rmdir($this->temporaryDirectory, true);
            });

            $result->setHeaders($this->getHeaders());
        }

        return $result;
    }

    /**
     * Write the zip file to a temporary location.
     *
     * @param array $collectedFiles
     * @throws \RuntimeException
     */
    protected function writeZipFile(array $collectedFiles)
    {

        $zip = new \ZipArchive();
        $zip->open($this->zipFileNameAndPath, \ZipArchive::CREATE);

        // Add the CSV content into the zipball.
        $zip->addFile($this->exportFileNameAndPath, basename($this->exportFileNameAndPath));

        // Add the files into the zipball.
        foreach ($collectedFiles as $file) {
            /** @var File $file */
            $zip->addFile($file->getForLocalProcessing(false), $file->getIdentifier());
        }

        $zip->close();
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Pragma' => 'public',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . basename($this->zipFileNameAndPath) . '"',
            'Content-Length' => filesize($this->zipFileNameAndPath),
            'Content-Description' => 'File Transfer',
            'Content-Transfer-Encoding' => 'binary',
        ];
    }

}
