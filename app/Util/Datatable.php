<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 01.06.2019
 * Time: 14:33
 */

namespace App\Util;


class Datatable
{
    public static function language(){
        return "
        language:{
                \"decimal\": \",\",
                \"thousands\": \".\",
                \"sEmptyTable\":   	\"". __('datatable.sEmptyTable') ."\",
                \"sInfo\":         	\"". __('datatable.sInfo') ."\",
                \"sInfoEmpty\":    	\"". __('datatable.sInfoEmpty') ."\",
                \"sInfoFiltered\": 	\"". __('datatable.sInfoFiltered') ."\",
                \"sInfoPostFix\":  	\"\",
                \"sInfoThousands\":  	\"". __('datatable.sInfoThousands') ."\",
                \"sLengthMenu\":   	\"". __('datatable.sLengthMenu') ."\",
                \"sLoadingRecords\": 	\"". __('datatable.sLoadingRecords') ."\",
                \"sProcessing\":   	\"". __('datatable.sProcessing') ."\",
                \"sSearch\":       	\"". __('datatable.sSearch') ."\",
                \"sZeroRecords\":  	\"". __('datatable.sZeroRecords') ."\",
                \"oPaginate\": {
                    \"sFirst\":    	\"". __('datatable.oPaginate_sFirst') ."\",
                    \"sPrevious\": 	\"". __('datatable.oPaginate_sPrevious') ."\",
                    \"sNext\":     	\"". __('datatable.oPaginate_sNext') ."\",
                    \"sLast\":     	\"". __('datatable.oPaginate_sLast') ."\"
                },
                \"oAria\": {
                    \"sSortAscending\":  \"". __('datatable.oAria_sSortAscending') ."\",
                    \"sSortDescending\": \"". __('datatable.oAria_sSortDescending') ."\"
                },
                \"select\": {
                    \"rows\": {
                        \"_\": \"". __('datatable.select_rows__') ."\",
                        \"0\": \"\",
                        \"1\": \"". __('datatable.select_rows_1') ."\"
                    }
                },
                \"buttons\": {
                    \"print\":	\"". __('datatable.buttons_print') ."\",
                    \"colvis\":	\"". __('datatable.buttons_colvis') ."\",
                    \"copy\":		\"". __('datatable.buttons_copy') ."\",
                    \"copyTitle\":	\"". __('datatable.buttons_copyTitle') ."\",
                    \"copyKeys\":	\"". __('datatable.buttons_copyKeys') ."\",
                    \"copySuccess\": {
                        \"_\": \"". __('datatable.buttons_copySuccess__') ."\",
                        \"1\": \"". __('datatable.buttons_copySuccess_1') ."\"
                    },
                    \"pageLength\": {
                        \"-1\": \"". __('datatable.buttons_pageLength_-1') ."\",
                        \"_\":  \"". __('datatable.buttons_pageLength__') ."\"
                    }
                }
            }
        ";
    }
}
