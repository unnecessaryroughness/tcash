<p class="backlink">
	<a href="/tcash/account?acc=<?php htmlout($selectedAcc . '#hlink-' 
                                              . $screenTxn->getId()); ?>"><< Back</a>
</p>

<h2><?php htmlout(ucfirst($selectedMode)); ?> Transaction</h2>
<br>

<form name="entryform" action=".?acc=<?php htmlout($selectedAcc); ?>" method="POST">
	<input type="hidden" id="returnURL" name="returnURL" value="">
	<input type="hidden" name="txnid" value="<?php htmlout($screenTxn->getId()); ?>">
	<input type="hidden" name="transfertxn" value="<?php htmlout($screenTxn->getTransferTxn()); ?>">
    <input type="hidden" name="refdata" id="refdata" value="">
    
	<div id="inputcol1">
		<!-- ID -->
		<div class="inputrow endfloat">
			<div class="editlabel"><label>Id</label></div>
			<div class="editinput">
                <label class="trailinglabel"><?php htmlout($screenTxn->getId()); ?></label>
            </div>
		</div>


		<!-- Transaction Type -->
		<div class="inputrow endfloat">
			<div class="editlabel">
                <label for="txntype">Transaction Type</label>
            </div>
			<div class="editinput">
                <select name="txntype" id="txntype" required>
                    
                    <?php if($screenTxn->getTxnType() != "Transfer"): ?>
                        <option <?php htmlout($screenTxn->getTxnType() == "Payment" ? 
                                              "selected" : ""); ?>>Payment</option>
                        <option <?php htmlout($screenTxn->getTxnType() == "Deposit" ? 
                                              "selected" : ""); ?>>Deposit</option>
                    <?php endif; ?>
                    
                    <?php if($screenTxn->getTxnType() == "Transfer" || $screenTxn->getId() == ""): ?>
                        <option <?php htmlout($screenTxn->getTxnType() == "Transfer" ? 
                                              "selected" : ""); ?>>Transfer</option>
                    <?php endif; ?>
                    
                </select>
            </div>
		</div>

		<!-- Date -->
		<div class="inputrow endfloat">
			<div class="editlabel">
                <label for="day">Date</label>
            </div>
			<div class="editinput">
				<input type="text" name="date" id="date" 
                       class="textfield mediumwidthfield"
                       pattern="^20[0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$"
                       value="<?php htmlout($screenTxn->getDate()); ?>"
                       required>                                
            </div>
            <div class="editlabel">&nbsp;</div>
            <div class="editinput">
                <!--INCLUDE THE CALENDAR SCRIPT-->
                <?php include $_SERVER["DOCUMENT_ROOT"] . 
                        "/tcash/common/calendar/tcash-calendar.inc.html.php"; ?>
			</div>
		</div>

    </div>
    <div id="inputcol2">
        
		<!-- Account -->
		<div class="inputrow endfloat">
			<div class="editlabel">
                <label for="accountid">Account</label>
            </div>
			<div class="editinput">
				<select name="accountid" id="accountid" 
                        value="<?php htmlout($screenTxn->getAccountId()); ?>" required>
					<?php foreach($acclist as $acct): ?>
						<option <?php htmlout($acct->getName() == $screenTxn->getAccountId() ?
                                      " selected " : "");?>>
                            <?php htmlout($acct->getName()); ?>
                        </option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>	


		<!-- Payee -->
		<div class="inputrow endfloat">
			<div class="editlabel">
                <label id="lbl_payee" for="payee">Pay To</label>
            </div>
			<div class="editinput">
				<select name="payee" id="payee" class="narrow" autofocus>
					<?php foreach($payeelist as $pay): ?>
						<option <?php htmlout($pay["payee"] == $screenTxn->getPayee() ? 
                                              " selected " : ""); ?>>
                                <?php htmlout($pay["payee"]); ?>
                        </option>
					<?php endforeach; ?>
				</select>
                <select name="txfaccount" id="txfaccount" class="narrow">
                    <option></option>
                    <?php foreach($txflist as $txfacc): ?>
                        <option <?php htmlout("<" . $txfacc["id"] . ">" == $screenTxn->getPayee() ? 
                                              " selected " : ""); ?>>
                               &lt;<?php htmlout($txfacc["id"]); ?>&gt;
                        </option>
                    <?php endforeach; ?>
                </select>
				<button type="button" class="narrow" 
                        name="addpayee" id="addpayee"
                        value="addpayee">New</button>
            </div>
		</div>	

        
		<!-- Category -->
		<div class="inputrow endfloat">
			<div class="editlabel">
                <label for="category">Category</label>
            </div>
			<div class="editinput">
				<select name="category" id="category" class="narrow" required>
					<?php foreach($catlist as $cat): ?>
						<option <?php htmlout($cat["id"] == $screenTxn->getCategory() ? 
                                              " selected " : ""); ?>>
                                <?php htmlout($cat["id"]); ?>
                        </option>
					<?php endforeach; ?>
				</select>
				<button type="button" class="narrow" 
                        name="addcategory" id="addcategory" 
                        value="addcategory">New</button>
			</div>
		</div>	

        
		<!-- Notes -->
		<div class="inputrowtall endfloat">
			<div class="editlabel">
                <label for="notes">Notes</label>
            </div>
			<div class="editinput">
				<textarea name="notes" id="notes"><?php htmlout($screenTxn->getNotes()); ?></textarea>
			</div>
		</div>	

		<!-- Reconciled Checkbox -->
		<div class="inputrow endfloat">
			<div class="editlabel">
                <label for="iscleared">Reconciled?</label>
            </div>
			<div class="editinput">
				<input type="checkbox" name="iscleared" id="iscleared" value="1" 
								<?php htmlout($screenTxn->getIsCleared() ? " checked " : ""); ?>>
			</div>
		</div>	

		<!-- Placeholder Checkbox -->
		<div class="inputrow endfloat">
			<div class="editlabel">
                <label for="isplaceholder">Placeholder?</label>
            </div>
			<div class="editinput">
				<input type="checkbox" name="isplaceholder" id="isplaceholder" value="1" 
							<?php htmlout($screenTxn->getIsPlaceholder() ? " checked " : ""); ?>>
			</div>
		</div>	

		<!-- Amount -->
		<div class="inputrow endfloat">
			<div class="editlabel">
                <label for="txnamount">Amount</amount>
            </div>
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
        
        <!-- Reduce Placeholder (ADD SCREEN ONLY) -->
        <?php if ($selectedMode == "add"): ?>
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
        <?php endif; ?>
        
        
		<!-- Operations -->
		<div class="inputrow endfloat">
			<div class="editlabel">
                <label>Operations</label>
            </div>
			<div class="editinput">
				<?php if ($selectedMode == "add"): ?>
					<button type="submit" name="action" value="add">Add</button>
				<?php elseif ($selectedMode == "edit"): ?>
					<button type="submit" id="updatebtn" name="action" value="update">Update</button>
					<button type="submit" id="deletebtn" class="last" name="action" value="delete">Delete</button>
				<?php endif; ?>
			</div>
		</div>	
	</div>

    <div class="endfloat"></div>

</form>

<script src="tcash-panel-account-view.js"></script>

