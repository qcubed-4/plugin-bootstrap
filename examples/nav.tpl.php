<?php     require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>
    <link href="<?= QCUBED_BOOTSTRAP_CSS ?>" rel="stylesheet">
    <link href="<?= QCUBED_BOOTSTRAP_ASSETS_URL ?>/css/qbootstrap.css" rel="stylesheet">

<?php $this->renderBegin(); ?>

    <div id="instructions">
        <h1 class="instruction_title">Bootstrap\Nav: A class to implement Bootstrap Navs and Tabs</h1>

        <p>
            The Nav can implement Navs as documented in the Bootstrap Components documetation and Tabs as described
            in the Bootstrap Javascript documentation.
        </p>
        <p>
            It has a couple of modes of operation, but the easiest is to add child Panels to the Nav and
            give each panel a name.
        </p>

    </div>

    <div id="demoZone">
        <h2>Basic implementation</h2>
        <?php $this->nav1->Render(); ?>
        <h2>With Pill Buttons</h2>
        <?php $this->nav2->Render(); ?>
    </div>
<?php $this->renderEnd(); ?>
<?php     require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>