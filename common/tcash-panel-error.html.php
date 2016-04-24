<h2>AN ERROR OCCURRED</h2>

<p class="ErrText">
    <?php htmlout(substr($_SESSION["errText"], 0, strpos($_SESSION["errText"], "#@#"))); ?>
</p>

<?php if ($_SESSION["devmode"]): ?>
    <h2>Source Error</h2>
    <p class="ErrText">
        <?php htmlout(substr($_SESSION["errText"], 
                             (strpos($_SESSION["errText"], "#@#")) >0 ? 
                                strpos($_SESSION["errText"], "#@#")+3 : 0)); ?>
    </p>
<?php endif; ?>


