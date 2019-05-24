<?php
?>
<div class="um um-register um-2398 uimob500" style="opacity: 1;">

    <div class="um-form">

        <form method="post" action="">
            <?php
            if(!empty($_GET['request_id'])){
                echo "<input type='hidden' name='request_id' value='{$_GET['request_id']}'>";
            }
            ?>
            <input type="hidden" name="action" value="register-join">
            <div class="um-row _um_row_1 " style="margin: 0 0 30px 0;"><div class="um-col-1"><div class="um-field um-field-user_login um-field-text um-field-type_text" data-key="user_login"><div class="um-field-label"><label for="user_login-2398">Username</label><div class="um-clear"></div></div><div class="um-field-area"><input autocomplete="off" class="um-form-field valid " type="text" name="user_login-2398" id="user_login-2398" value="" placeholder="" data-validate="unique_username" data-key="user_login">

                        </div></div><div class="um-field um-field-first_name um-field-text um-field-type_text" data-key="first_name"><div class="um-field-label"><label for="first_name-2398">First Name</label><div class="um-clear"></div></div><div class="um-field-area"><input autocomplete="off" class="um-form-field valid " type="text" name="first_name-2398" id="first_name-2398" value="" placeholder="" data-validate="" data-key="first_name">

                        </div></div><div class="um-field um-field-last_name um-field-text um-field-type_text" data-key="last_name"><div class="um-field-label"><label for="last_name-2398">Last Name</label><div class="um-clear"></div></div><div class="um-field-area"><input autocomplete="off" class="um-form-field valid " type="text" name="last_name-2398" id="last_name-2398" value="" placeholder="" data-validate="" data-key="last_name">

                        </div></div><div class="um-field um-field-user_email um-field-text um-field-type_text" data-key="user_email"><div class="um-field-label"><label for="user_email-2398">E-mail Address</label><div class="um-clear"></div></div><div class="um-field-area"><input autocomplete="off" class="um-form-field valid " type="text" name="user_email-2398" id="user_email-2398" value="" placeholder="" data-validate="unique_email" data-key="user_email">

                        </div></div><div class="um-field um-field-user_password um-field-password um-field-type_password" data-key="user_password"><div class="um-field-label"><label for="user_password-2398">Password</label><div class="um-clear"></div></div><div class="um-field-area"><input class="um-form-field valid " type="password" name="user_password-2398" id="user_password-2398" value="" placeholder="" data-validate="" data-key="user_password">

                        </div></div><div class="um-field um-field-user_password um-field-password um-field-type_password" data-key="confirm_user_password"><div class="um-field-label"><label for="confirm_user_password-2398">Confirm Password</label><div class="um-clear"></div></div><div class="um-field-area"><input class="um-form-field valid " type="password" name="confirm_user_password-2398" id="confirm_user_password-2398" value="" placeholder="" data-validate="" data-key="confirm_user_password">

                        </div></div></div></div>		<input type="hidden" name="form_id" id="form_id_2398" value="2398">

            <p class="request_name">
                <label for="request_2398">Only fill in if you are not human</label>
                <input type="text" name="request" id="request_2398" class="input" value="" size="25" autocomplete="off">
            </p>
            <?php wp_nonce_field('register-join'); ?>
            <div class="um-col-alt">


                <div class="um-left um-half">
                    <input type="submit" value="Register" class="um-button" id="um-submit-btn">
                </div>
                <div class="um-right um-half">
                    <a href="<?=home_url('/login')?>" class="um-button um-alt">
                        Login				</a>
                </div>


                <div class="um-clear"></div>

            </div>


        </form>

    </div>

</div>

