<?php
namespace Fab\VidiFrontend\Form\Elements;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Tca\Tca;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EnableFieldsElement extends AbstractElement
{
    public function render()
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        $result = $this->initializeResultArray();
        $flexform = $this->data['databaseRow']['pi_flexform'];
        $dataType = $this->getDataTypeFromFlexform($flexform);

        if (empty($dataType)) {
            $output = 'No enable fields to display yet! Select a content type first.';
        } else {

            $enableFieldsValues = GeneralUtility::trimExplode(',', $this->getSettings($flexform, 'enableFields'), true);

            $options = [];

            $enableFields = [
                'startTime' => ['label' => 'Start time', 'getter' => 'getStartTimeField'],
                'endTime' => ['label' => 'End time', 'getter' => 'getEndTimeField'],
                'disabled' => ['label' => 'Hidden', 'getter' => 'getHiddenField'],
            ];

            foreach ($enableFields as $value => $field) {

                $getter = $field['getter'];
                if (Tca::table($dataType)->$getter()) {
                    $isChecked = in_array($value, $enableFieldsValues, true) ? '' : 'checked="checked"';
                    $options[] = sprintf(
                        '<li><label><input type="checkbox" class="checkbox-enableField" value="%s" %s /> %s</label></li>',
                        strtolower($value),
                        $isChecked,
                        strtolower($field['label'])
                    );
                }
            }

            $output = sprintf(
                '
    <input name="data[tt_content][%s][pi_flexform][data][grid][lDEF][settings.enableFields][vDEF]" type="hidden" id="enableFields" value="%s">
    <ul class="list-unstyled" style="margin-top: 10px;">%s</ul>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <script>
                    (function() {
                        // todo replace me to avoid the quick and dirty fix of importing jquery
                    })();

                    (function($) {
                        $(function() {
                            $(".checkbox-enableField").change(function() {

                                var enableFields = $("#enableFields").val();
                                var currentValue = $(this).val();
                                
                                // In any case remove item
                                var expression = new RegExp(\', *\' + currentValue, \'i\');
                                enableFields = enableFields.replace(expression, \'\');
                                $("#enableFields").val(enableFields);

                                // Append new data type at the end if checked.
                                if (!$(this).is(":checked")) {
                                    $("#enableFields").val(enableFields + \', \' + currentValue);
                                }
                            });
                        });
                    })(jQuery);
                                
                </script>',
                $this->data['databaseRow']['uid'],
                $this->getSettings($flexform, 'enableFields'),
                implode("\n", $options)
            );
        }

        $result['html'] = $output;
        return $result;
    }

}
