/**
 * @file   modules/zend_picasa/insert_module.js
 * @author 1sam (csh@korea.com)
 **/

/* 모듈 생성 후 */
function completeInsertModule(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    var module_srl = ret_obj['module_srl'];

    alert(message);

    var url = current_url.setQuery('act','dispZend_picasaAdminModule');
    if(module_srl) url = url.setQuery('module_srl',module_srl);
    location.href = url;
}

/* 모듈 삭제 후 */
function completeDeleteModule(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var page = ret_obj['page'];
    alert(message);

    var url = current_url.setQuery('act','dispZend_picasaAdminConfig').setQuery('module_srl','');
    if(page) url = url.setQuery('page',page);
    location.href = url;
}
