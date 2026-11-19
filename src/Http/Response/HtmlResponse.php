<?php

namespace Rivetphp\Http\Response;

class HtmlResponse extends BaseResponse
{
    public string $file_path;
    public array $data;

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

    public function update_relpath_to_abspath($_template_dir)
    {
        $this->file_path = $_template_dir . $this->file_path;
    }

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
