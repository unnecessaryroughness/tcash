<h2>
    Repeating Transactions:
</h2>

<ul class="submenu">
    <li class="submenu_item"><a href="/tcash/repeating/view?mode=add">New</a></li>
    <li class="submenu_item lastmenuitem">
        <div id="mnuA2d" name="mnuA2d" class="dummya">To:</div>
    </li>
    <select class="date" name="applyday" id="applyday">
        <?php for($i=01; $i<=31; $i++): ?>
            <option><?php htmlout(str_pad($i, 2, "0", STR_PAD_LEFT)); ?></option>
        <?php endfor; ?>
    </select>
    <select class="date" name="applymonth" id="applymonth">
        <?php for($i=01; $i<=12; $i++): ?>
            <option><?php htmlout(str_pad($i, 2, "0", STR_PAD_LEFT)); ?></option>
        <?php endfor; ?>
    </select>
    <select class="date" name="applyyear" id="applyyear">
        <?php for($i=2015; $i<2020; $i++): ?>
            <option><?php htmlout($i); ?></option>
        <?php endfor; ?>
    </select>
</ul>


<div class="repeatList">
    <?php foreach($screenRegister->getTransactions() as $trans): ?>
        <article class="repeatTxn">
            <a href="/tcash/repeating/view?mode=edit&tx=<?php htmlout($trans->getId()); ?>">
                <div class="repeatTxnCol1">
                    <div id="rpt_AccountId">
                        <?php htmlout($trans->getAccountId()); ?>
                    </div>
                    <div id="rpt_Payee">
                        <?php htmlout($trans->getPayee()); ?>
                    </div>
                    <div id="rpt_Category">
                        <?php htmlout($trans->getCategory()); ?>
                    </div>
                </div>
                <div class="repeatTxnCol2">
                    <div id="rpt_Amount">
                        <?php htmlout("£" . $trans->getAmountCr()); ?>
                    </div>
                    <div id="rpt_NextDate">
                        <?php htmlout("next due on " . $trans->getNextDateFormatted()); ?>
                    </div>
                    <div id="rpt_FrequencyIncrement">
                        <?php htmlout("then repeat " . $trans->getFrequencyIncrementFormatted()); ?>
                    </div>
                    <div id="rpt_EndOnDate">
                        <?php htmlout('until ' . $trans->getEndOnDateFormatted()); ?>
                    </div>
                </div>
            </a>
        </article>
    <?php endforeach; ?>
</div>

<script src="tcash-panel-repeating-display.js"></script>
