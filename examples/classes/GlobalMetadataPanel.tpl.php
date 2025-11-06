<div class="form-horizontal">
    <div class="form-body">
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
                <?= _r($this->lblKeywordsHint); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblKeywords); ?>
            <div class="col-md-9">
                <?= _r($this->txtKeywords); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
                <?= _r($this->lblDescriptionHint); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblDescription); ?>
            <div class="col-md-9">
                <?= _r($this->txtDescription); ?>
            </div>
        </div>
        <!--<div class="row">
            <div class="col-md-offset-3 col-md-9">
                <?php /*= _r($this->lblAuthorHint); */?>
            </div>
        </div>
        <div class="form-group">
            <?php /*= _r($this->lblAuthor); */?>
            <div class="col-md-9">
                <?php /*= _r($this->txtAuthor); */?>
            </div>
        </div>-->
        <div class="form-actions fluid">
            <div class="col-md-offset-3 col-md-9">
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnDelete); ?>
            </div>
        </div>
    </div>
</div>