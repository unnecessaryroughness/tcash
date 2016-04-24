
<h2>Next Card:</h2>
<br>

<?php if(isset($acclist)): ?>

    <form action="." method="POST">
        <input type="hidden" name="txnid" value="<?php htmlout($screenTxn->getId()); ?>">
        <input type="hidden" name="txntype" value="Payment">
        <input type="hidden" name="date" value="<?php htmlout($screenTxn->getDate()); ?>">
        <input type="hidden" name="payee" value="<?php htmlout($screenTxn->getPayee()); ?>">
        <input type="hidden" name="category" value="<?php htmlout($screenTxn->getCategory()); ?>">

        <!-- Account -->
        <div>
            <div>
                <label for="accountid" class="nextlabel">Next Account:</label>
            </div>
            <div>
                <fieldset class="nextfieldset">
                    <?php foreach($acclist as $acct): ?>
                        <input type="radio" class="rdoInput" name="accountid" 
                               value="<?php htmlout($acct->getName()); ?>" 
                               id="rdoCA_<?php htmlout($acct->getName()); ?>"
                               onchange="rtnaccountid.value = this.value;"
                               required checked>
                        <label for="rdoCA_<?php htmlout($acct->getName()); ?>">
                            <?php htmlout($acct->getFullName()); ?>
                        </label>
                        <br>
                    <?php endforeach; ?>
                </fieldset>
            </div>
        </div>	


        <div>
            <label for="arrivedamount" class="nextlabel">Next Card Operations:</label>
        </div>

        <div class="nextborder">

            <div class="nextheading">
                Something Arrived
            </div>

            <div class="nextinputgroup">
                <!-- Amount -->
                <label for="arrivedamount" class="nextlabel">Amount</label>
                <input type="text" class="nextfield" 
                       name="arrivedamount" id="arrivedamount" 
                       pattern="-{0,1}[0-9]{1,6}\.[0-9]{2,2}"
                       required autofocus>

                <!-- Operations -->
                <div class="endfloat">
                    <button type="submit" name="action" class="nextbutton"
                            value="receipt">Add Receipt</button>
                </div>	
            </div>

            <div class="nextinputgroupwide">
                <textarea name="receiptnotes" id="receiptnotes" class="nexttextarea" 
                          placeholder="Items that were delivered"></textarea>
            </div>
        </div>
    </form>

    <form action="." method="POST">
        <input type="hidden" name="txnid" value="<?php htmlout($screenTxn->getId()); ?>">
        <input type="hidden" name="txntype" value="Payment">
        <input type="hidden" name="date" value="<?php htmlout($screenTxn->getDate()); ?>">
        <input type="hidden" name="payee" value="<?php htmlout($screenTxn->getPayee()); ?>">
        <input type="hidden" name="category" value="<?php htmlout($screenTxn->getCategory()); ?>">
        <input type="hidden" name="rtnaccountid" id="rtnaccountid">

        <div class="nextborder">

            <div class="nextheading">
                Something was returned
            </div>

            <div class="nextinputgroup">
                <!-- Amount -->
                <label for="returnedamount" class="nextlabel">Amount</label>
                <input type="text" class="nextfield" 
                       name="returnedamount" id="returnedamount" 
                       pattern="-{0,1}[0-9]{1,6}\.[0-9]{2,2}"
                       required>

                <!-- Operations -->
                <div class="endfloat">
                    <button type="submit" name="action" class="nextbutton" 
                            value="return">Add Return</button>
                </div>	
           </div>

            <div class="nextinputgroupwide">
                <textarea name="returnnotes" id="returnnotes" class="nexttextarea"  
                          placeholder="Items that were returned"></textarea>
            </div>
        </div>

        <div class="endfloat"></div>

    </form>

    <script src="tcash-panel-next-view.js"></script>

<?php else: ?>

    <div>
        <label for="accountid" class="nextlabel">No Next Account Defined.</label>
    </div>

<?php endif; ?>
