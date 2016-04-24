<h1>Edit User Details:</h1>

<form name="frmEditUser" action="." method="POST">

    <!-- FULL NAME box -->
    <div class="editinput">
        <div class="editlabel">
            <label>User Full Name:</label>
        </div>
        <div class="editinput">
            <input type="text" name="fullname" id="fullname" autofocus 
                   value="<?php htmlout($_SESSION["userobj"]->getFullName()); ?>">
        </div>
    </div>


    <!-- EMAIL box -->
    <div class="editinput">
        <div class="editlabel">
            <label>User Email:</label>
        </div>
        <div class="editinput">
            <input type="text" name="email" id="email" 
                   value="<?php htmlout($_SESSION["userobj"]->getEmail()); ?>">
        </div>
    </div>

    <!-- PASSWORD box -->
    <div class="editinput">
        <div class="editlabel">
            <label>Current Password:</label>
        </div>
        <div class="editinput">
            <input type="password" name="password" 
                   id="password" value="" required>
        </div>
    </div>

    <!-- PASSWORD box -->
    <div class="editinput">
        <div class="editlabel">
            <label>New Password:</label>
        </div>
        <div class="editinput">
            <input type="password" name="new-password" 
                   id="new-password" value="">
        </div>
    </div>

    <!-- PASSWORD CONFIRMATION box -->
    <div class="editinput">
        <div class="editlabel">
            <label>Confirm New Password:</label>
        </div>
        <div class="editinput">
            <input type="password" name="new-password-confirm" 
                   id="new-password-confirm" value="">
        </div>
    </div>

    <!-- OPERATIONS buttons -->
    <div class="editinput">
        <div class="editlabel">
            <label></label>
        </div>
        <div class="editinput">
            <button type="button" class="button-cancel" 
                    name="action" id="button-cancel" 
                    onclick="frmEditUser.submit();"
                    value="cancel">Cancel</button>

            <button type="submit" class="button-submit" 
                    name="action" id="button-submit" 
                    value="updateuser">Update Details</button>
        </div>
    </div>

</form>
