<?php $strPageTitle = t('Videos edit'); ?>

<?php require('header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

    <style>
        .form-actions-wrapper  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0 -15px; padding: 15px; text-align: left;}
        .form-actions  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 15px -15px 0 -15px; padding: 15px;}
        .form-horizontal .radio-inline {/*padding-top: 18px;*/margin-top: 0;margin-bottom: 0;}
        .edit.radio-inline {padding-top: 18px;margin-top: 0;margin-bottom: 0;}
        .video-add-wrapper {margin-top: -15px;}
        .slug-wrapper {margin: 15px 0;padding: 10px 0;border-top: #ddd 1px solid;}

        .video-setting-wrapper {margin-top: 15px;padding: 30px 0 10px;border-top: #ddd 1px solid;}
        .video-wrapper {margin-top: 15px;/*text-align: left;*/}
        .sortable div.activated {background-color: #eaffea; /*#ddffdd*/}
        .sortable div.inactivated {background-color: #fff0f1; /*#ffe8e8*/}
        .placeholder {height: 50px;outline: 1px dashed #4183C4;background: rgba(73, 182, 255, 0.07);border-radius: 3px;margin: -1px;}

        .div-block {display: block; padding: 9px; vertical-align: middle; border-top: #ddd 1px solid;}
        .div-block:hover {background-color: #f6f6f6;}
        .icon-set {display: inline-block; font-size: 16px; color: #7d898d; background-color: transparent; width: 38px; height: 38px; padding: 7px; text-align: center; vertical-align: middle; cursor: pointer;}
        .icon-set:hover {background: #f6f6f6; color: inherit; text-decoration: none; border: #7d898d 1px solid; border-radius: 4px;}
        .events {display: inline-block; vertical-align: middle;}
        .div-info {display: inline-block; width: 50%; padding-left: 10px;vertical-align: middle;}
        .status-info {display: inline-block; width: 35%; padding-left: 10px; vertical-align: middle;}
        .status-info .radio-inline {vertical-align: baseline !important;}
        .div-buttons {display: inline-block; width: 33%; vertical-align: middle; text-align: right;}
    </style>
    <script>
        customConfig = {
            skin: 'moono',
            width: '100%',
            height: '70px',
            extraPlugins: 'colorbutton,font,justify,pastefromword,liststyle',
            toolbar: [
                { name: 'clipboard', items: [ 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'Subscript', 'Superscript' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
                { name: 'links', items: [ 'Link', 'Unlink' ] },
                '/',
                { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor', 'CopyFormatting' ] },
                { name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'document', items: [ 'Source' ] }
            ]
        }

    </script>

    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
                    <div class="panel-heading">
                        <h3 class="vauu-title-3 margin-left-0"><?php _t('Videos edit: ') ?><?= _r($this->lblGroupTitle); ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-horizontal" style="padding: 0 5px;">
                            <div class="row equal">
                                <div class="col-md-9 left-box padded-wrapper">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="video-add-wrapper">
                                                <?= _r($this->btnAddVideo); ?>
                                                <?= _r($this->txtNewTitle); ?>
                                                <?= _r($this->btnVideoSave); ?>
                                                <?= _r($this->btnVideoCancel); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="slug-wrapper">
                                            <div class="col-md-12">
                                                <?= _r($this->lblTitleSlug); ?>
                                                <?= _r($this->txtTitleSlug); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="video-setting-wrapper">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <?= _r($this->lblTitle); ?>
                                                    <div class="col-md-9">
                                                        <?= _r($this->txtTitle); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?= _r($this->lblIntroduction); ?>
                                                    <div class="col-md-9">
                                                        <?= _r($this->txtIntroduction); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group js-embed-code">
                                                    <?= _r($this->lblEmbedCode); ?>
                                                    <div class="col-md-9">
                                                        <?= _r($this->txtEmbedCode); ?>
                                                        <?= _r($this->btnEmbed); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group hidden js-video">
                                                    <?= _r($this->lblVideo); ?>
                                                    <div class="col-md-9">
                                                        <div class="embed-responsive embed-responsive-16by9">
                                                            <?= _r($this->strVideo); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?= _r($this->lblContent); ?>
                                                    <div class="col-md-9">
                                                        <?= _r($this->txtContent); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?= _r($this->lblVideosGroupTitle); ?>
                                                    <div class="col-md-9">
                                                        <?= _r($this->lstGroupTitle); ?>
                                                        <?= _r($this->btnGoToSettings); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?= _r($this->lblVideoStatus); ?>
                                                    <div class="col-md-6">
                                                        <?= _r($this->lstVideoStatus); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-actions-wrapper" style="text-align: right;">
                                            <?= _r($this->btnUpdate); ?>
                                            <?= _r($this->btnDeleteVideo); ?>
                                            <?= _r($this->btnCloseWindow); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="video-wrapper">
                                                <?= _r($this->lblInfo); ?>
                                                <?= _r($this->dlgSorter); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-actions">
                                            <?= _r($this->btnSort); ?>
                                            <?= _r($this->btnBack); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 right-box padded-wrapper">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $this->RenderEnd(); ?>

<?php require('footer.inc.php'); ?>