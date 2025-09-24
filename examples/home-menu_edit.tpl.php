<?php $strPageTitle = t('Edit homepage'); ?>

<?php require('header.inc.php'); ?>
    <style>
        .select2-container--web-vauu .select2-results__option[aria-disabled=true] {
            display: none;
        }
    </style>
<?php $this->RenderBegin(); ?>
    <div class="page-container">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <?= _r($this->nav); ?>
                </div>
            </div>
        </div>
    </div>
<?php $this->RenderEnd(); ?>
<?php require('footer.inc.php'); ?>