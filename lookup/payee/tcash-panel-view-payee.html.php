<p>
	<a href="<?php htmlout($_SESSION['returnURL']); ?>"><< Back</a>
</p>

<h2>Payee - <?php ($screenMode == "edit" ? htmlout($screenPayee['payee']) : htmlout('new')); 
				?>: (<?php htmlout($screenMode); ?>)</h2>

<form action="." method="POST">
    <div class="editlabel">New Payee</div>

    <div class="editinput">
        <input type="text" name="newpayee" autofocus
               value="<?php htmlout($screenPayee['payee']); ?>">
        <input type="hidden" name="oldpayee" 
               value="<?php htmlout($screenPayee['payee']); ?>">
    </div>
	
    <div class="editlabel">&nbsp;</div>
    <div class="editinput">
        <button type="submit" name="action" 
                value="<?php htmlout(($screenMode=='edit' ? 'update' : 'create')) ?>">
                Add New Payee</button>
    </div>
    <div class="endfloat"></div>
  
</form>



