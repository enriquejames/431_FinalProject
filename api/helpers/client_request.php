<?php

class ClientRequest
{
    public string $uri;
    public $method;
    public $files;
    public $post;
    public $get;
    public $input;
    private $rawInput;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->files = $_FILES;
        $this->post = $_POST;
        $this->get = $_GET;
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->rawInput = file_get_contents("php://input"); //JSON
        $this->input = json_decode($this->rawInput, true) ?? []; //formData js object convert php associated array/ object ? 
    }
}

// Client request only convert front end JS user form data to PHP associated object 