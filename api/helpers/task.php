<?php
//error_reporting(E_ALL);



require("helpers/server_response.php");

$request = new ClientRequest();
$dataSource = new DataSource("data.json");
$response = new ServerResponse($request, $dataSource);



$response->process();



function GET(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);


    $listid = $request->get['listid'] ?? false;
    $taskid = $request->get['taskid'] ?? 'task1';

    $output = array(
        "listid" => $listid,
        "taskid" => $taskid,
        "task" => ""
    );

    if ($listid != false) {
        $listid = $listid;
        foreach ($data as $todo) {
            if (array_key_exists("listid", $todo) && $todo["listid"] == $listid) {
                $output['task'] = $todo['tasks'][$taskid];
                break;
            }
        }
        $response->status = "OK";
    } else {
        $response->status = "ERROR - DATA NOT FOUND";
    }

    $response->outputJSON($output);
}

function PUT(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);

    $listid = $request->put['listid'] ?? false;
    $taskid = $request->put['taskid'] ?? false;
    $details = $request->put['task'] ?? "";

    $target = 0;


    foreach ($data as $key => $todo) {
        if ($todo['listid'] == $listid) {
            $target = $key;
        }
    }

    $data[$target]['tasks'][$taskid] = $details;



    $newJson = json_encode($data);

    file_put_contents($dataSource->writePath, $newJson);

    
    $output = array(
        "listid"=>$listid,
        "taskid"=>$taskid,
        "task"=>$details
    );

    $response->status = "OK";
    $response->outputJSON($output);
}
