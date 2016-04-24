<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="utf-8">
	<!--meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"-->
	<meta name="viewport" content="width=device-width, maximum-scale=1">
	<meta name="apple-mobile-web-app-title" content="theMoney">
	<title>theMoney Accounts Management</title>
	<link rel="stylesheet" href="/tcash/common/styles/tcash-styles.css">
	<link rel="stylesheet" href="/tcash/common/styles/tcash-styles-layout.css">
    <link rel="stylesheet" href="/tcash/common/calendar/tcash-calendar-styles.css">
	<link rel="apple-touch-icon" sizes="72x72" href="/tcash/common/themoney.png">
<!--    <script src="/tcash/jquery/jquery-1.11.3.min.js"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
</head>

<body>
	<div id="container">
		<!-- include generic screen furniture here -->

		<header id="main_header">
			<p class="tcash-ident-large">
                <a href="/tcash/">
                    <em>the</em>Money 
                    <?php htmlout($_SESSION["envname"]); ?>
                </a>
            </p>
		</header>

        <section id="main_user">
            <table id="main_user_table"><tr>
                <td id="main_user_acgroup">
                    <span>A/C Group:</span>
                    <form id="main_user_frmAcGroup" action="/tcash/" method="POST">
                        <select id="main_user_acgroup_select" 
                                name="main_user_acgroup_select"
                                onchange="main_user_frmAcGroup.submit();">
                            <?php if(isset($_SESSION["userobj"])): ?>
                                <?php 
                                    $uo = $_SESSION["userobj"];
                                    foreach ($uo->getAccountGroups() as $ag): 
                                ?>
                                <option 
                                        <?php htmlout($ag["groupid"]==$uo->getAccountGroup() ? 
                                                      " selected " : "") ?>
                                        ><?php htmlout($ag["groupid"]); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </form>
                </td>
                <td id="main_user_user">
                    <?php htmlout(isset($_SESSION["userobj"]) ? 
                                  $_SESSION["userobj"]->getUserId() : 
                                  "Not logged in"); ?>
                    <a href="/tcash/user?mode=logout">(log out)</a>
                </td>
            </tr></table>
        </section>

        
		<div id="main_menu_toggle"><img src="/tcash/common/threelines-grey.jpg">Main Menu</div>

		<nav id="main_menu">  
			<ul class="top_menu">
				<li><a href="/tcash/">Balances</a></li>
				<li><a href="/tcash/receipt/">Add Receipt</a></li>
				<li><a href="/tcash/repeating/">Repeating</a></li>
				<li><a href="/tcash/nextcard/">Next Card</a></li>
				<li><a href="/tcash/reports/">Reports</a>
                    <ul id="report_submenu" class="main_submenu">
                        <li><a href="/tcash/">Monthly spending report</a></li>
                        <li><a href="/tcash/">Monthly spending grouped by category</a></li>
                        <li><a href="/tcash/">Monthly spending grouped by payee</a></li>
                        <li><a href="/tcash/">Charts</a></li>
                    </ul>
                </li>
				<li><a href="/tcash/user/" class="lastmenuitem">User</a></li>
			</ul>
		</nav>
        
        
		<section id="main_content">
			<!-- include the panel that has been requested -->
			<?php include $usePanel; ?>
		</section>

		<footer id="main_footer">
			Copyright &copy2015 TonksDEV
		</footer>
	</div>

	<script src="/tcash/common/tcash-menu.inc.js"></script>

</body>
    
</html>


