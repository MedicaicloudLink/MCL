<?php
echo phpinfo();exit;
$bulk = new MongoDB\Driver\BulkWrite;
$document = ['_id' => new MongoDB\BSON\ObjectID, 'name' => 'asdfgh123456'];

$_id= $bulk->insert($document);

var_dump($_id);

$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
$result = $manager->executeBulkWrite('test.runoob', $bulk, $writeConcern);
?>
