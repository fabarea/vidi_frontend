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

use Fab\Vidi\Tca\Tca;
use Fab\VidiFrontend\Configuration\ColumnsConfiguration;
use Fab\VidiFrontend\Resolver\FieldPathResolver;
use Fab\VidiFrontend\Service\ContentService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Action for exporting data to CSV format.
 */
class ExportCsvAction extends AbstractMassAction
{

    use TemporaryFileTrait;

    /**
     * @var string
     */
    protected $name = 'export_csv';

    /**
     * Renders a "csv export" item to be placed in the menu.
     * Only the admin is allowed to export for now as security is not handled.
     *
     * @return string
     */
    public function render()
    {
        $result = sprintf('<li><a href="%s" class="btn-export export-csv" data-format="csv"><i class="fa fa-file-text-o"></i> %s</a></li>',
            $this->getMassActionUrl(),
            LocalizationUtility::translate('export.csv', 'vidi_frontend')
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
            $this->exportFileNameAndPath .= '.csv'; // add extension to the file.

            // Write the exported data to a CSV file.
            $this->writeCsvFile($objects, $contentService->getDataType());

            $result->setHeaders($this->getHeaders());
            readfile($this->exportFileNameAndPath);

            $this->cleanUpTemporaryFiles();
        }

        return $result;
    }

    /**
     * Write the CSV file to a temporary location.
     *
     * @param array $objects
     * @param string $dataType
     */
    protected function writeCsvFile(array $objects, $dataType)
    {

        // Create a file pointer
        $output = fopen($this->exportFileNameAndPath, 'w');

        $columns = ColumnsConfiguration::getInstance()->get($dataType, $this->settings['columns']);

        // Compute columns header
        $finalFields = [];
        foreach ($columns as $fieldNameAndPath => $configuration) {

            if (Tca::table($dataType)->hasField($fieldNameAndPath) || $this->getFieldPathResolver()->containsPath($fieldNameAndPath, $dataType)) {
                $finalFields[] = $fieldNameAndPath;
            }
        }

        fputcsv($output, $finalFields);

        foreach ($objects as $object) {

            // Make sure we have a flat array of values for the CSV purpose.
            $flattenValues = [];
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

            fputcsv($output, $flattenValues);
        }

        // close file handler
        fclose($output);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-Type' => 'application/csv',
            'Content-Disposition' => 'attachment; filename="' . basename($this->exportFileNameAndPath) . '"',
            'Content-Length' => filesize($this->exportFileNameAndPath),
            'Content-Description' => 'File Transfer',
        ];
    }

    /**
     * @return FieldPathResolver
     */
    protected function getFieldPathResolver()
    {
        return GeneralUtility::makeInstance(FieldPathResolver::class);
    }

}
