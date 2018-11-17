<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */
?>

<div class="row">
    <div class="col-md-4 offset-md-4">
        <div class="container-fluid container comad-container login-container">
            <h3>Login</h3>

            <form method="post" action="/login/login">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="email">E-Mail:</label>
                            <input type="email" name="email" id="email" placeholder="email" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" name="password" id="password" placeholder="password"
                                   class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="submit" value="login" class="btn btn-block btn-primary">
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

