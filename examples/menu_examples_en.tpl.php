<?php $strPageTitle = 'Examples of menu tree' ; ?>

<?php require('examples-header.inc.php'); ?>
<?php $this->RenderBegin(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
                    <!-- MENU CONTAINER BEGIN -->
                    <div class="panel-body">
                        <h3 class="panel-title" style="margin-bottom: -10px;">Examples of menu tree</h3>
                        <div class="panel-examples">

                            <div class="row">
                                <div class="col-md-12">
                                    <p>These examples will show you how to use menu trees in each site. If to use in
                                        backend dynamic plugin. This plugin is developed to be used for QCubed v4 version.</p>
                                    <p>I will give examples of menu trees with three levels. It is possible to add levels
                                        in unlimited number. It is usually a good practice to limit menu tree levels up to
                                        two-three as it makes it easier for site visitors to navigate in your webpage and
                                        find information.</p>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <h4>Example: NaturalList</h4>
                                <p>The first example uses <i>the class of NaturalList</i> and its aim is to show natural
                                    list without styles or the support of javascript. Try to experiment with <a href="menu_manager.php">
                                        menu manager</a> and come back here. In order to do so, you need to refresh your browser.</p>
                                    <p><strong><i>Class of the NaturalList</i></strong> is a good base to use, if you want
                                        to create menu which has various looks and javascript effects. For these use functions
                                        <i>renderMenuTree()</i> and <i>makeJqWidget()</i>.</p>
                                </div>
                                <div class="col-md-4 col-md-offset-4" style="margin-bottom: 10px; border: #cdcdcd 1px solid">
                                    <?= _r($this->tblList); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <h4>Example: Bootstrap Navbar</h4>
                                    <p>The second example uses possibilities of <i>the QCubed Bootstrap</i> class, to be
                                        <i>more specific – NavabarList</i>. Official Bootstrap does not support multi-level
                                        levels, you can only display 2 levels.</p>
                                    <p>In this case you need to set <strong>the value to „2“ in NestedSortable <i>MaxLevel</i></strong>,
                                        which will limit the depth of the required levels.</p>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <?=  _r($this->navBar); ?>
                                    <div class="col-md-12" style="border: #ccc 2px dotted; min-height: 220px;">
                                        <h4>Home</h4>
                                        <p>Lorem ipsum dolor sit amet, exerci fastidii detracto in mel, alterum probatus scripserit te quo.
                                            Falli labore et eum, cibo posse scripserit in qui. Ne vix enim platonem accusamus.
                                            Mei et sint everti, mea discere erroribus ei, eam an omnes postea repudiandae.
                                            Ei blandit vituperata quo, in pro justo suavitate. Te case cibo tritani per.
                                            Nec sumo consequat ei, amet animal vis te.</p>
                                        <p>An placerat periculis mediocritatem has, ipsum officiis id sed. Ex nec error eripuit.
                                            Ut quo justo aeterno ceteros, eam ei etiam error. Ea has choro fabulas, quidam facete
                                            voluptaria te mel. Luptatum similique vituperatoribus mei ex. Nec cetero menandri
                                            abhorreant cu, ex aeterno debitis veritus eos.</p>
                                        <p>Cum eu etiam possit utamur, dolorum corrumpit at his, duo tempor inermis elaboraret eu.
                                            Vel ad summo dicit liberavisse, ut esse homero has. Everti vidisse dolores eos in.
                                            Dolorum complectitur at mel. His corrumpit expetendis in, ut usu posse movet,
                                            praesent dignissim has no.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <h4>Example: SmartMenus Bootstrap Addon (Navbar)</h4>
                                    <p>The third example uses the alternative of Bootstrap Navbar –  <i>SmartMenus</i>.
                                        In case you don’t find the two-leveled opportunities of official Bootstrap Navbar
                                        satisfactory. This example uses the opportunities of the
                                        <a href="https://www.smartmenus.org" target="_blank">SmartMenus</a> plugin.</p>
                                    <p>SmartMenus class, which is based on <i>the class of NaturalList</i>, used with
                                        <i>QCubed Bootstrap Navbar</i> will give us the wanted result.</p>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <?= _r($this->smartMenus); ?>
                                    <div class="col-md-12" style="border: #ccc 2px dotted; min-height: 220px;">
                                        <h4>Home</h4>
                                        <p>Lorem ipsum dolor sit amet, exerci fastidii detracto in mel, alterum probatus scripserit te quo.
                                            Falli labore et eum, cibo posse scripserit in qui. Ne vix enim platonem accusamus.
                                            Mei et sint everti, mea discere erroribus ei, eam an omnes postea repudiandae.
                                            Ei blandit vituperata quo, in pro justo suavitate. Te case cibo tritani per.
                                            Nec sumo consequat ei, amet animal vis te.</p>
                                        <p>An placerat periculis mediocritatem has, ipsum officiis id sed. Ex nec error eripuit.
                                            Ut quo justo aeterno ceteros, eam ei etiam error. Ea has choro fabulas, quidam facete
                                            voluptaria te mel. Luptatum similique vituperatoribus mei ex. Nec cetero menandri
                                            abhorreant cu, ex aeterno debitis veritus eos.</p>
                                        <p>Cum eu etiam possit utamur, dolorum corrumpit at his, duo tempor inermis elaboraret eu.
                                            Vel ad summo dicit liberavisse, ut esse homero has. Everti vidisse dolores eos in.
                                            Dolorum complectitur at mel. His corrumpit expetendis in, ut usu posse movet,
                                            praesent dignissim has no.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <h4>Example: Using Bootstrap Navbar with SideBar</h4>
                                    <p>Sometimes you might want the following: the first level of the menu tree is displayed
                                        in Bootstrap Navbar and next levels of the menu tree are displayed either left or
                                        right next to the content.</p>
                                    <p>The fourth example will show how to do it using classes of css and javascript.
                                        You also need to use <i>the class of NaturalList</i> and adapt to <i>the class of SideBar</i>.</p>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <?= _r($this->sideMenu); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div id="sidebar">
                                        <nav class="submenu">
                                            <ul>
                                                <?= _r($this->tblSubMenu); ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                                <div class="col-md-9" style="border: #ccc 2px dotted; min-height: 320px;">
                                    <h4>Home</h4>
                                    <p>Lorem ipsum dolor sit amet, exerci fastidii detracto in mel, alterum probatus scripserit te quo.
                                        Falli labore et eum, cibo posse scripserit in qui. Ne vix enim platonem accusamus.
                                        Mei et sint everti, mea discere erroribus ei, eam an omnes postea repudiandae.
                                        Ei blandit vituperata quo, in pro justo suavitate. Te case cibo tritani per.
                                        Nec sumo consequat ei, amet animal vis te.</p>
                                    <p>An placerat periculis mediocritatem has, ipsum officiis id sed. Ex nec error eripuit.
                                        Ut quo justo aeterno ceteros, eam ei etiam error. Ea has choro fabulas, quidam facete
                                        voluptaria te mel. Luptatum similique vituperatoribus mei ex. Nec cetero menandri
                                        abhorreant cu, ex aeterno debitis veritus eos.</p>
                                    <p>Cum eu etiam possit utamur, dolorum corrumpit at his, duo tempor inermis elaboraret eu.
                                        Vel ad summo dicit liberavisse, ut esse homero has. Everti vidisse dolores eos in.
                                        Dolorum complectitur at mel. His corrumpit expetendis in, ut usu posse movet,
                                        praesent dignissim has no.</p>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="margin-bottom: 20px;">
                                        <h4>Example: NestedSidebar</h4>
                                        <p>Sometimes it may be necessary to show a site, for example at the request of
                                            the subscriber, where Bootstrap Navbar is not used and the menu tree is
                                            displayed next to the content on the left or right.</p>
                                        <p>In the fifth example, we show how this can be done using css classes and javascript.
                                            This is a simplified example, developers can improve it on css or javascript
                                            or php.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div id="nestedmenu">
                                            <?= _r($this->tblNestedMenu); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-9" style="border: #ccc 2px dotted; min-height: 320px;">
                                        <h4>Home</h4>
                                        <p>Lorem ipsum dolor sit amet, exerci fastidii detracto in mel, alterum probatus scripserit te quo.
                                            Falli labore et eum, cibo posse scripserit in qui. Ne vix enim platonem accusamus.
                                            Mei et sint everti, mea discere erroribus ei, eam an omnes postea repudiandae.
                                            Ei blandit vituperata quo, in pro justo suavitate. Te case cibo tritani per.
                                            Nec sumo consequat ei, amet animal vis te.</p>
                                        <p>An placerat periculis mediocritatem has, ipsum officiis id sed. Ex nec error eripuit.
                                            Ut quo justo aeterno ceteros, eam ei etiam error. Ea has choro fabulas, quidam facete
                                            voluptaria te mel. Luptatum similique vituperatoribus mei ex. Nec cetero menandri
                                            abhorreant cu, ex aeterno debitis veritus eos.</p>
                                        <p>Cum eu etiam possit utamur, dolorum corrumpit at his, duo tempor inermis elaboraret eu.
                                            Vel ad summo dicit liberavisse, ut esse homero has. Everti vidisse dolores eos in.
                                            Dolorum complectitur at mel. His corrumpit expetendis in, ut usu posse movet,
                                            praesent dignissim has no.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="margin-top: 10px; margin-bottom: 10px;">
                                        <h4>Summary</h4>
                                        <p>Here you must keep in mind that there are many possibilities and solutions to
                                            link Bootstrap Navbar or SmartMenus Navbar and/or Sidebar content. Each of
                                            the developer will choose for themselves.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- MENU CONTAINER BEGIN -->
                        </div>
                    </div>
                </div>
            <!-- END PAGE CONTENT-->
            </div>
        </div>
        <!-- BEGIN CONTENT -->
<?php $this->RenderEnd(); ?>
<?php require('footer.inc.php'); ?>


