<?php
namespace Fab\VidiFrontend\MassAction;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class GenericResultAction
 */
class GenericResultAction implements ResultActionInterface
{
    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $output = '';

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Return the HTTPS header?
     *
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param string $output
     * @return $this
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }
}
