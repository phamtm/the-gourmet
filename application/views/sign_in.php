<div class="gourmet-title">The Gourmet</div>

<div class="center-holder">

  <div id="gallery">

    <div id="slides">

      <div class="slide"><a href="/index.php/recipe/browse"><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/macbook.jpg" width="600" height="400" alt="side" /></a></div>
      <div class="slide"><a href="/index.php/recipe/browse"><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/iphone.jpg" width="600" height="400" alt="side" /></a></div>
      <div class="slide"><a href="/index.php/recipe/browse"><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/imac.jpg" width="600" height="400" alt="side" /></a></div>

    </div>

    <div id="menu">

      <ul>
        <li class="menuItem"><a href=""><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/thumb_macbook.png" alt="thumbnail" /></a></li>
        <li class="menuItem"><a href=""><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/thumb_iphone.png" alt="thumbnail" /></a></li>
        <li class="menuItem"><a href=""><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/thumb_imac.png" alt="thumbnail" /></a></li>
      </ul>

    </div>

  </div>

  <div class="form-center" id="form-signin">
    <h1>Sign In</h1>
    <span>Share your recipes with the world</span>
    <fieldset>
      <form action="<?php  echo base_url();?>index.php/user/validate_login/" method="post" accept-charset="utf-8" >
        <input type="text" name="email_address" placeholder="Email address" value="<?php echo set_value('email_address'); ?>">
        <input type="password" name="password" placeholder="Password">
        <?php
        if (isset($id_pwd_not_matched) and $id_pwd_not_matched)
        {
          $id_pwd_not_matched = TRUE;
          echo "<span style='display:block; color:red;'>Username and password do not match</span>";
        }
        else
          echo "<br/>";
        ?>
        <span style="display:block;">
          <button class="cupid-green" type="submit">Log in</button>
          <a href="<?php echo base_url(); ?>index.php/user/sign_up" class="signin-right-button">Sign Up</a><br>
        </span>
      </form>
    </fieldset>
    <div style="margin-top:10px;">
      <a href="<?php echo base_url(); ?>index.php/user/facebookLogin">
        <img src="<?php echo base_url() ?>style/img/facebook.png" width="200" height="24" alt="Log in with facebook">
      </a>
    </div>
  </div>

</div>