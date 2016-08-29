<?php
namespace Fab\VidiFrontend\MassAction;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */


/**
 * Interface ResultActionInterface
 */
interface ResultActionInterface
{

    /**
     * @return string
     */
    public function getOutput();

    /**
     * @param string $output
     * @return $this
     */
    public function setOutput($output);

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers);

}
