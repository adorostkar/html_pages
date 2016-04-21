<?php
    // your secret key
    $secretKey = "6Ld-wx0TAAAAAFQdjCovCE1yjWC-HhBwC3NgdSdi";
    if(isset($_POST['g-recaptcha-response'])){
        $captcha=$_POST['g-recaptcha-response'];
    }
    //if(!$captcha){
        //die('<h2>Please check the the captcha form.</h2>');
    //    exit;
    //}
    $ip = $_SERVER['REMOTE_ADDR'];
    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
    $responseKeys = json_decode($response,true);
    if(intval($responseKeys["success"]) !== 1) {
        die ("The captcha is not entered. Please go back and try again.");
    }

    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $org = $_POST['organization'];
    $hasp = $_POST['has_presentation'];
    $abstract = $_POST['abstract'];
    $from = 'From: TDB16';
    $to = 'ali.dorostkar@it.uu.se';

    if($name === '' || $email === '' or $org === ''){
        die('<p style="color:red;text-align:center;margin:auto;font-size: 24px;">Name, email and organization can\'t be empty. Please try again!</p>');
    }
    if(isset($hasp) && $hasp == 'on'){
        $subject = 'TDB16 With talk';
    }else{  
        $subject = 'TDB16';
    }

    $body = "From: $name\n\n E-Mail: $email\n\n Organization: $org\n\n Wants to present: ";
    if(isset($hasp) && $hasp == 'on'){
        $body .= "YES\n\n Abstract:\n $abstract";
    }else{
        $body .= "NO";
    }

    if (mail ($to, $subject, $body, $from)){
        echo '<p>Your registration has been submitted!</p>';
    } else {
        echo '<p>Something went wrong, please try again!</p>';
    }
?>
