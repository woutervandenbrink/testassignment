<?php

if(file_exists(__DIR__.'/../admin/dbconnection.php')){
    require_once(__DIR__.'/../admin/dbconnection.php');
}else{
    exit("Sorry, file inclusion error: exiting");
}

/**
 * @param $email
 * @return bool
 */
function checkEmailInDb($email){
    //empty function, unimplemented database check
    return true;
}

/**
 * @param $email
 * @return bool
 */
function checkemail($email){

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //check if exists ;
        if(checkEmailInDb($email)==false){

            return array('status'=>false,'errormessage'=>'Emailadres is al geregistreerd');
        }
        return array('status'=>true,'errormessage'=>'');
        //return true;
    }else{
       // return false;
        return array('status'=>false,'errormessage'=>'Email heeft niet een geldig formaat');
    }
}

/**
 * @param $datum
 * @return bool
 */
function checkdatum($datum){
        //check pattern DD-MM-JJJJ UU:MM in $datum
        $errormessage='';
        $regsmatchs=array();
        if(mb_ereg('\A\s*(\d\d)-(\d\d)-(\d\d\d\d)\s+(\d\d):(\d\d)\s*\Z',$datum,$regsmatchs)){
            //$regsmatchs contains matches, $regsmatchs[0] the whole match, $regsmatchs[1] up and to $regsmatchs[5] day, month, year, hours and minutes respectively
           // echo "<pre>";print_r($regsmatchs);echo "</pre>";
            if(
                //check if date is a possible date
               !checkdate((int)$regsmatchs[2],(int)$regsmatchs[1],(int)$regsmatchs[3])

            ){
                $errormessage.=' Datum is niet een mogelijke datum - ';
            };
            if(
                //check hours
                !((int)$regsmatchs[4]>=0&& (int)$regsmatchs[4]<=23&& ctype_digit($regsmatchs[4]))

            ){
                $errormessage.=' Uren van 00 t/m 23 - ';
            };
            if(
                //check minutes
                !((int)$regsmatchs[5]>=0&& (int)$regsmatchs[5]<=59&& ctype_digit($regsmatchs[5]))
            ){
                //echo "echte minuten, echte uren, echte datum ";
                //return true;
                $errormessage.=' Minuten van 00 t/m 59 - ';

            }
            if ($errormessage==''){
                return array('status'=>true,'errormessage'=>'');
            }else{
                return array('status'=>false,'errormessage'=>$errormessage);
            }
        }else{
            //return false;
            return array('status'=>false,'errormessage'=>'Datum en tijd zijn niet in het juiste formaat');
        }
}

/**
 * @param $bericht
 * @return bool
 */
function checkbericht($bericht){
    if(is_string($bericht)&&$bericht!=''){//not empty string
        
        return array('status'=>true,'errormessage'=>'');
    }else {
        
        return array('status'=>false,'errormessage'=>'Bericht mag niet leeg zijn');
    }
}

/**
 * @return bool
 */
function checksubmitter(){
    return false;
}


function savePost($postparams, $dbconnection){
    //make params save: remove javascript/html ...
    foreach($postparams as $key=>$value){
        $postparams[$key]=strip_tags($value,"<b><i><br>");
    }
    // prepare and bind should prevent sql injection
    if($prestmt = $dbconnection->prepare("INSERT INTO `form_fields` (`unixtime`,`phptime`,`datum`, `e-mail`, `bericht`) VALUES (UNIX_TIMESTAMP(NOW()),?,?, ?, ?)")){
        $prestmt->bind_param("isss", time(),$postparams['datum'], $postparams['e-mail'], $postparams['bericht']);

        if($prestmt->execute()==true){
            echo "<br />Bericht opgeslagen...<br />";
            return true;
        }else{
            echo "Execute failed: (" . $dbconnection->errno . ") " . $dbconnection->error;
            return false;
        }

    }else{
        echo "Prepare failed: (" . $dbconnection->errno . ") " . $dbconnection->error;
        return false;
    }
}

/**
 * @param $dbconnection
 * @param int $limit
 * @param int $offset
 * @return mixed
 */
