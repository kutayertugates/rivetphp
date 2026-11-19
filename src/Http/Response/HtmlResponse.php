<?php

namespace Rivetphp\Http\Response;

/**
 * Sends HTML responses
 *
 * Renders a PHP template file with the given data and sends the output
 *
 * @package Rivetphp\Http\Response
 * @since v0.1.0
 */
class HtmlResponse extends BaseResponse
{
    public string $file_path;
    public array $data;

    /**
     * Prepares an HTML response for a template file
     *
     * @param string $_file_path Path of the template file
     * @param array $_data Data extracted into the template
     * @param int $_status_code HTTP status code
     * @since v0.1.0
     */
    public function __construct(
        $_file_path,
        $_data = [],
        int $_status_code = 200,
    ) {
        $this->file_path = ltrim($_file_path, "./");
        $this->data = $_data;
        $this->status_code = $_status_code;
        $this->addHeader("Content-Type", "text/html");
    }

    /**
     * Converts the template path to an absolute path
     *
     * @param string $_template_dir Directory to prepend to the file path
     * @return void
     * @since v0.1.0
     */
    public function update_relpath_to_abspath($_template_dir)
    {
        $this->file_path = $_template_dir . $this->file_path;
    }

    /**
     * Renders the template and sends the resulting HTML
     *
     * @return void
     * @since v0.1.0
     */
    public function apply()
    {
        http_response_code($this->status_code);

        foreach ($this->headers as $hname => $hvalue) {
            header($hname . ": " . $hvalue);
        }

        extract($this->data);
        ob_start();
        require $this->file_path;
        $html = ob_get_clean();

        echo $html;
    }
}
