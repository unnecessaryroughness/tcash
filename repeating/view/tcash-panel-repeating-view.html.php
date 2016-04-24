<p class="backlink">
	<a href="/tcash/repeating/"><< Back</a>
</p>

<h2>Repeating Transactions - Add/Edit:</h2>

<form name="rptform" action=".?acc=<?php htmlout($selectedAcc); ?>" method="POST">

    <input type="hidden" id="returnURL" name="returnURL" value="">
	<input type="hidden" name="txnid" value="<?php htmlout($screenTxn->getId()); ?>">
    <input type="hidden" name="refdata" id="refdata" value="">

    <div>
        <!-- ID -->
        <div class="inputrow endfloat">
            <div class="editlabel"><label>Id</label></div>
            <div class="editinput">
                <label class="trailinglabel"><?php htmlout($screenTxn->getId()); ?></label>
            </div>
        </div>
  
		<!-- Transaction Type -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="txntype">Transaction Type</label></div>
			<div class="editinput">
				<select name="txntype" id="txntype" autofocus required>
					<option <?php htmlout($screenTxn->getTxnType() == "Payment" ? "selected" : ""); ?>>Payment</option>
					<option <?php htmlout($screenTxn->getTxnType() == "Deposit" ? "selected" : ""); ?>>Deposit</option>
					<option <?php htmlout($screenTxn->getTxnType() == "Transfer" ? "selected" : ""); ?>>Transfer</option>
				</select>
			</div>
		</div>

        
		<!-- Account -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="accountid">Account</label></div>
			<div class="editinput">
				<select name="accountid" id="accountid" 
                        value="<?php htmlout($screenTxn->getAccountId()); ?>"
                        required>
					<?php foreach($acclist as $acct): ?>
						<option <?php if($acct->getName() == $screenTxn->getAccountId()) {
										htmlout(" selected "); } ?> ><?php htmlout($acct->getName()); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>	
    
		<!-- Payee -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label id="lbl_payee" for="payee">Pay To</label></div>
			<div class="editinput">
				<select name="payee" id="payee">
					<?php foreach($payeelist as $pay): ?>
						<option <?php if($pay["payee"] == $screenTxn->getPayee()) { 
                            htmlout(" selected "); } ?>><?php htmlout($pay["payee"]); ?></option>
					<?php endforeach; ?>
				</select>
                <select name="txfaccount" id="txfaccount">
                    <option></option>
                    <?php foreach($txflist as $txfacc): ?>
                        <option <?php if($txfacc["payee"] == $screenTxn->getPayee()) { 
                            htmlout(" selected "); } ?>><?php htmlout($txfacc["payee"]); ?></option>
                    <?php endforeach; ?>
                </select>
			</div>
		</div>	

		<div class="inputrow endfloat">
			<div class="editlabel">&nbsp;</div>
			<div class="editinput">
				<button type="button" class="last" 
                        name="addpayee" id="addpayee"
                        value="addpayee">Add Payee</button>
			</div>
		</div>	        
        

		<!-- Category -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="category">Category</label></div>
			<div class="editinput">
				<select name="category" id="category" required>
					<?php foreach($catlist as $cat): ?>
						<option <?php if($cat["id"] == $screenTxn->getCategory()) { 
                            htmlout(" selected "); } ?>><?php htmlout($cat["id"]); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>	


		<div class="inputrow endfloat">
			<div class="editlabel">&nbsp;</div>
			<div class="editinput">
				<button type="button" class="last" 
                        name="addcategory" id="addcategory" 
                        value="addcategory">Add Category</button>
			</div>
		</div>	        
        
        
		<!-- Notes -->
		<div class="inputrowtall endfloat">
			<div class="editlabel"><label for="notes">Notes</label></div>
			<div class="editinput">
				<textarea name="notes" id="notes"><?php htmlout($screenTxn->getNotes()); ?></textarea>
			</div>
		</div>	
    
    </div>
    
  		<!-- Placeholder -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="isplaceholder">Placeholder?</label></div>
			<div class="editinput">
				<input type="checkbox" name="isplaceholder" id="isplaceholder" value="1" 
							<?php if ($screenTxn->getIsPlaceholder()) { htmlout(" checked "); } ?>>
			</div>
		</div>	

		<!-- Amount -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="txnamount">Amount</amount></div>
			<div class="editinput">                
                <td><input type="text" class="textfield amtfield" 
                           name="txnamount" id="txnamount" 
                           <?php if ($screenTxn->getTxnType() == "Payment"): ?>
                                value="<?php htmlout($screenTxn->getDisplayDr()); ?>" 
                           <?php elseif ($screenTxn->getTxnType() == "Deposit"): ?>
                                value="<?php htmlout($screenTxn->getDisplayCr()); ?>"
                           <?php elseif ($screenTxn->getTxnType() == "Transfer"): ?>
                                value="<?php htmlout($screenTxn->getAmountCr()); ?>"
                           <?php endif; ?>
                           pattern="-{0,1}[0-9]{1,6}\.[0-9]{2,2}"
                           required>
                </td>
			</div>
		</div>	

    
		<!-- Next Date -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="nextday">Next Due Date</label></div>
			<div class="editinput">
				<input type="hidden" name="nextdate" id="nextdate" 
                       value="<?php htmlout($screenTxn->getNextDate()); ?>">
				
                <select class="date" name="nextday" id="nextday">
					<?php for($i=01; $i<=31; $i++): ?>
						<option <?php if ($i == $screenTxn->getNextDateDay()) { htmlout(" selected "); }?>
								><?php htmlout(str_pad($i, 2, "0", STR_PAD_LEFT)); ?></option>
					<?php endfor; ?>
				</select>
				-
				<select class="date" name="nextmonth" id="nextmonth">
					<?php for($i=01; $i<=12; $i++): ?>
						<option <?php if ($i == $screenTxn->getNextDateMonth()) { htmlout(" selected "); }?>
								><?php htmlout(str_pad($i, 2, "0", STR_PAD_LEFT)); ?></option>
					<?php endfor; ?>
				</select>
				-
				<select class="date" name="nextyear" id="nextyear">
					<?php for($i=2012; $i<2020; $i++): ?>
						<option <?php if ($i == $screenTxn->getNextDateYear()) { htmlout(" selected "); }?>
								><?php htmlout($i); ?></option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
    
		<!-- End On Date -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="endonday">End On Date</label></div>
			<div class="editinput">
				<input type="hidden" name="endondate" id="endondate" 
                       value="<?php htmlout($screenTxn->getEndOnDate()); ?>">
                
                <select class="date" name="endonday" id="endonday">
					<?php for($i=01; $i<=31; $i++): ?>
						<option <?php if ($i == $screenTxn->getEndOnDateDay()) { htmlout(" selected "); }?>
								><?php htmlout(str_pad($i, 2, "0", STR_PAD_LEFT)); ?></option>
					<?php endfor; ?>
				</select>
				-
				<select class="date" name="endonmonth" id="endonmonth">
					<?php for($i=01; $i<=12; $i++): ?>
						<option <?php if ($i == $screenTxn->getEndOnDateMonth()) { htmlout(" selected "); }?>
								><?php htmlout(str_pad($i, 2, "0", STR_PAD_LEFT)); ?></option>
					<?php endfor; ?>
				</select>
				-
				<select class="date" name="endonyear" id="endonyear">
					<?php for($i=2012; $i<2020; $i++): ?>
						<option <?php if ($i == $screenTxn->getEndOnDateYear()) { htmlout(" selected "); }?>
								><?php htmlout($i); ?></option>
					<?php endfor; ?>
				</select>
			</div>
		</div>

    
		<!-- Frequency -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="frequency">Repeat Frequency</label></div>
			<div class="editinput">
				<select name="frequency" id="frequency">
					<option <?php htmlout($screenTxn->getFrequencyFormatted() == "Yearly" ? "selected" : ""); ?> value="Y">Yearly</option>
					<option <?php htmlout($screenTxn->getFrequencyFormatted() == "Monthly" ? "selected" : ""); ?> value="M">Monthly</option>
					<option <?php htmlout($screenTxn->getFrequencyFormatted() == "Weekly" ? "selected" : ""); ?> value="W">Weekly</option>
					<option <?php htmlout($screenTxn->getFrequencyFormatted() == "Daily" ? "selected" : ""); ?> value="D">Daily</option>
				</select>
			</div>
		</div>

    
		<!-- Frequency Increment -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label for="every">Every</label></div>
			<div class="editinput">
				<input type="text" class="textfield narrowfield" name="every" id="every" 
                       value="<?php htmlout($screenTxn->getFrequencyIncrement()); ?>"
                       pattern="^[1-9]([0-9]{0,1})$" 
                       required>
                
                <div class="trailinglabel">
                    <label for="every" id="lblEvery">
                        <?php htmlout($screenTxn->getFrequencyFormatted('p')); ?>
                    </label>
                </div>
			</div>
		</div>
    
    
		<!-- Operations -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label>Operations</label></div>
			<div class="editinput">
				<?php if ($screenMode == "add"): ?>
					<button type="submit" name="action" value="add">Add Repeat Txn</button>
				<?php endif; ?>
				<?php if ($screenMode == "edit"): ?>
					<button type="submit" id="updatebtn" 
                            name="action" value="update">Update Repeat Txn</button>
					<button type="submit" id="deletebtn" class="last"  
                            name="action" value="delete">Delete Repeat Txn</button>
					<button type="submit" id="applybtn" class="last" 
                            name="action" value="apply">Apply Repeat Txn</button>
				<?php endif; ?>
			</div>
		</div>	
    
        <div class="inputrow endfloat"></div>
  
</form>

<script src="tcash-panel-repeating-view.js"></script>

