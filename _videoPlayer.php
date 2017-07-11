 <?php
include_once("includes/framework.php");


$node = JRequest::getVar('node');


if(!$node){

    $node = $objMenu->get_page_node_id();

}
if (JRequest::getVar('id')) {

    $sql_video = "SELECT a.* FROM #__print_tvc a where id='" . JRequest::getVar('id') . "' ";

    $result_video = $objDB->setQuery($sql_video);

    //echo $objDB->getQuery();

    $row_video = $objDB->loadObject();

}
?>
<?php echo!($row_video) ? $rows_tvc[0]->content : $row_video->content ?>