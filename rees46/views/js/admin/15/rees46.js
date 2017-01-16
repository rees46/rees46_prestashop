/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    p0v1n0m <ay@rees46.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function() {
    $('#auth_toolbar #desc-auth-new').css('display', 'none');
    $('#auth_toolbar #desc-auth-newAttributes').css('display', 'none');

    $('#rees46_login').click(function() {
        $('#auth_form #fieldset_1').css('display', 'none');
        $('#auth_form #fieldset_0').fadeIn();
        $('#auth_toolbar #desc-auth-new').css('display', 'block');
        $('#auth_toolbar #desc-auth-newAttributes').css('display', 'none');
    });

    $('#rees46_register').click(function() {
        $('#auth_form #fieldset_0').css('display', 'none');
        $('#auth_form #fieldset_1').fadeIn();
        $('#auth_toolbar #desc-auth-new').css('display', 'none');
        $('#auth_toolbar #desc-auth-newAttributes').css('display', 'block');
    });

    $('#auth_toolbar #desc-auth-newAttributes').click(function() {
        rees46UserRegister();
    });

    $('#auth_toolbar #desc-auth-new').click(function() {
        rees46ShopXML(true);
    });

    $('#submitShopXML').click(function() {
        rees46ShopXML();
    });

    $('#submitShopOrders').click(function() {
        rees46ShopOrders();
    });

    $('#submitShopCustomers').click(function() {
        rees46ShopCustomers();
    });

    $('#submitShopFile1').click(function() {
        rees46ShopFiles();
    });

    $('#submitShopFile2').click(function() {
        rees46ShopFiles();
    });
});

function getToken() {
    var rees46_token = $('#rees46_secure_key').val();

    return rees46_token;
}

function rees46UserRegister() {
    $.ajax({
        url: module_dir + 'rees46/ajax.php',
        data: {
            ajax: true,
            token: getToken(),
            action: 'rees46UserRegister',
            email: $('#auth_email').val(),
            phone: $('#auth_phone').val(),
            first_name: $('#auth_first_name').val(),
            last_name: $('#auth_last_name').val(),
            country_code: $('#auth_country_code').val(),
            category: $('#auth_category').val()
        },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            $('#auth_toolbar #desc-auth-new').css('display', 'none');
            $('#auth_toolbar #desc-auth-newAttributes').css('display', 'none');
        },
        success: function(json) {
            if (json['success']) {
                $('#content > table:first-child').after('<div class="module_confirmation conf confirm rees46">' + json['success'] + '</div>');

                rees46ShopRegister();
            }

            if (json['error']) {
                $('#auth_toolbar #desc-auth-newAttributes').css('display', 'block');

                $('#content > table:first-child').after('<div class="module_error alert error rees46">' + json['error'] + '</div>');
            }
        }
    });
}

function rees46ShopRegister() {
    $.ajax({
        url: module_dir + 'rees46/ajax.php',
        data: {
            ajax: true,
            token: getToken(),
            action: 'rees46ShopRegister'
        },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            $('#auth_toolbar #desc-auth-new').css('display', 'none');
            $('#auth_toolbar #desc-auth-newAttributes').css('display', 'none');
        },
        success: function(json) {
            if (json['success']) {
                $('#content > table:first-child').after('<div class="module_confirmation conf confirm rees46">' + json['success'] + '</div>');

                rees46ShopXML(true);
            }

            if (json['error']) {
                $('#auth_toolbar #desc-auth-newAttributes').css('display', 'block');

                $('#content > table:first-child').after('<div class="module_error alert error rees46">' + json['error'] + '</div>');
            }
        }
    });
}

function rees46ShopXML(auth = false) {
    $.ajax({
        url: module_dir + 'rees46/ajax.php',
        data: {
            ajax: true,
            token: getToken(),
            action: 'rees46ShopXML',
            store_key: $('#auth_store_key').val(),
            secret_key: $('#auth_secret_key').val()
        },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            $('#submitShopXML').css('display', 'none');
        },
        success: function(json) {
            if (json['success']) {
                $('#submitShopXML').parent().prev('.text-center').html('<img src="../img/admin/enabled.gif" alt="enabled.gif" title="enabled.gif">');
                $('#submitShopXML').remove();

                $.map(json['success'], function(success) {
                    $('#content > table:first-child').after('<div class="module_confirmation conf confirm rees46">' + success + '</div>');
                });

                if (auth) {
                    rees46ShopOrders(1, true);
                }
            }

            if (json['error']) {
                $('#submitShopXML').css('display', 'block');

                $('#content > table:first-child').after('<div class="module_error alert error rees46">' + json['error'] + '</div>');
            }
        }
    });
}

