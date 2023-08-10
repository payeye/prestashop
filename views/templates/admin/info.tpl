{*
 * PayEye
 *
 * @author    PayEye
 * @copyright Copyright (c) 2023 PayEye
 * @license   http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
*}

<div class="panel">
    <div class="panel-heading">Informacja</div>
    <p>

    </p>
</div>

<script>
    $.ajax({
        url: "https://static.payeye.com/e-commerce/modules/prestashop/e-payeye/version.json",
        success: function ($params) {
            console.log($params);
        }
    });
</script>
