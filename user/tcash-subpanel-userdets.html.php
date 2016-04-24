
<h1><?php htmlout($displayResult); ?></h1>

<?php if ($userdets): ?>
    <table>
    <tr>
        <td><div class="userlabel">User:</div> 
            <?php htmlout($userdets->getUserId()) ?></td>  
    </tr>
    <tr>
        <td><div class="userlabel">Full Name:</div> <?php htmlout($userdets->getFullname()) ?></td>  
    </tr>
    <tr>
        <td><div class="userlabel">Email:</div> <?php htmlout($userdets->getEmail()) ?></td>  
    </tr>
    <tr>
       <td><div class="userlabel">Current Account Group:</div> <?php htmlout($_SESSION["userobj"]->getAccountGroup()); ?></td> 
    </tr>
    <tr>
       <td><div class="userlabel">All Account Groups:</div>
           <?php 
                $ags = $_SESSION["userobj"]->getAccountGroups();
                if ($ags) {
                    foreach ($ags as $ag) {
                        htmlout($ag["groupid"] . ", ");
                    }
                }
           ?></td>
    </tr>
    </table>
<?php endif; ?>
