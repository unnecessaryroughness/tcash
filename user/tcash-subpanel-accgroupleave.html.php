<h2>Confirm Leave Account Group - <?php htmlout($leavegroup); ?></h2>

<p class="p-margin-tb-1em">
    Are you sure you want to leave the account group <?php htmlout($leavegroup); ?>?
</p>

<form action="." method="post">
    
    <input type="hidden" name="leavegroup" value="<?php htmlout($leavegroup); ?>">
    
    <div class="editinput">
    <button type="submit" class="button-cancel" name="action" value="cancel">Cancel</button>
    <button type="submit" class="button-submit" name="action" value="leavegrp">Confirm</button>
    </div>
</form>