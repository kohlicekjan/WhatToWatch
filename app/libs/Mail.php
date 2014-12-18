<?php

class Mail {

    private $to = [];
    private $attachments = [];
    public $subjects;
    public $from;
    public $fromName;
    public $message;
    private $boundaries = [];

    public function __construct() {
        $this->boundaries[] = md5(rand());
        $this->boundaries[] = md5(rand());
    }

    public function send() {

        $header = $this->headerMain();
        $header.= $this->headerMessage();
        $header.= $this->headerAttachment();

        $header = "From: ". $this->from . "\r\n"
            . "MIME-Version: 1.0 \r\n"
            . "Content-Transfer-Encoding: 8bit\r\n"
            . "Content-Type: text/plain; charset=utf-8\n";

    return @mail('kohlicekjan@gmail.com', $this->subjects, $this->message, $header);
        
        //return mail('', '', '', $header);
    }

    public function addTo($to, $name = null) {
        $this->to[] = (empty($name) ? $to : $name . ' <' . $to . '>');
    }

    public function addAttachment($path, $name = null) {
        $this->attachments[] = ['path' => $path, 'name' => $name];
    }

    private function headerMain() {

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'From: ' . (empty($this->fromName) ? $this->from : ($this->fromName . ' <' . $this->from . '>'));
        $headers[] = 'To: ' . implode(', ', $this->to);
        $headers[] = 'Subject: ' . $this->subjects;


        $headers[] = 'Content-Type: multipart/mixed; boundary=' . $this->boundaries[0];
        $headers[] = '';


        $headers[] = '--' . $this->boundaries[0];
        $headers[] = 'Content-Type: multipart/alternative; boundary=' . $this->boundaries[1];

        return implode("\r\n", $headers) . "\r\n";
    }

    private function headerMessage() {
        if (!empty($this->message)) {
            $headers[] = '';
            $headers[] = '--' . $this->boundaries[1];
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
            $headers[] = 'Content-Transfer-Encoding: quoted-printable';
            $headers[] = '';
            $headers[] = $this->message;
            $headers[] = '';
            $headers[] = '--' . $this->boundaries[1] . '--';
        }
        return implode("\r\n", $headers) . "\r\n";
    }

    private function headerAttachment() {
        $headers = [];
        foreach ($this->attachments as $attachment) {

            if (file_exists($attachment['path'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $ftype = finfo_file($finfo, $attachment['path']);
                $file = fopen($attachment['path'], "r");
                $source = fread($file, filesize($attachment['path']));
                $source = chunk_split(base64_encode($source));
                fclose($file);

                $fileName = (empty($attachment['name']) ? basename($attachment['path']) : $attachment['name'] );

                $headers[] = '--' . $this->boundaries[0];
                $headers[] = 'Content-Type: ' . $ftype . '; name="' . $fileName . '"';
                $headers[] = 'Content-Disposition: attachment; filename="' . $fileName . '"';
                $headers[] = 'Content-Transfer-Encoding: base64';
                $headers[] = '';
                $headers[] = $source;
            }
        }

        if (count($headers) > 0) {
            $headers[] = '--' . $this->boundaries[0] . '--';
        }

        return implode("\r\n", $headers) . "\r\n";
    }

}
