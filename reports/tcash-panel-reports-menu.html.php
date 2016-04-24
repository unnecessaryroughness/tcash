<h2>Reporting Menu:</h2>
<br>

<form action="." method="POST">

    <fieldset class="fieldset-report">
        <legend class="fieldset-legend-report">Select Report Type</legend>
        <ul>
            <li class="li-report">
                <input type="radio" class="radio-report" name="rdoReportType" 
                       value="SpendCat" id="rdoRpt_SpendCat"
                       <?php htmlout($scrParms["type"]=="SpendCat" ? " checked" : ""); ?>>
                <label class="label-report" for="rdoRpt_SpendCat">
                    All Spending By Category
                </label>
            </li>
            <li class="li-report">
                <input type="radio" class="radio-report" name="rdoReportType" 
                       value="SpendPayee" id="rdoRpt_SpendPayee"
                       <?php htmlout($scrParms["type"]=="SpendPayee" ? " checked" : ""); ?>>
                <label class="label-report" for="rdoRpt_SpendPayee">
                    All Spending By Payee
                </label>
            </li>
            <li class="li-report">
                <input type="radio" class="radio-report" name="rdoReportType" 
                       value="TransByCat" id="rdoRpt_TransByCat"
                       <?php htmlout($scrParms["type"]=="TransByCat" ? " checked" : ""); ?>>
                <label class="label-report" for="rdoRpt_TransByCat">
                    All Transactions By Category
                </label>
            </li>
        </ul>
    </fieldset>

    <fieldset name="startdate" class="fieldset-report">
        <legend class="fieldset-legend-report">Select Start Date for Report</legend>
        <input id="startdate" name="startdate" type="date" 
               class="datebox-report" 
               value="<?php htmlout($scrParms["sdate"]); ?>" 
               pattern="^201[0-9]-(0[1-9]|1[1-2])-(0[1-9]|[12][0-9]|3[01])$"
               required>
    </fieldset>

    <fieldset name="enddate" class="fieldset-report">
        <legend class="fieldset-legend-report">Select End Date for Report</legend>
        <input id="enddate" name="enddate" type="date" 
               class="datebox-report" 
               value="<?php htmlout($scrParms["edate"]); ?>" 
               pattern="^201[0-9]-(0[1-9]|1[1-2])-(0[1-9]|[12][0-9]|3[01])$"
               required>
    </fieldset>
      
    
    <fieldset class="fieldset-report">
        <legend class="fieldset-legend-report">Select Accounts for Report:</legend>
        <select name="account" id="accountlist" class="listbox-report" multiple="multiple">
            <option value="">ALL ACCOUNTS</option>
            <?php foreach ($scrAccList as $acct): ?>
                <option value="<?php htmlout($acct->getAccountId()); ?>">
                    <?php htmlout($acct->getFullName()); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </fieldset>
    
    <fieldset name="category" class="fieldset-report">
        <legend class="fieldset-legend-report">Select Categories for Report</legend>
        <select name="category" id="categorylist" class="listbox-report" multiple="multiple">
            <option value="">ALL CATEGORIES</option>
            <?php foreach ($scrCatList as $cat): ?>
                <option value="<?php htmlout($cat["id"]); ?>"><?php htmlout($cat["id"]); ?></option>
            <?php endforeach; ?>
        </select>
    </fieldset>
    
    <input type="hidden" name="acclist" id="acclist" style="width:30em" 
           value="<?php htmlout($scrParms["accs"]); ?>">
    <input type="hidden" name="catlist" id="catlist" style="width:30em"
           value="<?php htmlout($scrParms["cats"]); ?>">
    
    <?php if(isset($_SESSION["errText"])): ?>
        <p class="endfloat registererror"><?php htmlout($_SESSION["errText"]); 
            unset($_SESSION["errText"]); ?></p>
    <?php endif; ?>
    
    <button name="action" id="cmdRun" 
            type="submit" class="endfloat button-report" 
            value="run">Run Report</button>
    
    
</form>

<script src="tcash-panel-reports-menu.js"></script>
