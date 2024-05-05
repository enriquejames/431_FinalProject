<?php

class ServerResponse
{
    //public $status = "DEFAULT";

    public $method = null;

    public function __construct(string $method)
    {
        $this->method = $method;
        //$this->process();
    }

    // Set the HTTP Response Code (200 = OKAY, anything above is an error)
    public function HttpResponseCode(int $number, string $message, string $detail = "")
    {
        Header($_SERVER["SERVER_PROTOCOL"] . " " . $message, true, $number);
        exit($detail);
    }

    // Output JSON 
    public function json($data)
    {
        Header("Content-Type: application/json; charset=utf-8");
        exit(json_encode($data));
    }
    // Output Text or HTML
    public function text(string $data)
    {
        Header("Content-Type: text/html; charset=utf-8");
        exit($data);
    }

    // Executes the PHP function whose name matches the HTTP method from the request
    public function process($dependancy)
    {
        if (!function_exists($this->method)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 405 Method Not Allowed", true, 405);
            exit;
        }

        // Because "method" is a class property, we can't execute it like a function. 
        // Instead we pass it as an argument so we can execute it like a function in the "exec" function. 
        $this->exec($this->method, $dependancy);
    }

    // Special work-around to allow us to properly execute the matching function
    private function exec($method, $dependancy)
    {
        $method($dependancy);
    }
}



//server_reponse.php   only response to convert PHP object to different data type   JSON or TEXT 