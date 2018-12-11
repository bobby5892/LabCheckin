<?php
$view = <<<SOMEHTML
<section>
<form action="/admin/login" method="post">
<span>Email Address</span>
<input type="text" name="emailAddress">
<span>Password</span>
<input tye="password" name="password">
<input type="submit" value="login">
</form>
</section>
SOMEHTML;
return $view;