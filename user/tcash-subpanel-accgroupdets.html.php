<h1>Edit Account Groups:</h1>

<form name="frmAccGroups" action="." method="POST">

    <table>
        <tr>
           <td>
               <div class="editinput">
                   <div class="userlabel">All Account Groups:</div>
                       <table>
                           <?php 
                                $ags = $_SESSION["userobj"]->getAccountGroups();
                                if ($ags) {
                                    foreach ($ags as $ag) {
                                        echo '<tr><td>';
                                        htmlout($ag["groupid"]);
                                        htmlout($ag["primary"] ? " (primary)" : "");
                                        echo '</td><td>';
                                        echo '<a href=".?mode=leaveacg&acg=';
                                        htmlout($ag["groupid"]);
                                        echo '">[leave group]</a></td></tr>';
                                    }
                                }
                           ?>
                   </table>
               </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="editinput">
                    <div class="userlabel">Join an Existing Account Group:</div>
                    <label class="editlabel" for="joingroupname">Group Name</label>
                    <div class="editinput">
                        <input type="text" autofocus 
                               name="joingroupname" id="joingroupname" 
                               value="">
                    </div>
                    <label class="editlabel" for="joingrouppw">Group Password</label>
                    <div class="editinput">
                        <input type="password" 
                               name="joingrouppw" id="joingrouppw" 
                               value="">
                    </div>
                    <label class="editlabel">&nbsp;</label>
                    <div class="editinput">
                        <button type="submit" class="button-submit"
                               name="action" value="joingroup">Join</button>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="editinput">
                    <div class="userlabel">Create a New Account Group:</div>
                    <label class="editlabel" for="addgroupname">Group Name</label>
                    <div class="editinput">
                        <input type="text" autofocus 
                               name="addgroupname" id="addgroupname" 
                               value="">
                    </div>
                    <label class="editlabel" for="addgroupname">Group Description</label>
                    <div class="editinput">
                        <textarea name="addgroupdesc" id="addgroupdesc"></textarea>
                    </div>
                    <label class="editlabel" for="addgrouppw">Group Password</label>
                    <div class="editinput">
                        <input type="password" 
                               name="addgrouppw" id="addgrouppw" 
                               value="">
                    </div>
                    <label class="editlabel">&nbsp;</label>
                    <div class="editinput">
                        <button type="submit" class="button-submit"
                               name="action" value="addgroup">Create</button>
                    </div>
                </div>
            </td>
        </tr>
        
    </table>

</form>
