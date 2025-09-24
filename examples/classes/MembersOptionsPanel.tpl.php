<style>
    .sort-wrapper {margin-top: 10px;}
    .info-wrapper {padding: 15px 25px; width: 23%; margin-left: 15px;}
    .board-group-wrapper {margin-left: -15px;}
    .form-actions  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 15px -15px 0 -15px; padding: 15px; text-align: left;}
    .form-horizontal .radio {min-height: 20px;}
    .form-horizontal .radio, .form-horizontal .radio-inline {padding-top: 0; margin-top: 0; margin-bottom: 0;}
    .sortable {margin-top: 15px; border-bottom: #ddd 0.01em solid;}
    .placeholder {height: 55px;outline: 1px dashed #4183C4;background: rgba(73, 182, 255, 0.07); border-radius: 3px;margin: -1px;}
    .div-block {display: block; padding: 9px; vertical-align: middle; border-top: #ddd 1px solid;}
    .div-block:hover {background-color: #f6f6f6;}
    .icon-set {display: inline-block; font-size: 16px; color: #7d898d; background-color: transparent; width: 38px; height: 38px; padding: 7px; text-align: center; vertical-align: middle; cursor: pointer;}
    .icon-set:hover {background: #f6f6f6; color: inherit; text-decoration: none; border: #7d898d 1px solid; border-radius: 4px;}
    .events {display: inline-block; width: 5%; vertical-align: middle;}
    .div-info {display: inline-block; width: 30%; padding-left: 10px;vertical-align: middle;}
    .status-info {display: inline-block; width: 30%; padding-left: 10px; vertical-align: middle;}
    .status-info .radio-inline {vertical-align: baseline !important;}
    .div-buttons {display: inline-block; width: 33%; vertical-align: middle; text-align: right;}
</style>
<div class="form-horizontal">
    <div class="row">
        <div class="col-md-12">
            <div class="panel-heading" style="margin-top: -15px; margin-bottom: 15px; padding-left: 0;">
                <span class="vauu-title-3">Choices and orders of inputs</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 equal">
            <div class="col-md-9">
                <div class="board-group-wrapper">
                    <div class="col-md-5">
                        <?= _r($this->lstGroupTitle); ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="sortable sort-wrapper">
                    <?= _r($this->dlgSorter); ?>
                </div>
                <?= _r($this->lblInfo); ?>
            </div>
            <div class="col-md-3 right-box info-wrapper">
                <div class="form-group">
                    <?= _r($this->lblPostDate); ?>
                    <?= _r($this->calPostDate); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblPostUpdateDate); ?>
                    <?= _r($this->calPostUpdateDate); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblAuthor); ?>
                    <?= _r($this->txtAuthor); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblUsersAsEditors); ?>
                    <?= _r($this->txtUsersAsEditors); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblStatus); ?>
                    <?= _r($this->lstStatus); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblImageUpload); ?>
                    <?= _r($this->lstImageUpload); ?>
                </div>
            </div>
        </div>
    </div>
</div>