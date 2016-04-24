<h2>Login:</h2>

<br>


	<div class="layoutcol50pc">

        <form id="frmLogin" action="." method="POST">
            <label id="login-register-title">Existing User Login</label>
        
            <!-- Error Text -->
            <?php if ($errl): ?>
                <div class="inputrow endfloat">
                   <div class="editlabel"><label></label></div> 
                   <div class="editinput registererror"><label><?php htmlout($errl); ?></label></div> 
                </div>
            <?php endif; ?>

            <!-- User Name -->
            <div class="inputrow endfloat">
                <div class="editlabel"><label for="username">User Name</label></div>
                <div class="editinput">
                    <input type="text" id="username" name="username"  
                           class="textfield" autofocus required
                           pattern="^[a-z|A-Z|0-9]{4,30}"
                           value="">
                </div>
            </div>	

            <!-- Password -->
            <div class="inputrow endfloat">
                <div class="editlabel"><label for="password">Password</label></div>
                <div class="editinput">
                    <input type="password" id="password" name="password"  
                           class="textfield" required
                           pattern="^[a-z|0-9|A-Z|\W]{4,30}"
                           value="">
                </div>
            </div>	            

            <!-- Operations -->
            <div class="inputrow endfloat">
                <div class="editlabel"><label></label></div>
                <div class="editinput">
                    <button type="submit" name="action" value="login">Login</button>
                </div>
            </div>	
        </form>
    </div>

    <div class="layoutcol50pc">
        <label id="login-register-title">New User Registration</label>

        <form id="frmRegister" action="." method="POST">

            <!-- Error Text -->
            <?php if ($errr): ?>
                <div class="inputrow endfloat">
                   <div class="editlabel"><label></label></div> 
                   <div class="editinput registererror"><label><?php htmlout($errr); ?></label></div> 
                </div>
            <?php endif; ?>

            
            <!-- User Name -->
            <div class="inputrow endfloat">
            <div class="editlabel"><label for="registername">User Name</label></div>
            <div class="editinput">
                <input type="text" id="username" name="username"
                       class="textfield" required
                       pattern="^[a-z|A-Z|0-9]{4,30}"
                       value="">
            </div>

            <!-- Full Name -->
            <div class="inputrow endfloat">
            <div class="editlabel"><label for="fullname">Full Name</label></div>
            <div class="editinput">
                <input type="text" id="fullname" name="fullname"
                       class="textfield" required
                       pattern="^[\w|\W]{4,100}"
                       value="">
            </div>

            <!-- Email Address -->
            <div class="inputrow endfloat">
            <div class="editlabel"><label for="email">Email Address</label></div>
            <div class="editinput">
                <input type="text" id="email" name="email"
                       class="textfield" required
                       pattern="^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$"
                       value="">
            </div>

            <!-- Password -->
            <div class="inputrow endfloat">
                <div class="editlabel"><label for="password">Password</label></div>
                <div class="editinput">
                    <input type="password" id="password" name="password"  
                           class="textfield" required
                           pattern="^[a-z|0-9|A-Z|\W]{4,30}"
                           value="">
                </div>
            </div>	

            <!-- Confirm Password -->
            <div class="inputrow endfloat">
                <div class="editlabel"><label for="confirmpassword">Confirm Password</label></div>
                <div class="editinput">
                    <input type="password" id="confirmpassword" name="confirmpassword"  
                           class="textfield" required
                           pattern="^[a-z|0-9|A-Z|\W]{4,30}"
                           value="">
                </div>
            </div>	
                                                
            <!-- Operations -->
            <div class="inputrow endfloat">
                <div class="editlabel"><label></label></div>
                <div class="editinput">
                    <button type="submit" name="action" value="register">Register</button>
                </div>
            </div>	
            
        </form>
            
    </div>
    
    

