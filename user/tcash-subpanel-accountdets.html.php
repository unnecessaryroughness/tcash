
<h1>Edit Account Details (<?php htmlout($_SESSION["userobj"]->getAccountGroup()); ?>):</h1>

<form name="frmAccUpd" action="." method="POST">

    <?php if ($accdets): ?>
            <?php foreach ($accdets as $acc): ?>
                <div class="usereditinput">
                    <div class="userlabel"><?php htmlout($acc->getAccountId()); ?></div>  
                </div>
    
                <div class="usereditinput">
                    <div class="editlabel">
                        <label>Full Name:</label>
                    </div>
                    <div class="editinput">
                        <input type="text" class="maxwidthfield" 
                               name="name_<?php htmlout($acc->getAccountId()); ?>"
                               value="<?php htmlout($acc->getFullName()); ?>">
                    </div>  
                </div>
                <div class="usereditinput">
                    <div class="editlabel">
                        <label>Account Type:</label>
                    </div>
                    <div class="editinput">
                        <select name="type_<?php htmlout($acc->getAccountId()); ?>">
                            <option value="CURRENTACC"
                                <?php 
                                    htmlout($acc->getAccountType() == 'CURRENTACC' ? 'selected' : ''); 
                                    ?>>Current Account</option>
                            <option value="SAVINGSACC" <?php 
                                    htmlout($acc->getAccountType() == 'SAVINGSACC' ? 'selected' : ''); 
                                    ?>>Savings Account</option>
                            <option value="STORECARD" <?php 
                                    htmlout($acc->getAccountType() == 'STORECARD' ? 'selected' : ''); 
                                    ?>>Store Card</option>
                            <option value="CREDITACC" <?php 
                                    htmlout($acc->getAccountType() == 'CREDITACC' ? 'selected' : ''); 
                                    ?>>Credit Card</option>
                        </select>
                    </div>
                </div>
                <div class="usereditinput">
                    <div class="editlabel">
                        <label>Bank Name:</label>
                    </div>
                    <div class="editinput">
                        <input type="text" class="maxwidthfield" 
                               name="bankname_<?php htmlout($acc->getAccountId()); ?>"
                               value="<?php htmlout($acc->getBankName()); ?>">
                    </div>  
                </div>
  
            <?php endforeach; ?>
        <?php endif; ?>
    
        <div class="usereditinput">
            <div class="userlabel">Add New Account:</div>  
        </div>

        <div class="usereditinput">
            <div class="editlabel">
                <label>Account:</label>
            </div>

            <div class="editinput">
                <input type="text" class="maxwidthfield" 
                           name="id_new"
                           value=""
                           placeholder="new account id">
            </div>
        </div>

        <div class="usereditinput">
            <div class="editlabel">
                <label>Full Name:</label>
            </div>
            <div class="editinput">
                <input type="text" class="maxwidthfield" 
                       name="name_new"
                       value=""
                       placeholder="new account name">
            </div>
        </div>

        <div class="usereditinput">
            <div class="editlabel">
                <label>Account Type:</label>
            </div>
            <div class="editinput">
                <select name="type_new">
                    <option value="CURRENTACC">Current Account</option>
                    <option value="SAVINGSACC">Savings Account</option>
                    <option value="STORECARD">Store Card</option>
                    <option value="CREDITACC">Credit Card</option>
                </select>
            </div>
        </div>

        <div class="usereditinput">
            <div class="editlabel">
                <label>Bank Name:</label>
            </div>
            <div class="editinput">
                <input type="text" class="maxwidthfield" 
                       name="bankname_new"
                       value="">
            </div>  
        </div>

    <div class="editinput">
        <button type="button" class="button-cancel"
                id="action" name="action"
                onclick="frmAccUpd.submit();"
                value="cancel">Cancel</button>
        <button type="submit" class="button-submit" 
                id="action" name="action"
                value="updacc">Update</button>
    </div>
</form>

