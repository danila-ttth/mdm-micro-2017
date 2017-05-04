<?php session_start(); ?>
<?php

  $error = $_SESSION['msg'];
  $success = $_SESSION['success'];
  
  //echo $form_submitted."<h1 style='color:red;'>1</h1>";
  
  function redirect_to(){
	  header("Location: index.php?error=true#enter");
	  exit;
  }

  if(isset($_POST['submit'])) {
    $fname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
    $lname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $u_email = str_replace('@', '%40', $email);

    if (!isset($_POST['terms'])) {
      $_SESSION['msg'] = "Please agree to terms";
      redirect_to();
   } else {
     $c_url = 'http://mdm-catalog-service.web.xm/marketing/trendContest/createGameParticipant';
     $c_arguments = '_format=json&trendGameParticipant%5Bmarket%5D=UK';
     $c_arguments .= '&trendGameParticipant%5Blanguage%5D=en';
     $c_arguments .= '&trendGameParticipant%5BgameName%5D=UKGAME2017';
     $c_arguments .= '&trendGameParticipant%5Bcivility%5D=1';
     $c_arguments .= '&trendGameParticipant%5BfirstName%5D=' . $fname;
     $c_arguments .= '&trendGameParticipant%5Bname%5D=' . $lname;
     $c_arguments .= '&trendGameParticipant%5Bemail%5D=' . $u_email;
     $c_arguments .= '&trendGameParticipant%5Boptin%5D=yes';
     $ch = curl_init();
     $curl_parameters = array(
         CURLOPT_HTTPHEADER => array(
             'Accept: application/json',
             'Content-Type: application/x-www-form-urlencoded'
         ),
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_URL => $c_url,
         CURLOPT_POSTFIELDS => $c_arguments,
         CURLOPT_POST => true,
         CURLOPT_RETURNTRANSFER => 1,
         CURLOPT_VERBOSE => 1,
         CURLOPT_HEADER => 1
     );
     curl_setopt_array($ch, $curl_parameters);
		die(var_dump($curl_parameters));
     $response = curl_exec($ch);
     $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
     $body = substr($response, $header_size);
     $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
     curl_close($ch);

     $decoded = json_decode($body, true);
//	  die(var_dump($decoded));
     if (isset($decoded['status']) && $decoded['status'] == false) {
       if ($return_code == 409) {
         $_SESSION['msg'] = "Email already exists";
         redirect_to();
       } else {
         $_SESSION['msg'] = "Please complete all fields";
         redirect_to();
       }
     } else {
       $_SESSION['success'] = "true";
       header("Location: index.php");
       exit;
     }
   }
	}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <title>Maisons du Monde Competition</title>
  <link rel="stylesheet" href="css/app.css">
  <!--[if gte IE 9]
  <style type="text/css">
  .gradient {
  filter: none;
  }
  </style>
  <![endif]-->
</head>
<body <?php if(isset($success)) : ?>class="noscroll"<?php endif; ?>><!-- no scroll is added on form completion -->

<section id="hero">
  <span class="mdm-logo"></span>
  <span class="gradient"><!-- empty --></span>
  <h1 class="text-center">Win your very own room makeover worth £2,500</h1>
  <a href="#details" class="arrow" rel="relativeanchor"><!-- empty --></a>
</section>

<article id="details">
  <div class="row">
    <div class="small-12 columns text-center">
      <h2>Create the room of your dreams</h2>
      <p>To celebrate the launch of our new 2017 collection, we’re giving you the chance to win the ultimate room makeover. One lucky winner will receive all the furniture and decor to create their perfect room. They'll also receive personal guidance from one of our in-house stylists to help bring their vision to life. Not only that, but four runners up will win £250 to spend on maisonsdumonde.com</p>

      <ul class="icons">
        <li><h4>Living Room</h4></li>
        <li><h4>Bedroom</h4></li>
        <li><!-- empty --></li>
        <li><h4>Study</h4></li>
        <li><h4>Dining Room</h4></li>
      </ul>
    </div>
  </div>
  <a href="#enter" class="arrow black" rel="relativeanchor"><!-- empty --></a>
