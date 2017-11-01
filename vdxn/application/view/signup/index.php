<div class="container">
  <div class="col-lg-6 col-md-10" style="background-color: #1abc9c; min-height: 100%; padding: 50px;">
    <h1 style="color: #ffffff">Sign Up</h1>
    <div class="login-form">
      <div class="form-group">
        <form action="<?php echo URL; ?>signup/submitForm" method="POST">
          <div class="form-group">
            <input type="text" name="username" class="form-control login-field" value="" placeholder="Username" id="login-name" autocomplete="off" style="background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;">
            <label class="login-field-icon fui-user" for="login-name"></label>
          </div>

          <div class="form-group">
            <input type="text" name="email" class="form-control login-field" value="" placeholder="Email" id="login-name" autocomplete="off" style="background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;">
            <label class="login-field-icon" for="login-name"></label>
          </div>

          <div class="form-group">
            <input type="text" name="firstName" class="form-control login-field" value="" placeholder="First name" id="login-name" autocomplete="off" style="background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
            <label class="login-field-icon" for="login-name"></label>
          </div>

          <div class="form-group">
            <input type="text" name="lastName" class="form-control login-field" value="" placeholder="Last name" id="login-name" autocomplete="off" style="background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;">
            <label class="login-field-icon" for="login-name"></label>
          </div>

          <div class="form-group">
            <input type="text" name="contactNumber" class="form-control login-field" value="" placeholder="Contact no." id="login-name" autocomplete="off" style="background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;">
            <label class="login-field-icon" for="login-name"></label>
          </div>

          <div class="form-group">
            <input type="password" name="password" class="form-control login-field" value="" placeholder="Password" id="login-pass" autocomplete="off" style="background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
            <label class="login-field-icon fui-lock" for="login-pass"></label>
          </div>

          <div class="form-group">
            <input type="password" name="password2" class="form-control login-field" value="" placeholder="Confirm Password" id="login-pass" autocomplete="off" style="background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
            <label class="login-field-icon fui-lock" for="login-pass"></label>
          </div>

          <input class="btn btn-primary btn-lg btn-block" type="submit" value="Create Account" />
        </form>
      </div>
    </div>
  </div>
</div>
