<h2>User Details:</h2>

<ul class="submenu">
    <li class="submenu_item"><a href="/tcash/user">Home</a></li>
    <li class="submenu_item"><a href="/tcash/user?mode=edit">User</a></li>
    <li class="submenu_item"><a href="/tcash/user?mode=viewacg">Groups</a></li>
    <?php if ($_SESSION["userobj"]->isAccGroupOwner()): ?>
        <li class="submenu_item"><a href="/tcash/user?mode=viewacc">Accounts</a></li>
    <?php endif; ?>
</ul>

<br>

<?php include $useSubPanel; ?>