</article>
<?php if($success != "true") : ?>
<section id="enter">
  <div class="row fw">
    <div class="small-12 columns">
      <p class="text-center thin">The winners will be chosen at random after the competition closes.<br />
        To enter simply submit the form below before 30/06/2017</p>
      <?php if(!empty($error)){ ?><div class="error-container phperror"><?php echo $error; ?></div><?php } ?>
      <form data-abide novalidate method="post" action="index.php">
        <div class="row expanded">
          <div class="small-12 large-6 columns">
            <label>
              <input type="text" name="firstname" placeholder="First Name" required pattern="text">
            </label>
            <span class="form-error">
            Please fill in your first name.
            </span>
          </div>
          <div class="small-12 large-6 columns">
            <label>
              <input type="text" name="lastname" placeholder="Last Name" required pattern="text">
            </label>
            <span class="form-error">
            Please fill in your last name.
            </span>
          </div>
        </div>
        <div class="row expanded">
          <div class="small-12 columns">
            <label>
              <input type="email" name="email" placeholder="Email" required pattern="email">
            </label>
            <span class="form-error">
            Please check and enter a valid email address.
            </span>
          </div>
        </div>

        <div class="row expanded checkers">
          <fieldset class="small-12 columns text-center">
            <div class="check-wrapper">
              <input id="checkbox1" class="icheck" type="checkbox" name="terms" required><label for="checkbox1">I agree to the terms
              & conditions</label>
            </div>
          </fieldset>
        </div>
        <div class="row expanded">
          <fieldset class="xxlarge-12 columns text-center">
            <div class="button submit">
              <input type="submit" name="submit" value="overlay" onclick="mdm_events (3, this , 'track-event', {'event_category':'Comp_2017', 'event_action':'Form_Submit', 'event_label':'comp_form'})" />
              <span>Submit</span>
            </div>
          </fieldset>
        </div>
      </form>

      <p class="text-center thin">
        <small><a href="terms.html" title="I agree to the terms & conditions">Terms & Conditions</a></small>
      </p>
    </div>
  </div>
</section>
<?php endif; ?>
<?php if($_SESSION[success] == "true") : ?>
<aside id="overlay" class="overlay text-center" data-toggler=".visible">
  <div class="frame">
    <h2>Thank you for entering!</h2>
    <p class="thin">Share the excitement with your friends and family and increase your chances of winning</p>
    <p class="thin">Share on social media:</p>
    <ul class="socials">
      <li><a class="fc"
             href="https://www.facebook.com/dialog/feed?app_id=184683071273&link=%20http%3A%2F%2Fwww.maisonsdumonde.com%2FUK%2Fen%2Fcompetition%2Findex.php%3Futm_source%3Dfacebook%26utm_medium%3Dreferral%26utm_campaign%3Dfbsharelink&picture=http%3A%2F%2Ftomorrowtth.com%2Fclients%2Fmdm%2Flp-2017%2Fimg%2Ffacebook-post.jpg&name=Win%20your%20very%20own%20room%20makeover%20worth%20%C2%A32500&caption=%20&description=Create%20the%20room%20of%20your%20dreams.%20Enter%20to%20WIN%20now%20%23mdmcomp&redirect_uri=http%3A%2F%2Fwww.facebook.com%2F"
             title="share on facebook" target="_blank" onclick="mdm_events (3, this , 'track-event', {'event_category':'Comp_2017', 'event_action':'FB_Click', 'event_label':'facebook'})">
        <span class="facebook"><!-- empty --></span>
      </a></li>
      <li><a class="tw"
             href="http://twitter.com/intent/tweet?text=%20I%E2%80%99ve%20entered%20the%20%23MaisonsduMonde%20room%20makeover%20%23competition%20-%20you%20can%20enter%20here%20too!%20https%3A%2F%2Fgoo.gl%2FsKfDHa"
             title="share on twitter" target="_blank" onclick="mdm_events (3, this , 'track-event', {'event_category':'Comp_2017', 'event_action':'TW_Click', 'event_label':'twitter'})">
        <span class="twitter"><!-- empty --></span>
      </a></li>
    </ul>
    <p class="thin"><a href="http://www.maisonsdumonde.com/UK/en" title="maisons du monde">www.maisonsdumonde.com</a></p>
  </div>
</aside>
<?php 
	endif;
	session_destroy();
?>

<script src="js/jquery.min.js"></script>
<script src="js/foundation.min.js"></script>
<script src="js/app.js"></script>
<script src="js/icheck.min.js"></script>
<script>
jQuery(document).ready(function($) {
	if (window.location.hash) {     
		$('html,body').animate({scrollTop:$(this.hash).offset().top}, 1000);
	};
});

$(document).ready(function() {
    $('a[rel="relativeanchor"]').click(function(){
      $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top
      }, 500);
      return false;
    });
  });
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-324743-10', 'auto');
  ga('send', 'pageview');

</script>

<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	  n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	  document,'script','https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '166520587063262');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
			   src="https://www.facebook.com/tr?id=166520587063262&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->

</body>
</html>
