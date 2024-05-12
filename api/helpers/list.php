<?php
error_reporting(E_ALL);



require("helpers/server_response.php");

$request = new ClientRequest();
$dataSource = new DataSource("data.json");
$response = new ServerResponse($request, $dataSource);

$response->process();


function POST(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);
    $newlist = $request->post['list'] ?? false;

    $listidTop = 0;
    foreach ($data as $todo) {
        $num = (int)$todo['listid'];
        if ($num > $listidTop) {
            $listidTop = $num;
        }
    }

    $newlistid = $listidTop + 1;

    $newlist = array(
        "list" => $newlist,
        "listid" => $newlistid,
        "tasks" => array(
            "task1" => "",
            "task2" => "",
            "task3" => ""
        )
    );

    array_push($data, $newlist);

    $newJson = json_encode($data);

    file_put_contents($dataSource->writePath, $newJson);

    $response->status = "OK";
    $response->outputJSON($newlist);
}
function GET(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);
    $output = [];
    $id = $request->get['id'] ?? false;

    if ($id != false) {
        $listid = $id;
        foreach ($data as $todo) {
            if (array_key_exists("listid", $todo) && $todo["listid"] == $listid) {
                array_push($output, $todo);
                break;
            }
        }
    } else {
        $output = $data;
    }

    $response->status = "SOMETHING WENT WRONG";

    if (sizeof($output) >= 1) {
        $response->status = "OK";
    } 

    $response->outputJSON($output);
}

function PUT(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);

    $listid = $request->put['listid'] ?? false;

    $newData = array(
        "list" => $request->put['list'],
        "listid" => $listid,
        "tasks" => array()
    );

    $target = 0;

    foreach ($data as $key => $todo) {
        if ($todo['listid'] == $listid) {
            $newData["tasks"] = $todo["tasks"];
            $target = $key;
        }
    }

    $data[$key] = $newData;

    $newJson = json_encode($data);

    file_put_contents($dataSource->writePath, $newJson);

    $response->status = "OK";
    $response->outputJSON($newData);
}

function DELETE(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);
    $data = array("Method" => "Delete");

    $response->status = "OK";
    $response->outputJSON($data);
}


exit;

$dataPath = __DIR__ . "\data.json";

$json = file_get_contents($dataPath);

$data = json_decode($json, true);

echo ("<pre>" . print_r($data, true) . "</pre>");

$newJson = json_encode($data);

//file_put_contents($dataPath, $newJson);

Header("Content-Type: application/json; charset=utf-8");
exit(json_encode($testResults));
