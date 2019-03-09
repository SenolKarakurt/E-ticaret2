<?php
ob_start();
session_start();
include "connection.php";

$p = $_GET["p"];
$site_name = '2';

if ($p == "writereview"){
    if (isset($_POST["send_review"])){
        $recaptcha_secret = "6LdjvwwTAAAAAEMqT_X6bw_MiH0c9qQtuQiWAbIv";
        $sendstring = "https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_secret."&response=".$_POST["g-recaptcha-response"];
        $connect_link = connect($sendstring);
        $decode = json_decode($connect_link);
        $date = date("Y-m-d H:i:s");
        $product_id = strip_tags(trim($_POST["pid"]));
        $imgid = strip_tags(trim($_POST["imgid"]));
        $page = strip_tags(trim($_POST["page"]));
        $review_name = strip_tags(trim($_POST["name"]));
        $review_email = strip_tags(trim($_POST["email"]));
        $review_comment = strip_tags(trim($_POST["comment"]));
        $review_rating = strip_tags(trim($_POST["rating"]));
        $review_show = 0;
        $site_name = '2';

        $savereview = $db->query("insert into `reviews`(`productid`, `name`, `comment`, `tarih`, `email`, `show`, `rating`, `websiteid`) values('$product_id','$review_name','$review_comment','$date','$review_email','$review_show','$review_rating','$site_name')");

        if ($savereview->rowCount() && $decode->{"success"} == "1"){
            header("Location: ../index.php?p=".$page."&prid=".$product_id."&imgid=".$imgid);
        }
        else{
            echo '<script type="text/javascript">alert("REVIEW NOT SAVED");</script>';
            header("Location: ../index.php?p=".$page."&prid=".$product_id."&imgid=".$imgid);
        }
    }
    else{
        header("Location: ../index.php?p=".$page."&prid=".$product_id."&imgid=".$imgid."");
    }
}
elseif ($p == "askquestion"){
    if (isset($_POST["send_question"])){
        $date = date("Y-m-d H:i:s");
        $product_id = strip_tags(trim($_POST["pid"]));
        $imgid = strip_tags(trim($_POST["imgid"]));
        $page = strip_tags(trim($_POST["page"]));
        $questionname = strip_tags(trim($_POST["name"]));
        $questionemail = strip_tags(trim($_POST["email"]));
        $questioncomment = strip_tags(trim($_POST["comment"]));
        $recaptcha_secret = "6LdjvwwTAAAAAEMqT_X6bw_MiH0c9qQtuQiWAbIv";
        $sendstring = "https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_secret."&response=".$_POST["g-recaptcha-response"];
        $connect_link = connect($sendstring);
        $decode = json_decode($connect_link);
        $questionshow = 0;
        $questionisemailsent = 0;
        $site_name = '2';

        $savequestion = $db->query("insert into `questions`(`productid`, `name`, `comment`, `tarih`, `email`, `show`, `isemailsent`, `websiteid`) values('$product_id','$questionname','$questioncomment','$date','$questionemail','$questionshow','$questionisemailsent','$site_name')");
        if ($savequestion->rowCount() && $decode->{"success"} == "1"){
            header("Location: ../index.php?p=".$page."&prid=".$product_id."&imgid=".$imgid);
        }
        else{
            echo '<script type="text/javascript">alert("QUESTION NOT SAVED");</script>';
            header("Location: ../index.php?p=".$page."&prid=".$product_id."&imgid=".$imgid);
        }
    }
    else{
        header("Location: ../index.php?p=".$page."&prid=".$product_id."&imgid=".$imgid);
    }
}
elseif ($p == "contact-send-email"){
    if (isset($_POST["send"])){
        $recaptcha_secret = "6LdjvwwTAAAAAEMqT_X6bw_MiH0c9qQtuQiWAbIv";
        $sendstring = "https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_secret."&response=".$_POST["g-recaptcha-response"];
        $name = strip_tags(trim($_POST["name"]));
        $tel = strip_tags(trim($_POST["tel"]));
        $email = strip_tags(trim($_POST["email"]));
        $comment = strip_tags(trim($_POST["comment"]));
        $strComments = $comment."<br /><br /><br />Name: ".$name."<br /><br />Tel: ".$tel;
        $connect_link = connect($sendstring);
        $decode = json_decode($connect_link);
        if (empty($email) || $email == ""){
            echo '<script type="text/javascript">alert("EMAIL ADRESS IS NULL");</script>';
            header("Location: ../index.php?p=contact");
        }
        else{
            if ($decode->{"success"} == "1"){

                /*
                 *
                 *
                 * MAIL
                 *
                 *
                 * */
                /*
                $mail = new \PHPMailer\PHPMailer\PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->Host = 'smtp.1and1.com';
                $mail->Port = 587;
                $mail->Username = $email;
                $mail->Password = "Sd911911";
                $mail->SetFrom($mail->Username, $name);
                $mail->AddAddress('info@stonedeals.co.uk', $name);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Contact Form Message';
                $mail->MsgHTML($strComments);
                if($mail->Send()) {
                    header("Location: index.php?p=email-posted");
                }
                else {
                    echo '<script type="text/javascript">alert("'.$mail->ErrorInfo.'");</script>';
                    header("Location: index.php?p=contact");
                }
                */
            }
            else{
                echo '<script type="text/javascript">alert("'.$decode->{"error-codes"}{0}.'");</script>';
                header("Location: ../index.php?p=contact");
            }
        }
    }
    else{
        echo '<script type="text/javascript">alert("DATA NOT FOUND");</script>';
        header("Location: ../index.php?p=contact");
    }
}
elseif ($p == "calculate-discount"){
    if (isset($_POST["couponcode"]) && $_POST["couponcode"] != "" && $_POST["orderid"] != ""){
        $orderid = $_POST["orderid"];
        $couponcode = strip_tags(trim($_POST["couponcode"]));
        $cpn = $db->query("SELECT * FROM orders where id='$orderid'")->fetch();
        if ($couponcode == $cpn["couponcode"]){
            $valid = 1;
            header("Location: ../index.php?p=cart&beforevalid=1");
        }
        else{
            $sizes = $db->query("select * from coupons where couponcode='$couponcode'");
            if ($sizes->rowCount()){
                foreach($sizes as $sz){
                    $couponcode = $sz["couponcode"];
                    $ste = strtotime($sz["validdate"]);
                    $validdate = date("Y-m-d",$ste);
                    $validdate = strtotime($validdate);
                    $now = strtotime(date("Y-m-d"));
                    if (($sz["isused"] == "0") && ($validdate >= $now)){
                        $cpnup = $db->query("update orders set couponcode='$couponcode' where id='$orderid'");
                        if ($cpnup->rowCount()){
                            $or = $db->query("select * from orders where id='$orderid'")->fetch();
                            $subdis = $or["subtotal"] * ($sz["discountrate"] / 100);
                            $sub = $or["subtotal"] - $subdis;
                            $vatdis = $or["vattotal"] * ($sz["discountrate"] / 100);
                            $vat = $or["vattotal"] - $vatdis;
                            $entdis = $or["entotal"] * ($sz["discountrate"] / 100);
                            $ent = $or["entotal"] - $entdis;
                            $upent = $db->query("update orders set subtotal='$sub', vattotal='$vat', entotal='$ent', discountrate='".$sz["discountrate"]."', discountprice='$entdis' where id='$orderid'");
                            if ($upent->rowCount()){
                                header("Location: ../index.php?p=cart&valid=1");
                            }
                            else{
                                header("Location: ../index.php?p=cart");
                            }
                        }
                        else{
                            header("Location: ../index.php?p=cart");
                        }
                    }
                    else{
                        if ($validdate < $now){
                            $notvalid = 3;
                            header("Location: ../index.php?p=cart&notvalid=".$notvalid);
                        }
                        elseif ($sz["isused"] == "1"){
                            $notvalid = 2;
                            $couponcode = "";
                            $cpnup = $db->query("update orders set couponcode='$couponcode' where id='$orderid'");
                            if ($cpnup->rowCount()){
                                header("Location: ../index.php?p=cart&notvalid=".$notvalid);
                            }
                            else{
                                header("Location: ../index.php?p=cart");
                            }
                        }
                        else{
                            header("Location: ../index.php?p=cart");
                        }
                    }
                }
            }
            else{
                $notvalid = 1;
                $couponcode = "";
                $cpnup = $db->query("update orders set couponcode='$couponcode' where id='$orderid'");
                if ($cpnup->rowCount()){
                    header("Location: ../index.php?p=cart&notvalid=".$notvalid);
                }
                else{
                    header("Location: ../index.php?p=cart");
                }
            }
        }
    }
    else{
        header("Location: ../index.php?p=cart");
    }
}
elseif ($p == "bsk"){
   if (isset($_SESSION["travertinetiles"])){
       $sessionid = $_SESSION["travertinetiles"];
       $session = $db->query("select * from basket where sessionid='".$_SESSION["travertinetiles"]."'");
       foreach ($session as $pr) {
           $pr_id = $pr["productid"];
           $size_id = $pr["sizeid"];
           $product = $db->query("select * from products where pr_id='$pr_id' and websiteid='$site_name'")->fetch();
           $size = $db->query("select * from sizes where id='$size_id'")->fetch();
           $itemunit = $size["itemunit"];
           $qtyunit = $size["qtyunit"];
           $szunit = $size["sizeunit"];
           $cmb = $db->query("select * from combos where id='$qtyunit'")->fetch();
           $cmbitem = $db->query("select * from combos where id='$itemunit'")->fetch();
           $unit = $db->query("select * from combos where id='$szunit'")->fetch();
           $qtySl = $db->query("select * from basket where sessionid='$sessionid' and productid='$pr_id' and sizeid='$size_id'")->fetch();
           $img = $db->query("select * from images where pr_id='$pr_id' and websiteid='$site_name'")->fetch();
           ?>
           <li>
               <a href="?p=cart">
                   <img src="img/products/<?php echo $img["imagename"]; ?>" width="53" height="40" align="absmiddle">
                   <?php echo $product["name"]." • ".$size["size"]."  ";
                   if ($unit["name"] != "None") {
                       echo $unit["name"];
                   }
                   ?>
                   <?php
                   $item = $qtySl["quantity"] / ($size["area"] / 10000);
                   $ceil = ceil($item);
                   $quantity = $ceil * $size["area"] / 10000;
                   ?>
                   <small> - (<?php if ($pr["sample"] == 1){ echo $pr["quantity"]; }elseif ($pr["productid"] == 146){ echo $pr["quantity"]; }elseif ($pr["productid"] == 145){ echo $pr["quantity"]; }elseif ($pr["productid"] == 134){ echo $pr["quantity"]; }elseif ($pr["productid"] == 138){ echo $pr["quantity"]; }elseif ($pr["productid"] == 248){ echo $pr["quantity"]; }else{ echo number_format($quantity,3);} ?> <?php echo $cmb["name"];  ?>)</small>
               </a>
               <div class="point-line"></div>
           </li>
           <?php
       }
   }
   else{   ?>
       <li>
           <a href="?p=cart">Your shopping cart is empty</a>
       </li>
   <?php   }
}
elseif ($p == "btnbsk"){
    echo "Basket";
if (isset($_SESSION["travertinetiles"])){
    $sessionid = $_SESSION["travertinetiles"];
    $session = $db->query("select * from basket where sessionid='$sessionid'");
    $cntsess = $session->rowCount();
    if ($cntsess != 0){   ?>
        <span class="small">(<?php echo $cntsess; ?> item)</span>
    <?php
    }
}
?>
    <span class="caret"></span>
    <?php
}
elseif ($p == "cartremove"){
    $prid = $_GET["prid"];
    $basketid = $_GET["bid"];
    $prdel = $db->query("DELETE FROM `basket` WHERE id='$basketid' and productid='$prid'");
    if ($prdel){
        if (isset($_SESSION["travertinetiles"])){
            $sessionid = $_SESSION["travertinetiles"];
            $session = $db->query("select * from basket where sessionid='$sessionid'");
            $cntsess = $session->rowCount();
            if ($cntsess != 0){
                header("Location: ../index.php?p=cart");
            }
            else{
                unset($_SESSION["travertinetiles"]);
                unset($_SESSION["select"]);
                header("Location: ../index.php?p=cart");
            }
        }
        else{
            unset($_SESSION["travertinetiles"]);
            unset($_SESSION["select"]);
            header("Location: ../index.php?p=cart");
        }
    }
    else{
        header("Location: ../index.php");
    }
}
elseif ($p == "addcart"){
    $orderid = $_GET["orderid"];
    if (isset($_POST["submitbflex"])){
        if ($_POST["qty1"] != 0){
            $qty1 = strip_tags(trim($_POST["qty1"]));
            $productid = $_POST["productid"];
            //$szid = $_POST["productid"];
            $pr_sizeid = $_POST["product_sizeid"];

            if (isset($_SESSION["travertinetiles"])){
                $sessionid = $_SESSION["travertinetiles"];
            }
            else{
                $sessionid = date("Ymdhis").$productid;
                $_SESSION["travertinetiles"] = $sessionid;
            }

            $createdate = date("Y-m-d H:i:s");

            $basketinsert = $db->query("insert into basket(orderid,productid,sizeid,sessionid,quantity,vatrate,createdate,websiteid) values('$orderid','$productid','$pr_sizeid','$sessionid','$qty1','20','$createdate','$site_name')");

            if ($basketinsert){
                header("Location: ../?p=cart");
            }
            else{
                header("Location: ../?p=cart");
            }
        }
        else{
            header("Location: ../?p=cart");
        }
    }
    elseif (isset($_POST["submitbrapid"])){
        if ($_POST["qty11"] != 0){
            $qty11 = strip_tags(trim($_POST["qty11"]));
            $productid = $_POST["productid"];
            //$szid = $_POST["productid"];
            $pr_sizeid = $_POST["product_sizeid"];

            if (isset($_SESSION["travertinetiles"])){
                $sessionid = $_SESSION["travertinetiles"];
            }
            else{
                $sessionid = date("Ymdhis").$productid;
                $_SESSION["travertinetiles"] = $sessionid;
            }

            $createdate = date("Y-m-d h:i:s");

            $basketinsert = $db->query("insert into basket(orderid,productid,sizeid,sessionid,quantity,vatrate,createdate,websiteid) values('$orderid','$productid','$pr_sizeid','$sessionid','$qty11','20','$createdate','$site_name')");

            if ($basketinsert){
                header("Location: ../?p=cart");
            }
            else{
                header("Location: ../?p=cart");
            }
        }
        else{
            header("Location: ../?p=cart");
        }
    }
    elseif (isset($_POST["submitbltp"])){
        if ($_POST["qty3"] != 0){
            $qty3 = strip_tags(trim($_POST["qty3"]));
            $productid = $_POST["productid"];
            $pr_sizeid = $_POST["product_sizeid"];
            $select = $db->query("select * from product_sizes where id='$pr_sizeid'")->fetch();
            $sizeid = $select["sizeid"];

            if (isset($_SESSION["travertinetiles"])){
                $sessionid = $_SESSION["travertinetiles"];
            }
            else{
                $sessionid = date("Ymdhis").$productid;
                $_SESSION["travertinetiles"] = $sessionid;
            }

            $createdate = date("Y-m-d h:i:s");

            $basketinsert = $db->query("insert into basket(orderid,productid,sizeid,sessionid,quantity,vatrate,createdate,websiteid) values('$orderid','$productid','$sizeid','$sessionid','$qty3','20','$createdate','$site_name')");

            if ($basketinsert){
                header("Location: ../?p=cart");
            }
            else{
                header("Location: ../?p=cart");
            }
        }
        else{
            header("Location: ../?p=cart");
        }
    }
    elseif (isset($_POST["submitbpolis"])){
        if ($_POST["qty4"] != 0){
            $qty4 = strip_tags(trim($_POST["qty4"]));
            $productid = $_POST["productid"];
            $pr_sizeid = $_POST["product_sizeid"];
            $select = $db->query("select * from product_sizes where id='$pr_sizeid'")->fetch();
            $sizeid = $select["sizeid"];

            if (isset($_SESSION["travertinetiles"])){
                $sessionid = $_SESSION["travertinetiles"];
            }
            else{
                $sessionid = date("Ymdhis").$productid;
                $_SESSION["travertinetiles"] = $sessionid;
            }

            $createdate = date("Y-m-d h:i:s");

            $basketinsert = $db->query("insert into basket(orderid,productid,sizeid,sessionid,quantity,vatrate,createdate,websiteid) values('$orderid','$productid','$sizeid','$sessionid','$qty4','20','$createdate','$site_name')");

            if ($basketinsert){
                header("Location: ../?p=cart");
            }
            else{
                header("Location: ../?p=cart");
            }
        }
        else{
            header("Location: ../?p=cart");
        }
    }
    elseif (isset($_POST["submitbgrout"])){
        if ($_POST["qty7"] != 0){
            $qty7 = strip_tags(trim($_POST["qty7"]));
            $productid = $_POST["productid"];
            $pr_sizeid = $_POST["product_sizeid"];
            $select = $db->query("select * from product_sizes where id='$pr_sizeid'")->fetch();
            $sizeid = $select["sizeid"];

            if (isset($_SESSION["travertinetiles"])){
                $sessionid = $_SESSION["travertinetiles"];
            }
            else{
                $sessionid = date("Ymdhis").$productid;
                $_SESSION["travertinetiles"] = $sessionid;
            }

            $createdate = date("Y-m-d h:i:s");

            $basketinsert = $db->query("insert into basket(orderid,productid,sizeid,sessionid,quantity,vatrate,createdate,websiteid) values('$orderid','$productid','$sizeid','$sessionid','$qty7','20','$createdate','$site_name')");

            if ($basketinsert){
                header("Location: ../?p=cart");
            }
            else{
                header("Location: ../?p=cart");
            }
        }
        else{
            header("Location: ../?p=cart");
        }
    }
    else{
        header("Location: ../index.php");
    }
}
elseif ($p == "add-user"){
    if (isset($_POST["emailquote"])){
        $firstname = strip_tags(trim($_POST["firstname"]));
        $lastname = strip_tags(trim($_POST["lastname"]));
        $house = strip_tags(trim($_POST["house"]));
        $street = strip_tags(trim($_POST["street"]));
        $city = strip_tags(trim($_POST["city"]));
        $county = strip_tags(trim($_POST["county"]));
        $country = strip_tags(trim($_POST["country"]));
        $postcode = strip_tags(trim($_POST["postcode"]));
        $email = strip_tags(trim($_POST["email"]));
        $tel = strip_tags(trim($_POST["tel"]));
        $usertype = strip_tags(trim($_POST["usertype"])); // usertype olarak db de email quote şeklinde bulunmakta....
        $ipadresi = $_REQUEST['REMOTE_ADDR'];
        $linkyapisi = @unserialize(file_get_contents('http://ip-api.com/php/'.$ipadresi));
        $ipno = $linkyapisi["query"];
        $maxorderid = $db->query("select max(orderid) as maxorderid from basket")->fetch();
        $maxproformano = $db->query("select max(proformano) as maxproformano from orders")->fetch();
        //$maxuser = $db->query("select max(id) as maxuserid from users")->fetch();
        $basket = $db->query("select * from basket where sessionid='".$_SESSION["travertinetiles"]."'")->fetch();
        $maxorderID = $basket["orderid"];
        $cartdate = $basket["createdate"];
        $maxproformaNo = $maxproformano["maxproformano"] + 1;

        $prlist = $db->query("select * from basket where sessionid='".$_SESSION["travertinetiles"]."'");
        $subprices = array();
        $prquantity = array();
        $k = 0;
        foreach($prlist as $prls){
            $pr_id = $prls["productid"];
            $size_id = $prls["sizeid"];
            $pr = $db->query("select * from products where szid='$pr_id' and websiteid='$site_name'")->fetch();
            $szid = $pr["szid"];
            $prsizes = $db->query("select * from product_sizes where productid='$szid' and sizeid='$size_id'")->fetch();
            $qtySl = $db->query("select * from basket where sessionid='".$_SESSION["travertinetiles"]."' and productid='$pr_id' and sizeid='$size_id'")->fetch();
            $sizes = $db->query("select * from sizes where id='$size_id'")->fetch();
            $item = $qtySl["quantity"] / ($sizes["area"] / 10000);
            $ceil = ceil($item);
            $quantity = $ceil * $sizes["area"] / 10000;
            if ($prls["sample"] == 1){
                $prquantity[$k] = $prls["quantity"];
            }
            else{
                $subprice = $quantity * $prsizes["price"];
                $subprices[$k] = $subprice;
            }
            $k++;
        }
        if (array_sum($subprices) == 0){
            if (array_sum($prquantity) > 2){
                $dlvry = array_sum($prquantity) - 2;
                $deliverytotal = $dlvry * 2;
                $entotal = array_sum($subprices) + $deliverytotal;
            }
            else{
                $entotal = array_sum($subprices);
            }
        }
        else{
            if (array_sum($prquantity) > 2){
                $dlvry = array_sum($prquantity) - 2;
                $deliverytotal = $dlvry * 2;
                $entotal = array_sum($subprices) + $deliverytotal;
            }
            else{
                $entotal = array_sum($subprices);
            }
        }
        $ordertype = 2;
        $status = 0;

        $saveuser = $db->query("insert into users (email,firstname,lastname,house,street,city,county,country,postcode,tel,ipno,usertype) values('$email','$firstname','$lastname','$house','$street','$city','$county','$country','$postcode','$tel','$ipno','$usertype')");
        if ($saveuser->rowCount()){
            $userID = $db->lastInsertId();
            //$saveproforma = $db->query("insert into orders (id,proformano,ordertype,status,entotal,sessionid,userid,cartdate,orderdate,websiteid) values('$maxorderID','$maxproformaNo','$ordertype','$status','$entotal','".$_SESSION["travertinetiles"]."','$userID','$cartdate','$cartdate','$site_name')");
            $newsession = $_SESSION["travertinetiles"];
            $orderup = $db->prepare("update orders set sampleno=:sampleno, orderno=:orderno, ordertype=:ordertype, status=:status, entotal=:entotal, ipno=:ipno, cartdate=:cartdate,orderdate=:orderdate, deliverydate=:deliverydate, postcodeid=:postcodeid, isnextday=:isnextday, deliverytime=:deliverytime, deliveryprice=:deliveryprice where id=:id and sessionid=:sessionid");
            $orderup->bindValue(':sampleno',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':orderno',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':ordertype',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':status',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':entotal',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':ipno',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':cartdate',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':orderdate',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':deliverydate',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':postcodeid',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':isnextday',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':deliverytime',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':deliveryprice',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':id',$maxorderID,PDO::PARAM_INT);
            $orderup->bindValue(':sessionid',$newsession,PDO::PARAM_STR);
            $orderup->execute();

            $saveproforma = $db->query("update orders set proformano='$maxproformaNo', ordertype='$ordertype', status='$status', entotal='$entotal', userid='$userID', ipno='$ipno', cartdate='$cartdate', orderdate='$cartdate', websiteid='$site_name' where id='$maxorderID' and sessionid='".$_SESSION["travertinetiles"]."'");
            if ($saveproforma){
                $isorderedup = $db->query("update basket set isordered=1 where orderid='$maxorderID' and sessionid='".$_SESSION["travertinetiles"]."'");
                header("Location: ../index.php");
                unset($_SESSION["travertinetiles"]);
                unset($_SESSION["select"]);
            }
            else{
                header("Location: ../index.php");
            }
        }
        else{
            echo '<script type="text/javascript">alert("USER NOT INSERT");</script>';
            header("Location: ../index.php");
        }
    }
    elseif (isset($_POST["requestsample"])){

        $firstname = strip_tags(trim($_POST["firstname"]));
        $lastname = strip_tags(trim($_POST["lastname"]));
        $contactname = $firstname." ".$lastname;
        $house = strip_tags(trim($_POST["house"]));
        $street = strip_tags(trim($_POST["street"]));
        $city = strip_tags(trim($_POST["city"]));
        $county = strip_tags(trim($_POST["county"]));
        $country = strip_tags(trim($_POST["country"]));
        $postcode = strip_tags(trim($_POST["postcode"]));
        $email = strip_tags(trim($_POST["email"]));
        $tel = strip_tags(trim($_POST["tel"]));
        $usertype = strip_tags(trim($_POST["usertype"])); // usertype olarak db de email quote şeklinde bulunmakta....
        $ipadresi = $_REQUEST['REMOTE_ADDR'];
        $linkyapisi = @unserialize(file_get_contents('http://ip-api.com/php/'.$ipadresi));
        $ipno = $linkyapisi["query"];
        $maxorderid = $db->query("select max(orderid) as maxorderid from basket")->fetch();
        $maxsampleno = $db->query("select max(sampleno) as maxsampleno from orders")->fetch();
        //$maxuser = $db->query("select max(id) as maxuserid from users")->fetch();
        $basket = $db->query("select * from basket where sessionid='".$_SESSION["travertinetiles"]."'")->fetch();
        $maxorderID = $basket["orderid"];
        $cartdate = $basket["createdate"];
        $maxsampleNo = $maxsampleno["maxsampleno"] + 1;
        //$userID = $maxuser["maxuserid"] + 1;

        $prlist = $db->query("select * from basket where sessionid='".$_SESSION["travertinetiles"]."'");
        $subprices = array();
        $prquantity = array();
        $k = 0;
        foreach($prlist as $prls){
            $pr_id = $prls["productid"];
            $size_id = $prls["sizeid"];
            $pr = $db->query("select * from products where szid='$pr_id' and websiteid='$site_name'")->fetch();
            $szid = $pr["szid"];
            $prsizes = $db->query("select * from product_sizes where productid='$szid' and sizeid='$size_id'")->fetch();
            $qtySl = $db->query("select * from basket where sessionid='".$_SESSION["travertinetiles"]."' and productid='$pr_id' and sizeid='$size_id'")->fetch();
            $sizes = $db->query("select * from sizes where id='$size_id'")->fetch();
            $item = $qtySl["quantity"] / ($sizes["area"] / 10000);
            $ceil = ceil($item);
            $quantity = $ceil * $sizes["area"] / 10000;
            if ($prls["sample"] == 1){
                $prquantity[$k] = $prls["quantity"];
            }
            else{
                $subprice = $quantity * $prsizes["price"];
                $subprices[$k] = $subprice;
            }
            $k++;
        }
        if (array_sum($subprices) == 0){
            if (array_sum($prquantity) > 2){
                $dlvry = array_sum($prquantity) - 2;
                $deliverytotal = $dlvry * 2;
                $entotal = array_sum($subprices) + $deliverytotal;
            }
            else{
                $entotal = array_sum($subprices);
            }
        }
        else{
            if (array_sum($prquantity) > 2){
                $dlvry = array_sum($prquantity) - 2;
                $deliverytotal = $dlvry * 2;
                $entotal = array_sum($subprices) + $deliverytotal;
            }
            else{
                $entotal = array_sum($subprices);
            }
        }

        $ordertype = 1;
        $status = 0;

        $saveuser = $db->query("insert into users (email,firstname,lastname,house,street,city,county,country,postcode,tel,ipno,usertype) values('$email','$firstname','$lastname','$house','$street','$city','$county','$country','$postcode','$tel','$ipno','$usertype')");
        if ($saveuser->rowCount()){
            $userID = $db->lastInsertId();
            //$saveproforma = $db->query("insert into orders (id,sampleno,ordertype,status,entotal,sessionid,userid,cartdate,orderdate,websiteid) values('$maxorderID','$maxsampleNo','$ordertype','$status','$entotal','".$_SESSION["travertinetiles"]."','$userID','$cartdate','$cartdate','$site_name')");

            $newsession = $_SESSION["travertinetiles"];
            $orderup = $db->prepare("update orders set sampleno=:sampleno, orderno=:orderno, ordertype=:ordertype, status=:status, entotal=:entotal, ipno=:ipno, cartdate=:cartdate,orderdate=:orderdate, deliverydate=:deliverydate, postcodeid=:postcodeid, isnextday=:isnextday, deliverytime=:deliverytime, deliveryprice=:deliveryprice where id=:id and sessionid=:sessionid");
            $orderup->bindValue(':sampleno',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':orderno',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':ordertype',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':status',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':entotal',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':ipno',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':cartdate',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':orderdate',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':deliverydate',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':postcodeid',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':isnextday',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':deliverytime',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':deliveryprice',NULL,PDO::PARAM_NULL);
            $orderup->bindValue(':id',$maxorderID,PDO::PARAM_INT);
            $orderup->bindValue(':sessionid',$newsession,PDO::PARAM_STR);
            $orderup->execute();

            $saveproforma = $db->query("update orders set sampleno='$maxsampleNo', ordertype='$ordertype', status='$status', entotal='$entotal', userid='$userID', ipno='$ipno', cartdate='$cartdate', orderdate='$cartdate', websiteid='$site_name' where id='$maxorderID' and sessionid='".$_SESSION["travertinetiles"]."'");
            if ($saveproforma){
                $isorderedup = $db->query("update basket set isordered=1 where orderid='$maxorderID' and sessionid='".$_SESSION["travertinetiles"]."'");

                $bs = $db->query("select * from basket where sessionid='".$_SESSION['travertinetiles']."'")->fetch();
                $orderid = $bs["orderid"];

                $forwhat = "Sample-Email";
                if ($site_name == 2){
                    $strFromEmail = "sales@travertinetilesuk.com";
                }
                else{
                    $strFromEmail = "info@stonedeals.co.uk";
                }

                $user = $db->query("SELECT users.*, orders.*, orders.isdeleted as oisdeleted FROM users left join orders on users.id=orders.userid where orders.id='$orderid'")->fetch();
                $couponcode = $user["couponcode"];
                $strName = $contactname;
                if ($strName == " "){ $strName = $user["companyname"]; }
                $strAddress = $user["house"]." ".$user["street"]."<br />".$user["county"]."<br />".$user["city"]."<br />".$user["postcode"]." ".$user["country"];
                $format = $db->query("SELECT * FROM emailformats where forwhat='$forwhat' and websiteid='$site_name'")->fetch();
                $strComments = $format["emailtext"];
                $strSubject = $format["subject"];
                if ($couponcode != ""){
                    $cpn = $db-query("SELECT * FROM coupons where couponcode='$couponcode'")->fetch();
                    $strVoucher = $cpn["couponcode"];
                    $strDiscountRate = $cpn["discountrate"];
                    $strDiscountMoney = $cpn["discountmoney"];
                    if ($strDiscountRate == ""){
                        $strDiscountRate = "";
                    }
                    if ($strDiscountMoney == ""){
                        $strDiscountMoney = "";
                    }
                    $date = date("Y-m-d");
                    $date = strtotime("7 day",strtotime($date));
                    $now7 = date("Y-m-d",$date);
                    $valid = $db->query("update coupons set validdate='$now7' where couponcode='$couponcode'");
                    $strValidDate = $valid["validdate"];
                }
                if (strstr($strComments,"<strSamples>")){
                    $basket = $db->query("SELECT basket.*, products.name, products.categoryid, products.pr_id as pidi FROM basket inner join products on basket.productid = products.pr_id where basket.orderid='$orderid' and products.showsite=1");

                    $strSamples = $strSamples.'<table style="width:100%;border-collapse:collapse;font-size:14px;text-align:left" cellpadding="5"> <thead> <tr style="background-color:#eee; padding:5px"> <th style="padding:5px">Product Name</th> <th>Quantity</th> </tr></thead> <tbody>';
                    foreach($basket as $bsk){
                        $img = $db->query("select * from images where pr_id='".$bsk['productid']."'")->fetch();
                        $imgid = $img["pr_id"];
                        $strSamples = $strSamples.'<tr style="border-bottom:1px solid #eee">';
                        $strSamples = $strSamples.'<td style="padding:5px">"'.'"<a href="?p=product_details&prid="'.$bsk["pidi"].'&imgid='.$imgid.' target="_blank">'.$bsk["name"].'"</a></td><td>'.$bsk["quantity"].' item(s)</td></tr>';
                    }
                    $strSamples = $strSamples.'</tbody></table>';
                }

                $today = date("Y-m-d");
                if ($strName == "" || empty($strName)){
                    $strName = "...";
                    $strComments = str_replace("<strName>",$strName,$strName);
                    $strComments = str_replace("<strVoucher>",$strVoucher,$strVoucher);
                    $strComments = str_replace("<strDiscountRate>",$strDiscountRate,$strDiscountRate);
                    $strComments = str_replace("<strDiscountMoney>",$strDiscountMoney,$strDiscountMoney);
                    $strComments = str_replace("<strValidDate>",$strValidDate,$strValidDate);
                    $strComments = str_replace("<strNow>",$strNow,$today);
                    $strComments = str_replace("<strAddress>",$strAddress,$strAddress);
                    $strComments = str_replace("<strSamples>",$strSamples,$strSamples);
                    $strComments = str_replace("<strQuote>",$strQuote,$strQuote);
                }

                /*
                 *
                 *
                 * MAIL
                 *
                 *
                 *
                 */
                header("Location: ../index.php");
                unset($_SESSION["travertinetiles"]);
                unset($_SESSION["select"]);
            }
            else{
                header("Location: ../index.php");
            }
        }
        else{
            echo '<script type="text/javascript">alert("USER NOT INSERT");</script>';
            header("Location: ../index.php");
        }
    }
    elseif (isset($_POST["email"])){
        $session = $_SESSION["travertinetiles"];
        $basket = $db->query("select * from basket where sessionid='$session'")->fetch();
        $orderid = $basket["orderid"];
        $ipadresi = $_REQUEST['REMOTE_ADDR'];
        $linkyapisi = @unserialize(file_get_contents('http://ip-api.com/php/'.$ipadresi));
        $ipno = $linkyapisi["query"];
        $usertype = strip_tags(trim($_POST["usertype"]));
        $email = strip_tags(trim($_POST["email"]));
        $orderuser = $db->query("select * from orders where id='$orderid'")->fetch();
        $max = $db->query("select max(orderno) as maxorderno from orders")->fetch();
        $orderno = $max["maxorderno"] + 1;
        /*
         *
         * Aynı email adresine ait kullanıcı usertype olarak hem order yapabilir ya da email quote olarak ya da request sample olarakta işlem yapabilmelidir.
         * Yani bir email adresine ait 3 işleme kadar izin verilmelidir. Olması gerekenin bu olmalı.
         *  Mevcut sistemde focusout olduğu anda sürekli veritabanına kayıt oluyor.
         *
         *
         */
        if (!empty($orderuser["userid"]) && $orderuser["userid"] != ""){
            $userup = $db->query("update users set email='$email' where id='".$orderuser['userid']."'");
            if ($userup->rowCount()){
                header("Location: ../?p=proceed-to-checkout");
            }
            else{
                echo '<script type="text/javascript">alert("USER NOT UPDATE");</script>';
                header("Location: ../?p=proceed-to-checkout");
            }
        }
        else{
            $emailsave = $db->query("insert into users (email,ipno,usertype) values('$email','$ipno','$usertype')");
            if ($emailsave->rowCount()){
                $lastid = $db->lastInsertId();
                $ip = $linkyapisi["query"];
                $ordertype = 3;
                $status = 0;
                $isdeleted = 0;

                $orderup = $db->query("update orders set orderno='$orderno', ordertype='$ordertype', status='$status', userid='$lastid', ipno='$ip', isdeleted='$isdeleted' where id='$orderid' and sessionid='$session'");

                if ($orderup->rowCount()){
                    if ($basket["isordered"] != 1){
                        $isordered = 1;
                        $isorderedup = $db->query("update basket set isordered='$isordered' where orderid='$orderid' and sessionid='$session'");
                        if ($isorderedup->rowCount()){
                            header("Location: ../?p=proceed-to-checkout");
                        }
                        else{
                            echo '<script type="text/javascript">alert("BASKET NOT UPDATE");</script>';
                            header("Location: ../?p=proceed-to-checkout");
                        }
                    }
                    else{
                        header("Location: ../?p=proceed-to-checkout");
                    }
                }
                else{
                    echo '<script type="text/javascript">alert("USER NOT UPDATE");</script>';
                    header("Location: ../?p=proceed-to-checkout");
                }
            }
            else{
                echo '<script type="text/javascript">alert("USER NOT INSERT");</script>';
                header("Location: ../?p=proceed-to-checkout");
            }
        }
    }
    else{
        header("Location: ../index.php");
    }
}
elseif ($p == "updatecost"){
    $id = $_POST["id"];
    $qty2 = $_POST["qty2"];
    $upbasket = $db->query("update basket set quantity='$qty2' where id='$id'");
}
elseif ($p == "reduce"){
    $bsid = $_POST["id"];
    $selectbasket = $db->query("select * from basket where id='$bsid'")->fetch();
    $qty2 = $selectbasket["quantity"];
    $qty2 = $qty2 - 1;
    if ($qty2 >= 1 && $qty2 < 9999){
        $upbasket = $db->query("update basket set quantity='$qty2' where id='$bsid'");
    }
}
elseif ($p == "increase"){
    $bsid = $_POST["id"];
    $selectbasket = $db->query("select * from basket where id='$bsid'")->fetch();
    $qty2 = $selectbasket["quantity"];
    $qty2 = $qty2 + 1;
    if ($qty2 >= 1 && $qty2 < 9999){
        $upbasket = $db->query("update basket set quantity='$qty2' where id='$bsid'");
    }
}

?>


<?php
function connect($link){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$link);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    $operation = curl_exec($ch);
    curl_close($ch);
    return $operation;
}
?>