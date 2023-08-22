{*
 * PayEye
 *
 * @author    PayEye
 * @copyright Copyright (c) 2023 PayEye
 * @license   http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
*}

{if $PAYEYE_MODULE_VERSION.update}
    <div class="panel">
        <div class="panel-heading">Informacja</div>
        <p>
            Obecna wersja: {$PAYEYE_MODULE_VERSION.current}
            <br>
            Nowa wersja modułu: {$PAYEYE_MODULE_VERSION.version}
            <br>
            Adres url: <a href="{$PAYEYE_MODULE_VERSION.url}">{$PAYEYE_MODULE_VERSION.url}</a>
        </p>
    </div>
{/if}
