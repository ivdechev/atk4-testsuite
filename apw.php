<?php
include 'atk4/loader.php';
$api=new ApiWeb('sample_project');
$api->add('jUI');
$api->add('H1')->set('Subscribe to Newsletter');
$form=$api->add('Form');
$form->addField('line','email');
$form->addSubmit('ok');
if($form->isSubmitted()){
$em=$form->get('email');
$form->js()->univ()->alert('Thank you for subscribing, '.$em)->execute();
}
$api->execute();