function rees46ShopOrders(next = 1, auth = false) {
    $.ajax({
        url: module_dir + 'rees46/ajax.php',
        data: {
            ajax: true,
            token: getToken(),
            action: 'rees46ShopOrders',
            next: next,
        },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            $('#submitShopOrders').css('display', 'none');
        },
        success: function(json) {
            if (json['success']) {
                $('#content > table:first-child').after('<div class="module_confirmation conf confirm rees46">' + json['success'] + '</div>');
            }

            if (json['next']) {
                rees46ShopOrders(json['next']);
            } else {
                if (!auth && json['success']) {
                    $('#submitShopOrders').parent().prev('.text-center').html('<img src="../img/admin/enabled.gif" alt="enabled.gif" title="enabled.gif">');
                    $('#submitShopOrders').remove();
                }

                if (auth) {
                    rees46ShopCustomers(1, true);
                }
            }

            if (json['error']) {
                $('#submitShopOrders').css('display', 'block');

                $('#content > table:first-child').after('<div class="module_error alert error rees46">' + json['error'] + '</div>');
            }
        }
    });
}

function rees46ShopCustomers(next = 1, auth = false) {
    $.ajax({
        url: module_dir + 'rees46/ajax.php',
        data: {
            ajax: true,
            token: getToken(),
            action: 'rees46ShopCustomers',
            next: next,
        },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            $('#submitShopCustomers').css('display', 'none');
        },
        success: function(json) {
            if (json['success']) {
                $('#content > table:first-child').after('<div class="module_confirmation conf confirm rees46">' + json['success'] + '</div>');
            }

            if (json['next']) {
                rees46ShopCustomers(json['next']);
            } else {
                if (!auth && json['success']) {
                    $('#submitShopCustomers').parent().prev('.text-center').html('<img src="../img/admin/enabled.gif" alt="enabled.gif" title="enabled.gif">');
                    $('#submitShopCustomers').remove();
                }

                if (auth) {
                    rees46ShopFiles(true);
                }
            }

            if (json['error']) {
                $('#submitShopCustomers').css('display', 'block');

                $('#content > table:first-child').after('<div class="module_error alert error rees46">' + json['error'] + '</div>');
            }
        }
    });
}

function rees46ShopFiles(auth = false) {
    $.ajax({
        url: module_dir + 'rees46/ajax.php',
        data: {
            ajax: true,
            token: getToken(),
            action: 'rees46ShopFiles',
        },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            $('#submitShopFile1').css('display', 'none');
            $('#submitShopFile2').css('display', 'none');
        },
        success: function(json) {
            if (json['success']) {
                $('#submitShopFile1').parent().prev('.text-center').html('<img src="../img/admin/enabled.gif" alt="enabled.gif" title="enabled.gif">');
                $('#submitShopFile2').parent().prev('.text-center').html('<img src="../img/admin/enabled.gif" alt="enabled.gif" title="enabled.gif">');
                $('#submitShopFile1').remove();
                $('#submitShopFile2').remove();

                $.map(json['success'], function(success) {
                    $('#content > table:first-child').after('<div class="module_confirmation conf confirm rees46">' + success + '</div>');
                });
            }

            if (json['error']) {
                $('#submitShopFile1').css('display', 'block');
                $('#submitShopFile2').css('display', 'block');

                $.map(json['error'], function(error) {
                    $('#content > table:first-child').after('<div class="module_error alert error rees46">' + error + '</div>');
                });
            }

            if (auth) {
                if ($('#auth_store_key').val() == '' && $('#auth_secret_key').val() == '') {
                    rees46ShopFinish();
                } else {
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            }
        }
    });
}

function rees46ShopFinish() {
    $.ajax({
        url: module_dir + 'rees46/ajax.php',
        data: {
            ajax: true,
            token: getToken(),
            action: 'rees46ShopFinish',
        },
        type: 'post',
        dataType: 'json',
        success: function(json) {
            if (json) {
                $('body').append(json);

                setTimeout(function() {
                    $('#submitShopFinish').submit();
                }, 1000);

                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        }
    });
}
