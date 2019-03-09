<?php
$db = new PDO("mysql:host=localhost;dbname=newconcept","root","root");
if ($db) {
    /*
    $im = $db->prepare("update images set websiteid=:websiteid where websiteid=:id");
    $im->bindValue(':websiteid',NULL,PDO::PARAM_NULL);
    $im->bindValue(':id',0,PDO::PARAM_INT);
    $im->execute();
    */

    //$file = fopen("pr.txt","r");
    /*
    $im = $db->query("SELECT * FROM `images` WHERE websiteid IS NULL");
    $file = fopen("rp.txt","wb");
    if ($im->rowCount()){
        foreach ($im as $item){
            $ad = $item["id"]."\r\n";
            echo fwrite($file,$ad);
        }
    }
    else{
        echo "olmadi";
    }
    */
    /*
    for ($i = 248; $i <= 478; $i++){
        $ad = $i."\r\n";
        echo fwrite($file,$ad);
    }
*/
    $file2 = fopen("rp.txt","r");

    while(!feof($file2)){
        //$ad = fgets($file);
        $ad2 = fgets($file2);
        //echo  $ad."=>".$ad2."<br />";
        //echo $ad."<br />";

        $up = $db->query("update images set websiteid=1 where id='$ad2'");
        if ($up){
            echo "oluyor"."     "."<br />";

        }
        else{
            echo "olmuyor"."<br />";
        }
    }

}
else{
    echo "olmaz";
}
?>