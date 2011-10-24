<?php
include 'atk4/loader.php';
$api=new ApiWeb('sample_project');
$api->add('HelloWorld');
$api->add('jUI');
$f=$api->add('Form');
$f->addSubmit('ok');
$api->execute();
