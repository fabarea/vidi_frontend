<?php
namespace Fab\VidiFrontend\Configuration;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use Fab\Vidi\Tca\TcaServiceInterface;

/**
 * Tweak TCA configuration for the Frontend for File References.
 */
class TcaTweak
{

    /**
     * Tweak the TCA for sys_file_reference on the Frontend.
     * Make that sys_file_reference behaves as a MM relations between "sys_file" and "tx_domain_model_foo"
     *
     * @param string $dataType
     * @param string $serviceType
     * @return void
     */
    public function tweakFileReferences($dataType, $serviceType)
    {

        if ($serviceType === TcaServiceInterface::TYPE_TABLE && $this->isFrontendMode()) {
            foreach ($this->getFields($dataType) as $fieldName) {
                if ($this->getForeignTable($dataType, $fieldName) === 'sys_file_reference') {

                    // Adjust TCA so that sys_file_reference behaves as MM tables of type "group" on the Frontend
                    // Consequence: we'll get directly a file and not a File Reference.
                    unset(
                        $GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_field'],
                        $GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_label']
                    );
                    $GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_table'] = 'sys_file';
                    $GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['MM'] = 'sys_file_reference';
                    $GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['MM_opposite_field'] = 'items';
                    $GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['MM_match_fields'] = [
                        'tablenames' => $dataType,
                        'fieldname' => $fieldName,
                    ];

                    // Just a faked TCA to handle the opposite relation of sys_file_reference.
                    // It is required by Vidi to have relations configured both side.
                    if (empty($GLOBALS['TCA']['sys_file']['columns']['items'])) {
                        $GLOBALS['TCA']['sys_file']['columns']['items']['config'] = [
                            'allowed' => '*',
                            'internal_type' => 'db',
                            'MM' => 'sys_file_reference',
                            'type' => 'group',
                        ];
                    }
                }
            }
        }
    }

    /**
     * Returns whether the current mode is Frontend
     *
     * @param string $dataType
     * @return array
     */
    protected function getFields($dataType)
    {
        $fields = [];
        if (is_array($GLOBALS['TCA'][$dataType]) && is_array($GLOBALS['TCA'][$dataType]['columns'])) {
            $fields = array_keys($GLOBALS['TCA'][$dataType]['columns']);
        }
        return $fields;
    }

    /**
     * Returns whether the current mode is Frontend
     *
     * @param string $dataType
     * @param string $fieldName
     * @return string
     */
    protected function getForeignTable($dataType, $fieldName)
    {
        $foreignTableName = '';
        if (isset($GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_table'])) {
            $foreignTableName = $GLOBALS['TCA'][$dataType]['columns'][$fieldName]['config']['foreign_table'];
        }
        return $foreignTableName;
    }

    /**
     * Returns whether the current mode is Frontend
     */
    protected function isFrontendMode(): bool
    {
        return ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

}
