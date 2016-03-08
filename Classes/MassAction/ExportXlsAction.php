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

use Fab\Vidi\Service\SpreadSheetService;
use Fab\Vidi\Tca\Tca;
use Fab\VidiFrontend\Configuration\ColumnsConfiguration;
use Fab\VidiFrontend\Resolver\FieldPathResolver;
use Fab\VidiFrontend\Service\ContentService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Action for exporting data to XLS format.
 */
class ExportXlsAction extends AbstractMassAction
{

    use TemporaryFileTrait;

    /**
     * @var string
     */
    protected $name = 'export_xls';

    /**
     * Renders a "xls export" item to be placed in the menu.
     *
     * @return string
     */
    public function render()
    {
        $result = sprintf('<li><a href="%s" class="btn-export export-xls" data-format="xls"><i class="fa fa-file-excel-o"></i> %s</a></li>',
            $this->getMassActionUrl(),
            LocalizationUtility::translate('export.xls', 'vidi_frontend')
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
     */
    public function execute(ContentService $contentService)
    {
        $result = new GenericResultAction();
        $objects = $contentService->getObjects();

        // Make sure we have something to process...
        if ((bool)$objects) {

            // Initialization step.
            $this->initializeEnvironment($objects);
            $this->exportFileNameAndPath .= '.xls'; // add extension to the file.

            // Write the exported data to a CSV file.
            $this->writeXlsFile($objects, $contentService->getDataType());

            $result->setHeaders($this->getHeaders());
            readfile($this->exportFileNameAndPath);

            $this->cleanUpTemporaryFiles();
        }

        return $result;
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
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . basename($this->exportFileNameAndPath) . '"',
            'Content-Length' => filesize($this->exportFileNameAndPath),
            'Content-Description' => 'File Transfer',
            'Content-Transfer-Encoding' => 'binary',
        ];
    }

    /**
     * Write the CSV file to a temporary location.
     *
     * @param array $objects
     * @param string $dataType
     */
    protected function writeXlsFile(array $objects, $dataType)
    {

        /** @var SpreadSheetService $spreadSheet */
        $spreadSheet = GeneralUtility::makeInstance(SpreadSheetService::class);

        // Handle object header, get the first object and get the list of fields.
        /** @var \Fab\Vidi\Domain\Model\Content $object */
        $object = reset($objects);

        $columns = ColumnsConfiguration::getInstance()->get($dataType, $this->settings['columns']);

        // Compute columns header
        $finalFields = [];
        foreach ($columns as $fieldNameAndPath => $configuration) {

            if (Tca::table($dataType)->hasField($fieldNameAndPath) || $this->getFieldPathResolver()->containsPath($fieldNameAndPath, $dataType)) {
                $finalFields[] = $fieldNameAndPath;
            }
        }

        $spreadSheet->addRow($finalFields);

        // Handle columns case
        foreach ($objects as $object) {

            // Make sure we have a flat array of values for the CSV purpose.
            $flattenValues = array();
            foreach ($columns as $fieldNameAndPath => $configuration) {

                if (Tca::table($dataType)->hasField($fieldNameAndPath)) {
                    $value = $object[$fieldNameAndPath];

                    if (is_array($value)) {
                        $flattenValues[$fieldNameAndPath] = implode(', ', $value);
                    } else {
                        $flattenValues[$fieldNameAndPath] = str_replace("\n", "\r", $value); // for Excel purpose.
                    }
                } elseif ($this->getFieldPathResolver()->containsPath($fieldNameAndPath, $dataType)) {
                    // The field name may contain a path, e.g. metadata.title and must be resolved.
                    $fieldParts = GeneralUtility::trimExplode('.', $fieldNameAndPath, true);
                    $flattenValues[$fieldNameAndPath] = (string)$object[$fieldParts[0]][$fieldParts[1]];
                }
            }

            $spreadSheet->addRow($flattenValues);
        }

        file_put_contents($this->exportFileNameAndPath, $spreadSheet->toString());
    }

    /**
     * @return FieldPathResolver
     */
    protected function getFieldPathResolver()
    {
        return GeneralUtility::makeInstance(FieldPathResolver::class);
    }

}
