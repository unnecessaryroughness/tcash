<p>
	<a href="/tcash/account?acc=<?php htmlout($selectedAcc); ?>"><< Back to transaction register</a>
</p>

<h2>Transaction - <?php htmlout($selectedTxn); ?>:</h2>

<form action=".?acc=<?php htmlout($selectedAcc); ?>" method="POST">
	<table border="1">
		<tr>
			<th>Id</th>
			<td><?php htmlout($screenTxn->getId()); ?></td>
		</tr>	
			<tr>
				<th>Date</th>
				<td><?php htmlout($screenTxn->getDate()); ?></td>
			</tr>	
			<tr>
				<th>Account</th>
				<td><?php htmlout($screenTxn->getAccountId()); ?></td>
			</tr>	
			<tr>
				<th>Payee</th>
				<td><?php htmlout($screenTxn->getPayee()); ?></td>
			</tr>	
			<tr>
				<th>Category</th>
				<td><?php htmlout($screenTxn->getCategory()); ?></td>
			</tr>	
			<tr>
				<th>Notes</th>
				<td><?php htmlout($screenTxn->getNotes()); ?></td>
			</tr>	
			<tr>
				<th>Reconciled?</th>
				<td><?php if ($screenTxn->getIsCleared()) { htmlout("YES"); } ?></td>
			</tr>	
			<tr>
				<th>Placeholder?</th>
				<td><?php if ($screenTxn->getIsPlaceholder()) { htmlout("YES"); } ?></td>
			</tr>	
			<tr>
				<th>Transaction Type</th>
				<td><?php htmlout($screenTxn->getTxnType()); ?></td>
			</tr>
			<tr>
				<th>Amount</th>
				<td><?php htmlout($screenTxn->getAmountCr()); ?></td>
			</tr>	
			<tr>
				<th>Operations</th>
				<td>
					<button type="submit" name="action" value="delete">Delete Transaction</button>
				</td>
			</tr>	
			<input type="hidden" name="accountid" value="<?php htmlout($screenTxn->getAccountId()); ?>">
	</table>
	<input type="hidden" name="txnid" value="<?php htmlout($selectedTxn); ?>">
</form>


