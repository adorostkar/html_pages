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
    // Get response from google to see if the captcha is correct. Not neccessary since
    // The submit button is controled by the google captcha.
    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
    // Decode the recieved JSON to see the value
    $responseKeys = json_decode($response,true);
    if(intval($responseKeys["success"]) !== 1) {
        die ("The captcha is not entered. Please go back and try again.");
    }   

    // Get the fields from the forms
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $org = $_POST['organization'];
    // If has_presentation is set, get its value
    if(isset($_POST['has_presentation'])){
        $hasp = $_POST['has_presentation'];
    }
    $abstract = $_POST['abstract'];
    $abstitle = $_POST['title'];
    $from = 'TDB16';
    $to = 'ali.dorostkar@it.uu.se';
    
    // This is specifically for the Safari browser. It doesn't understand the required field
    if($name === '' || $email === '' || $org === ''){
        die('<p style="color:red;text-align:center;margin:auto;font-size: 24px;">Name, email and organization can\'t be empty. Please try again!</p>');
    }  
    
    // Check to see if the abstract and the file area not be empty at the same time.
    if(isset($hasp) && $hasp == 'on'){
        if($_FILES['file']['error'] === UPLOAD_ERR_NO_FILE && $abstract === '' ){
            die('<p style="color:red;text-align:center;margin:auto;font-size: 24px;">No abstract or file is given! Please make a correction.</p>');
        }   
    }
    
    // Give different titles if they have abstract or not
    if(isset($hasp) && $hasp == 'on'){
        $subject = 'TDB16 With talk';
    }else{        
        $subject = 'TDB16';
    }   

    // Construct the body of the message
    $body = "From: $name\n\n E-Mail: $email\n\n Organization: $org\n\n Wants to present: ";
    if(isset($hasp) && $hasp == 'on'){
        $body .= "YES\n\n Abstract Title:\n $abstitle";
        $body .= "\n\n Abstract:\n $abstract";
    }else{
        $body .= "NO";
    }

    $attachment = '';
    if($_FILES['file']['tmp_name'] != ''){
        $attachment = chunk_split(base64_encode(file_get_contents($_FILES['file']['tmp_name'])));
    }
    $filename = $_FILES['file']['name'];

    $boundary =md5(date('r', time()));

    $headers = "From: $from\r\nReply-To: $from";
    $headers .= "\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"_1_$boundary\"";

    // Create the body of the email with the correct attachement.
    $body="This is a multi-part message in MIME format.

--_1_$boundary
Content-Type: multipart/alternative; boundary=\"_2_$boundary\"

--_2_$boundary
Content-Type: text/plain; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

$body

--_2_$boundary--";

if($_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE){
$body .= "\n--_1_$boundary
Content-Type: application/octet-stream; name=\"$filename\" 
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

$attachment
--_1_$boundary--";
}

    // Send email to me
    if (mail ($to, $subject, $body, $headers)){
        echo '<p>Your registration has been submitted!</p>';
    } else {
        echo '<p>Something went wrong, please try again!</p>';
    }
?>
