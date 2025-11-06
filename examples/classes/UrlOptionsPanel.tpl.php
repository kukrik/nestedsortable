<style>
    .text-success {color: #4caf50; font-size: 1.33333333em; line-height: 1em; vertical-align: -15%;}
    .text-error {color: #ff0000; font-size: 1.33333333em; line-height: 1em; vertical-align: -15%;}
    .form-actions-wrapper  {display: block; background-color: #f5f5f5; border-radius: 4px; padding: 15px; text-align: left;}
</style>
<div class="form-horizontal">
    <div class="form-body">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="dashboard-heading" style="margin-left: 15px;">
                        <?= _r($this->lblUrlOptionTitle); ?>
                    </div>
                    <div class="form-group">
                        <?= _r($this->lblLockedName); ?>
                        <div class="form-inline">
                            <div class="col-md-7">
                                <?= _r($this->txtLockedName); ?>
                            </div>
                            <div class="col-md-1">
                                <?= _r($this->lblLockedNameCheck); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-actions-wrapper" style="text-align: right;">
                            <?= _r($this->btnSave); ?>
                            <?= _r($this->btnCancel); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6"  style="border-left: 1px solid #ccc;">
                <div class="col-md-12">
                    <div class="row">
                        <div class="dashboard-heading">
                            <?= _r($this->lblExclusionListTitle); ?>
                        </div>
                        <?= _r($this->lblInfo); ?>
                        <?= _r($this->pnlExclusionList); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>