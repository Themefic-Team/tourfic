<?php 

$id=$value['id'];
$placeholder=$value['placeholder'];
$title=$value['title'];
$fieldsname = $sectionid.'['.$id.']';
 
if(!empty($title)){
echo "<label>{$title}</label>";
}
echo "<input type='{$type}' id='{$id}' name='{$fieldsname}' placeholder='{$placeholder}'> ";

?>