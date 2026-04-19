<?php
require __DIR__ . '/bootstrap.php';
$success='';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!hs_csrf_validate()){$error='Invalid form session.';} else {
    $name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$message=trim($_POST['message']??'');
    if($name===''||$email===''||$message===''){$error='All fields are required.';} else {
      hs_log_event('info','Contact form submitted',['name'=>$name,'email'=>$email]);
      $success='Thanks, your message was received.';
    }
  }
}
?>
<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>Contact</title><link rel='stylesheet' href='<?= hs_base_url('assets/css/style.css') ?>'><?= hs_pwa_head_tags() ?><script defer src='<?= hs_base_url('assets/js/pwa.js') ?>'></script></head><body><main class='page-bg'><div class='container'><section class='auth-card' style='max-width:700px;margin:auto;'><h1>Contact Us</h1><?php if($error):?><div class='error-msg'><?= htmlspecialchars($error) ?></div><?php endif; ?><?php if($success):?><div class='success-msg'><?= htmlspecialchars($success) ?></div><?php endif; ?><form method='post'><?= hs_csrf_input() ?><div class='auth-field'><label>Name</label><input name='name' required></div><div class='auth-field'><label>Email</label><input type='email' name='email' required></div><div class='auth-field'><label>Message</label><textarea name='message' style='min-height:120px;border:1px solid var(--border);border-radius:12px;padding:12px;' required></textarea></div><button class='btn btn-primary' type='submit'>Send Message</button></form></section></div></main></body></html>
