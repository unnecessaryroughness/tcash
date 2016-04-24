<section class="layoutcol1">
	<h2>Balances:</h2>

	<div class="accounttype">Current Accounts</div>

    <?php $totCABal = 0; ?>
	<?php foreach($portfolio->getAllAccounts() as $acc): ?>

		<?php 
            if($acc->getAccountType() == 'CURRENTACC'): ?>
            <?php $totCABal = $totCABal + $acc->getBalance(true); ?>
			<article>
				<header>
					<a href="/tcash/account?acc=<?php htmlout($acc->getName()); 
                             ?>"><?php htmlout($acc->getName()); ?></a>
				</header>
				<p id="balancebox1">
					<a href="/tcash/account?acc=<?php htmlout($acc->getName()); 
								?>"><?php htmlout($acc->getFullName()); ?></a>
				</p>
				<p id="balancebox2">
					<a href="/tcash/account?acc=<?php htmlout($acc->getName()); 
								?>">£<?php htmlout($acc->getBalance()); ?></a>
				</p>
				<div class="endfloat"></div>
			</article>
		<?php endif; ?>
	<?php endforeach; ?>


	<div class="accounttype">Savings Accounts</div>

	<?php foreach($portfolio->getAllAccounts() as $acc): ?>

		<?php if($acc->getAccountType() == 'SAVINGSACC'): ?>
			<article>
				<header>
					<a href="/tcash/account?acc=<?php htmlout($acc->getName()); 
                             ?>"><?php htmlout($acc->getName()); ?></a>
				</header>
				<p id="balancebox1">
					<a href="/tcash/account?acc=<?php htmlout($acc->getName()); 
								?>"><?php htmlout($acc->getFullName()); ?></a>
				</p>
				<p id="balancebox2">
					<a href="/tcash/account?acc=<?php htmlout($acc->getName()); 
								?>">£<?php htmlout($acc->getBalance()); ?></a>
				</p>
				<div class="endfloat"></div>
			</article>
		<?php endif; ?>
	<?php endforeach; ?>

	<div class="accounttype">Credit/Store Accounts</div>

	<?php foreach($portfolio->getAllAccounts() as $acc): ?>

		<?php if($acc->getAccountType() == 'CREDITACC' || $acc->getAccountType() == 'STORECARD'): ?>
			<article>
				<header>
					<a href="/tcash/account?acc=<?php htmlout($acc->getName()); 
                             ?>"><?php htmlout($acc->getName()); ?></a>
				</header>
				<p id="balancebox1">
					<a href="/tcash/account?acc=<?php htmlout($acc->getName()); 
								?>"><?php htmlout($acc->getFullName()); ?></a>
				</p>
				<p id="balancebox2">
					<a href="/tcash/account?acc=<?php htmlout($acc->getName()); 
								?>">£<?php htmlout($acc->getBalance()); ?></a>
				</p>
				<div class="endfloat"></div>
			</article>
		<?php endif; ?>
	<?php endforeach; ?>

</section>

<section class="layoutcol2">
    <div class="balancecalendar">
        <!--INCLUDE THE CALENDAR SCRIPT-->
        <?php include $_SERVER["DOCUMENT_ROOT"] . 
                "/tcash/common/calendar/tcash-calendar.inc.html.php"; ?>
    </div>
    
    <div class="cashpermonth">
        <p>Next Pay Day: <span id="nextpayday"></span></p>
        <p>Days Until Pay Day: <span id="daystilpayday"></span></p>
        <br>
        <p>Total Balance: <span id="totalbalance">£<?php htmlout($totCABal); ?></span></p>
        <p>Cash Per Day Remaining: <span id="cashperday"></span></p>
        <p>Cash Per Week Remaining: <span id="cashperweek"></span></p>
    </div>
</section>

<p class="endfloat"></p>

<script src="/tcash/balances/tcash-panel-balances.js"></script>

<script>
    $(function() {
        $.ajax({
            method: "GET",
            dataType: "jsonp", 
            contentType: 'application/json',
            url: "http://nodejs-tonks.rhcloud.com/api/money/cashperday",
            data: {
                payday: 27,
                balance: <?php htmlout($totCABal); ?>
            },
            success: function(data) {
                $("#nextpayday").text(data.nextPayDate);
                $("#cashperday").text("£" + data.cashPerDayRemaining);
                $("#cashperweek").text("£" + data.cashPerWeekRemaining);
                $("#daystilpayday").text(data.numberOfDaysToPayday);
            },
            error: function (jqXHR, text, errorThrown) {
                console.log("an error occurred: ");
                console.log(jqXHR + " " + text + " " + errorThrown);
            }
        });
    });
</script>