function readBerichten($dbconnection,$limit=20,$offset=0){
    $berichtentoshow=array();
    if($berichtenresult = $dbconnection->query("SELECT * FROM `form_fields` ORDER BY `id` DESC LIMIT ".$limit." OFFSET ".$offset." ;")){

        if($berichtenresult->num_rows>0){

            while($rownow =$berichtenresult->fetch_assoc()){

                $berichtentoshow[]=$rownow;
            }
            return $berichtentoshow;
        }else{
            return array('error'=>'no records found');
        }
    }else{
        return array('error'=>"Prepare failed: (" . $dbconnection->errno . ") " . $dbconnection->error);
    }

}

$postitemstatusses = array();

foreach($_POST as $key=>$value){
    $value = trim($value);
    $_POST[$key]=$value;
    if ($key=='e-mail'){$key = 'email';}
    if (function_exists('check'.$key)){//check if a function exists for a $key
        $func = "check".$key;//create reference to that function
        $postitemstatusses[$key]=$func($value);//save function result in array
    }
    unset($key,$value,$func);// for safety
}

//print_r($postitemstatusses);
if((isset($postitemstatusses['email'])&&$postitemstatusses['email']['status']==true)&&
    (isset($postitemstatusses['datum'])&&$postitemstatusses['datum']['status']==true)&&
    (isset($postitemstatusses['bericht'])&&$postitemstatusses['bericht']['status']==true)&&
    isset($postitemstatusses['submitter'])
){// now all validations are positive

    ?>
    <!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="utf-8">
        <title>Form is klaar</title>

    </head>
    <body>
    <?php
        echo time() . "<br />";
        savePost($_POST,$mysqli);
    ?>
    Form correct ingevuld: we hebben 'm bewaard. De nieuwste berichten ziet je hieronder.
    <?php echo "<pre>";print_r(readBerichten($mysqli));echo "</pre>" ;?>
    </body>
    </html>
    <?php

}else {
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>Een webform</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
   <!--  <script src="script.js"></script> -->
    <style>
        .alarm {
            color:red;
        }
    </style>
</head>
<body>
<!-- page content -->
<?php
    echo time() . "<br />";
?>
Een webform<br /><br />
<hr />
Hoewel ik normaal gesproken de tijd van creatie van een record automatisch zou laten gebeuren, <br />
heb ik hier (ook) een veld dat door de gebruiker moet worden ingevuld aangebracht (en moet worden
gevalideerd:-)). <br /><br />
<form accept-charset="utf-8" method="post">
    <label for="datumid">Vul in: Datum en tijd met uren en minuten: DD-MM-JJJJ UU:MM, bv 15-07-2016 22:55</label>
    <br /><input id="datumid" name="datum" type="text" maxlength="" size="37"  value="<?php echo $_POST['datum'];?>" placeholder="DD-MM-JJJJ UU:MM, bv 15-07-2016 22:55"/>
    <?php  echo !($postitemstatusses['datum']['status'])&&isset($_POST['submitter'])?"<span class=\"alarm\">".$postitemstatusses['datum']['errormessage'] .  "</span>":"";?>

    <br /><label for="emailid">emailadres, bv. honk@toet.nl</label>
    <br /><input id="emailid" name="e-mail" type="text" maxlength="" size=""  value="<?php echo $_POST['e-mail'];?>" placeholder="honk@toet.org"/>
    <?php  echo !($postitemstatusses['email']['status'])&&isset($_POST['submitter'])?"<span class=\"alarm\">".$postitemstatusses['email']['errormessage']. "</span>":"";?>

    <br /><label for="berichtid">Hier uw bericht</label>
    <br /><textarea id="berichtid" name="bericht" maxlength="" rows="10" cols="80" placeholder="Typ hier uw bericht" ><?php echo $_POST['bericht'];?></textarea>
    <?php  echo !($postitemstatusses['bericht']['status'])&&isset($_POST['submitter'])?"<span class=\"alarm\">".$postitemstatusses['bericht']['errormessage']. "</span>":"";?>

    <br /><input type="submit" id="submitterid" name="submitter" value="verstuur" />
</form>
<br />
<hr />
*** Een array met de records van de laatste berichten ***<br />
<?php echo "<pre>";print_r(readBerichten($mysqli,5,0));echo "</pre>" ;?>
</body>
</html>
<?php }; ?>
