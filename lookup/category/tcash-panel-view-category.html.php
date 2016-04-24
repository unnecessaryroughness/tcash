<p>
	<a href="<?php htmlout($_SESSION['returnURL']); ?>"><< Back</a>
</p>

<h2>Category - <?php ($screenMode == "edit" ? htmlout($screenCategory['id']) : htmlout('new')); 
				?>: (<?php htmlout($screenMode); ?>)</h2>

<form action="." method="POST">
    <div class="editlabel">New Category</div>

    <div class="editinput">
        <input type="text" name="newcategory" autofocus
               value="<?php htmlout($screenCategory['id']); ?>">
		<input type="hidden" name="oldcategory" 
               value="<?php htmlout($screenCategory['id']); ?>">
    </div>
    
    <div class="editlabel">&nbsp;</div>
    <div class="editinput">
       <button type="submit" name="action" 
               value="<?php htmlout(($screenMode=='edit' ? 'update' : 'create')) ?>">
               Add New Category</button>
    </div>
    <div class="endfloat"></div>
    
</form>



