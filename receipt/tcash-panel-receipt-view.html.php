
<h2>Add Quick Receipt:</h2>
<br>

<form action="." method="POST">
	<input type="hidden" id="returnURL" name="returnURL" value="">
	<input type="hidden" name="txnid" value="<?php htmlout($screenTxn->getId()); ?>">
    <input type="hidden" name="txntype" value="Payment">
    <input type="hidden" name="date" id="date" value="<?php htmlout($screenTxn->getDate()); ?>">

	<div class="layoutcol1">

		<!-- Account -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="accountid">Account</label></div>
			<div class="editinput">
                <fieldset class="fldInput">
					<?php foreach($acclist as $acct): ?>
                        <input type="radio" class="rdoInput" name="accountid" 
                               value="<?php htmlout($acct->getName()); ?>" 
                               id="rdoCA_<?php htmlout($acct->getName()); ?>"
                               autofocus required>
                        <label for="rdoCA_<?php htmlout($acct->getName()); ?>">
                            <?php htmlout($acct->getFullName()); ?>
                        </label>
                        <br>
					<?php endforeach; ?>
                </fieldset>
			</div>
		</div>	


		<!-- Payee -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label id="lbl_payee" for="payee">Pay To</label></div>
			<div class="editinput">
				<select name="payee" id="payee" required>
					<?php foreach($payeelist as $pay): ?>
						<option><?php htmlout($pay["payee"]); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>	

		<!-- Category -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="category">Category</label></div>
			<div class="editinput">
				<select name="category" id="category" required>
					<?php foreach($catlist as $cat): ?>
						<option><?php htmlout($cat["id"]); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>	

		<!-- Amount -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="txnamount">Amount</amount></div>
			<div class="editinput">
                <input type="text" class="textfield amtfield" 
                       name="txnamount" id="txnamount" 
                       pattern="-{0,1}[0-9]{1,6}\.[0-9]{2,2}"
                       required>
			</div>
		</div>	

        
        <!-- Reduce Placeholder -->
            <div class="inputrow endfloat">
                <div class="editlabel">
                    <label>Reduce Placeholder</label>
                </div>
                <div class="editinput">
                    <select id="reduce" name="reduce" class="narrow">
                        <option id="opt-none" value="">None</option>
                        <?php foreach($pholders as $ph): ?>
                            <option id="opt-<?php htmlout($ph["id"]); ?>" 
                                    value="<?php htmlout($ph["id"]); ?>">
                                        <?php htmlout($ph["payee"]); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="reduceamt" disabled 
                           name="reduceamt" class="textfield narrowfield"
                           pattern="-{0,1}[0-9]{1,6}\.[0-9]{2,2}"
                           value="0.00">
                </div>
            </div>

        
		<!-- Operations -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label>Operations</label></div>
			<div class="editinput">
                <button type="submit" name="action" value="add">Add Receipt</button>
			</div>
		</div>	
	</div>

    <div class="endfloat"></div>

</form>

<script src="tcash-panel-receipt-view.js"></script>

