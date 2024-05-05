<?php

require("helpers/client_request.php");
require("helpers/data_source.php");
require("helpers/server_response.php");

class Handler{

    public ServerResponse $response;
    public ClientRequest $request;
    public DataSource $db;

    public function __construct($credentials = null)
    {
        $this->db = new DataSource($credentials);
        $this->request = new ClientRequest();
    }

    public function process(){
        $this->response = new ServerResponse($this->request->method);
        $this->response->process($this);
 
    }
}

