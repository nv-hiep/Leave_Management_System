<div class="header-top">
    <div class="wrap">
        <div class="header-top-left">
            <div class="box">
                <select tabindex="4" class="dropdown">
                    <?php foreach ($sys_lang as $key => $lang): ?>
                    <option value="<?= $key; ?>" class="label"><?= $lang; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="box1">
                <select tabindex="4" class="dropdown">
                    <?php foreach ($sys_curr as $key => $curr): ?>
                    <option value="<?= $key; ?>" class="label"><?= $curr['symbol']; ?> <?= $curr['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="clear"></div>
        </div>
        <div class="cssmenu">
            <ul>
                <!--manager's menu-->
                <?php if (isset($login_user) and Auth::has_access("admin.admin[index]")): ?>
                    <li><?php echo Html::anchor('admin/index', __('menu.manager')) ?></li> |
                <?php endif; ?>
                <li><a href="checkout.html">Wishlist</a></li> |
                <li><?php echo Html::anchor('account/register', __('menu.signup')) ?></li> |
                <?php if (isset($login_user)) : ?>
                <li><?php echo Html::anchor('user/info', __('menu.account') . ' (' . $login_user->username . ')' ) ?></li> |
                <li><?php echo Html::anchor('base/logout', __('menu.logout')) ?></li>
                <?php else : ?>
                <li><?php echo Html::anchor('user/login', __('menu.login')) ?></li>
                <?php endif; ?>

            </ul>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="header-bottom">
    <div class="wrap">
        <div class="header-bottom-left">
            <div class="logo">
                <a href="<?php echo Uri::base(); ?>home"><img src="<?php echo Uri::base(); ?>assets/img/logo.png" alt=""/></a>
            </div>
            <div class="menu">
                <ul class="megamenu skyblue">
                    <li class="active grid"><a href="index.html">Home</a></li>
                    <li><a class="color4" href="#">women</a>
                        <div class="megapanel">
                            <div class="row">
                                <div class="col1">
                                    <div class="h_nav">
                                        <h4>Contact Lenses</h4>
                                        <ul>
                                            <li><a href="womens.html">Daily-wear soft lenses</a></li>
                                            <li><a href="womens.html">Extended-wear</a></li>
                                            <li><a href="womens.html">Lorem ipsum </a></li>
                                            <li><a href="womens.html">Planned replacement</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col1">
                                    <div class="h_nav">
                                        <h4>Sun Glasses</h4>
                                        <ul>
                                            <li><a href="womens.html">Heart-Shaped</a></li>
                                            <li><a href="womens.html">Square-Shaped</a></li>
                                            <li><a href="womens.html">Round-Shaped</a></li>
                                            <li><a href="womens.html">Oval-Shaped</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col1">
                                    <div class="h_nav">
                                        <h4>Eye Glasses</h4>
                                        <ul>
                                            <li><a href="womens.html">Anti Reflective</a></li>
                                            <li><a href="womens.html">Aspheric</a></li>
                                            <li><a href="womens.html">Bifocal</a></li>
                                            <li><a href="womens.html">Hi-index</a></li>
                                            <li><a href="womens.html">Progressive</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li><a class="color5" href="#">Men</a>
                        <div class="megapanel">
                            <div class="col1">
                                <div class="h_nav">
                                    <h4>Contact Lenses</h4>
                                    <ul>
                                        <li><a href="mens.html">Daily-wear soft lenses</a></li>
                                        <li><a href="mens.html">Extended-wear</a></li>
                                        <li><a href="mens.html">Lorem ipsum </a></li>
                                        <li><a href="mens.html">Planned replacement</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col1">
                                <div class="h_nav">
                                    <h4>Sun Glasses</h4>
                                    <ul>
                                        <li><a href="mens.html">Heart-Shaped</a></li>
                                        <li><a href="mens.html">Square-Shaped</a></li>
                                        <li><a href="mens.html">Round-Shaped</a></li>
                                        <li><a href="mens.html">Oval-Shaped</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col1">
                                <div class="h_nav">
                                    <h4>Eye Glasses</h4>
                                    <ul>
                                        <li><a href="mens.html">Anti Reflective</a></li>
                                        <li><a href="mens.html">Aspheric</a></li>
                                        <li><a href="mens.html">Bifocal</a></li>
                                        <li><a href="mens.html">Hi-index</a></li>
                                        <li><a href="mens.html">Progressive</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li><a class="color6" href="other.html">Other</a></li>
                    <li><a class="color7" href="other.html">Purchase</a></li>
                </ul>
            </div>
        </div>
        <div class="header-bottom-right">
            <div class="search">
                <input type="text" name="s" class="textbox" value="Search" onfocus="this.value = '';" onblur="if (this.value == '') {
                            this.value = 'Search';
                        }">
                <input type="submit" value="Subscribe" id="submit" name="submit">
                <div id="response"> </div>
            </div>
            <div class="tag-list">
                <ul class="icon1 sub-icon1 profile_img">
                    <li><a class="active-icon c1" href="#"> </a>
                        <ul class="sub-icon1 list">
                            <li><h3>sed diam nonummy</h3><a href=""></a></li>
                            <li><p>Lorem ipsum dolor sit amet, consectetuer  <a href="">adipiscing elit, sed diam</a></p></li>
                        </ul>
                    </li>
                </ul>
                <ul class="icon1 sub-icon1 profile_img">
                    <li><a class="active-icon c2" href="#"> </a>
                        <ul class="sub-icon1 list">
                            <li><h3>No Products</h3><a href=""></a></li>
                            <li><p>Lorem ipsum dolor sit amet, consectetuer  <a href="">adipiscing elit, sed diam</a></p></li>
                        </ul>
                    </li>
                </ul>
                <ul class="last"><li><a href="#">Cart(0)</a></li></ul>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>