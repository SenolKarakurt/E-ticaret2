<?php
include "connection.php";
$term = $_GET["term"];
$site_id = '2';

$like = $db->query("select DISTINCT products.szid, images.pr_id, products.name, products.showsite, products.websiteid from products left join images on products.szid=images.pr_id where name like '%".$term."%' and products.showsite=1 and products.websiteid='$site_id' order by rand() limit 10");

if ($like->rowCount()){
    $data = array();
    foreach ($like as $item){
        $img = $db->query("select * from images where pr_id='".$item["szid"]."' and websiteid='$site_id'")->fetch();
        $data[] = array(
            "value"      =>  $item["name"],
            "imageid"    =>  $img["id"],
            "imagename"  =>  $img["imagename"],
            "pr_id"      =>  $item["pr_id"],
            "name"       =>  $item["name"],
            "fromprice"  =>  $item["fromprice"],
            "categoryid" =>  $item["categoryid"],
            "showsite"   =>  $item["showsite"],
            "websiteid"  =>  $item["websiteid"]
        );
    }
    echo json_encode($data);
}
?>