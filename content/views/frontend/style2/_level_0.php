<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kim
 * Date: 8/14/12
 * Time: 2:56 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<?php $riview->get('loader')->load(array('bootstrap.lib', 'riMenu::frontend/style2/css/menu.css'));?>
<div class="navbar wrapper-978">
    <div class="navbar-inner">
        <div class="container">
            <!-- Everything you want hidden at 940px or less, place within here -->
            <div class="nav-collapse">
            <!-- .nav, .navbar-search, .navbar-form, etc -->
                <ul class="nav pull-left">
                    <li class="active"><a href="#">HOME</a></li>
                        <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">SHOP COLLECTIONS</a>
                            <div id="top">
                            <div id="mid">
                            <ul class="dropdown-menu-hover">
                                <li><a>Boutonnieres</a></li>
                                <li><a>Colorful Blossoms</a></li>
                                <li><a>Custom orders</a></li>
                                <li><a>Dress Adornments</a></li>
                                <li><a>Shoe Clips</a></li>
                            </ul>
                            </div>
                            </div>
                        </li>
                    <li><a href="#">CATEGORIES</a></li>
                    <li><a href="#">EVENT</a></li>
                    <li><a href="#">ABOUT</a></li>
                    <li><a href="#">BLOG</a></li>
                </ul>
                <form class="form-search pull-right">
                    <input type="text" class="search-query" value="Search...">
                </form>
            </div>
        </div>
    </div>
</div>