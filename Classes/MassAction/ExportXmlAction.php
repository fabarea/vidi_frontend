<?php
namespace Fab\VidiFrontend\MassAction;

/**
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Action for exporting data to XML format.
 */
class ExportXmlAction extends AbstractMassAction
{

    use TemporaryFileTrait;

    /**
     * @var string
     */
    protected $name = 'export_xml';

    /**
     * Renders an "xml export" item to be placed in the menu.
     * Only the admin is allowed to export for now as security is not handled.
     *
     * @return string
     */
    public function render()
    {
        $result = sprintf('<li><a href="%s" class="btn-export export-xml" data-format="xml"><i class="fa fa-file-code-o"></i> %s</a></li>',
            $this->getMassActionUrl(),
            LocalizationUtility::translate('export.xml', 'vidi_frontend')
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
            $this->exportFileNameAndPath .= '.xml'; // add extension to the file.

            // Write the exported data to a XML file.
            $this->writeXmlFile($objects, $contentService->getDataType());

            $result->setHeaders($this->getHeaders());
            $result->setFile($this->exportFileNameAndPath);
            $result->setCleanUpTask(function() {
                GeneralUtility::rmdir($this->temporaryDirectory, true);
            });
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . basename($this->exportFileNameAndPath) . '"',
            'Content-Length' => filesize($this->exportFileNameAndPath),
            'Content-Description' => 'File Transfer',
        ];
    }

    /**
     * Write the XML file to a temporary location.
     *
     * @param Content[] $objects
     * @param string $dataType
     */
    protected function writeXmlFile(array $objects, $dataType)
    {
        $columns = ColumnsConfiguration::getInstance()->get($dataType, $this->settings['columns']);

        $items = [];
        foreach ($objects as $object) {

            $item = [];
            foreach ($columns as $fieldNameAndPath => $configuration) {

                if (Tca::table($dataType)->hasField($fieldNameAndPath)) {
                    $item[$fieldNameAndPath] = $object[$fieldNameAndPath];
                } elseif ($this->getFieldPathResolver()->containsPath($fieldNameAndPath, $dataType)) {
                    // The field name may contain a path, e.g. metadata.title and must be resolved.
                    $fieldParts = GeneralUtility::trimExplode('.', $fieldNameAndPath, true);
                    $item[$fieldNameAndPath] = (string)$object[$fieldParts[0]][$fieldParts[1]];
                }
            }
            $items[] = $item;
        }

        $xml = new \SimpleXMLElement('<items/>');
        $xml = $this->arrayToXml($items, $xml);
        file_put_contents($this->exportFileNameAndPath, $this->formatXml($xml->asXML()));
    }

    /**
     * Convert an array to xml
     *
     * @return \SimpleXMLElement
     */
    protected function arrayToXml($array, \SimpleXMLElement $xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $key = is_numeric($key) ? 'item' : $key;
                $subNode = $xml->addChild($key);
                $this->arrayToXml($value, $subNode);
            } else {
                $key = is_numeric($key) ? 'item' : $key;
                $xml->addChild($key, "$value");
            }
        }
        return $xml;
    }

    /**
     * Format the XML so that is looks human friendly.
     *
     * @param string $xml
     * @return string
     */
    protected function formatXml($xml)
    {
        $dom = new \DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);
        return $dom->saveXML();
    }

    /**
     * @return object|FieldPathResolver
     * @throws \InvalidArgumentException
     */
    protected function getFieldPathResolver()
    {
        return GeneralUtility::makeInstance(FieldPathResolver::class);
    }

}
