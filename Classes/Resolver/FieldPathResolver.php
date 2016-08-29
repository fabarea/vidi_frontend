<?php
namespace Fab\VidiFrontend\Resolver;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use Fab\Vidi\Tca\Tca;

/**
 * Class for retrieving value from a field name and path.
 */
class FieldPathResolver implements SingletonInterface
{

    /**
     * Tell whether the field name contains a path, e.g. metadata.title
     * But resolves the case when the field is composite e.g "items.sys_file_metadata" and looks as field path but is not!
     * A composite field = a field for a MM relation  of type "group" where the table name is appended.
     *
     * @param string $fieldNameAndPath
     * @param string $dataType
     * @return boolean
     */
    public function containsPath($fieldNameAndPath, $dataType)
    {
        $doesContainPath = strpos($fieldNameAndPath, '.') > 0 && !Tca::table($dataType)->hasField($fieldNameAndPath); // -> will make sure it is not a composite field name.
        return $doesContainPath;
    }

}
