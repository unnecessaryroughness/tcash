
<div class="twocoltable">
    <div class="twocoltable-col1">

        <h2 class="accountsummary">
            <select name="showaccount" id="showaccount" class="selectdropdown">
                <?php foreach($acclist as $acct): ?>
                   <option <?php htmlout($acct->getName() == $selectedAcc ? " selected " : "");?> >
                       <?php htmlout($acct->getName()); ?>
                   </option>
                <?php endforeach; ?>
            </select>  
        </h2>
    </div>
    <div class="twocoltable-col2">
        <h2 class="register-todaysbalance">Today: £<?php htmlout($todaybal); ?></h2>
    </div>
</div>

<ul class="submenu">
    <li class="submenu_item">
        <a href="/tcash/account/view?mode=add&acc=<?php htmlout($selectedAcc); ?>">
            Add a New Transaction
        </a>
    </li>
</ul>

<div class="endfloat"></div>

<!-- article-based view -->

<?php foreach($account->getTransactions() as $txn): ?>
	
	<article id="entry-<?php htmlout($txn->getId()); ?>" 
			 class="register<?php htmlout((!$txn->getIsCleared() ? ' unreconciled ' : '')); 
									htmlout(($txn->isFutureDated() ? ' futuredated ' : '')); 
                                    htmlout(($txn->getIsPlaceholder() ? ' placeholder ' : '')); 
                                    htmlout(($txn->getId() == $todaytxn ? ' today ' : '')); 
                            ?>">

		<span id="register-col1">
			<span id="entry-date-<?php htmlout($txn->getId()); ?>" 
				  class="register-date" 
				  onclick="toggleCleared(<?php 
                                        htmlout(
                                                $txn->getId() .','. 
                                                $txn->getIsCleared() .','. 
                                                ($txn->isFutureDated() ? '1' : '0') . ',' . 
                                                $txn->getIsPlaceholder() . ',' .
                                                ($txn->getId() == $todaytxn ? '1' : '0')
                                               ); 
								?>)"><?php htmlout($txn->getDateFormatted()); ?></span>
			<span class="register-payee"><?php htmlout($txn->getPayee()); ?></span>
			<span class="register-category"><?php htmlout($txn->getCategory()); ?></span>
		</span>
        
		<span id="register-col2">
			<a id="hlink-<?php htmlout($txn->getId()); ?>" 
               href="./view?mode=edit&acc=<?php htmlout($txn->getAccountId()); ?>&txn=<?php 
												htmlout($txn->getId()); ?>"
               class="register<?php htmlout((!$txn->getIsCleared() ? ' unreconciled ' : '')); 
									htmlout(($txn->isFutureDated() ? ' futuredated ' : '')); 
                                    htmlout(($txn->getIsPlaceholder() ? ' placeholder ' : '')); ?>"
               >			
                <span class="register-amountcr"><?php htmlout($txn->getAmountCr()); ?></span>
                <span class="register-displaydr"><?php htmlout($txn->getDisplayDr()); ?></span>
				<span class="register-balance"><?php htmlout($txn->getTxnBalance()); ?></span> 
                <span class="register-displaycr"><?php htmlout($txn->getDisplayCr()); ?></span>
			</a>
		</span>
        <div class="endfloat"></div>
	</article>

<?php endforeach; ?>

<script src="tcash-panel-account.js"></script>



