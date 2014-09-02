/**
 * Класс для работы с фильтрами
 * 
 * @param {dataTable} $table
 * @returns {SlFilters}
 */
function SlFilters($table) {
    var self = this;
    var _filters = {};
    var _table = $table;

    var _system_fields = ['id', 'active', 'create', 'timestamp', 'archived'];
    
    this.addFilter = function(field, v, t, orig, notify) {
        t = t || 'like';
        v = v || '';
        switch(v.charAt(0)) {
            case '<':
                v = v.split('').slice(1).join('');
                t = 'lt';
                if(v.charAt(0) === '=') {
                    t = 'e' + t;
                    v = v.split('').slice(1).join('');
                }
                break;
            case '>':
                v = v.split('').slice(1).join('');
                t = 'gt';
                if(v.charAt(0) === '=') {
                    t = 'e' + t;
                    v = v.split('').slice(1).join('');
                }
                break;
            case '=':
                v = v.split('').slice(1).join('');
                t = 'eq';
                break;
            case '!':
                v = v.split('').slice(1).join('');
                t = 'n'+t;
                break;
        }
        _filters[field] = {
            type: t,
            value: v,
            label: field,
            name: field,
            _field: orig
        };
        if($(_table).find('thead th[data-name="'+field+'"]').length) {
            _filters[field].label = $(_table).find('thead th[data-name="'+field+'"]').text().replace(/([^a-zA-Zа-яА-Я])/g, '$1');
        }
        if(notify !== false) {
            self.notify();
        }
    };
    
    this.addFilters = function(filters) {
        try {
            _.each(filters, function(o, i){
                self.addFilter(o.field, o.value, o.type, o.orig, false);
            });
            self.notify();
        } catch(e) {
            console.log(e.message);
        }
    };
    // Обертка для функции, запускающая ее гарантированно не чаде чем заданный интервал
    this.notify = _.throttle(function() {
        $(_table).trigger('filter_changed.sl'); // Что будем делать
    }, 1000, { // Таймаут
        leading: false // Не запускать по переднему фронту
    });

    this.getFilters = function(ignore_system) {
        if(ignore_system === false) {
            return _filters;
        } else {
            var res = {};
            _.each(_filters, function(f, name){
                if(!_.contains(_system_fields, name)) {
                    res[name] = f;
                }
            });
            return res;
        }
    };

    this.cleanFilters = function(notify) {
        _.each(self.getFilters(), function(f, name){
            var el = _table.find('thead th[data-name="'+name+'"]');
            if(el.is('.type_select')) {
                $(el).find('select').val('-1').change();
            } else {
                _.each($(el).find('input'), function(i){
                    $(i).val('').change();
                });
            };
            self.removeFilter(name);
        });
        if(notify !== false) {
            self.notify();
        }
    };

    this.removeFilter = function(field) {
        delete _filters[field];
        self.notify();
    };
    
    this.describeFilter = function(field_name) {
        var filter;
        if(typeof field_name === 'object') {
            filter = field_name;
        } else {
            filter = _table.data('controller').findField(field_name);
        }
        var name = filter.label, type = '', value = filter.value;
        // Имя
        if(filter._field) {
            name = filter._field.sLabel;
        }
        // Тип сравнения
        switch(filter.type) {
            case 'eq': type = 'равно'; break;
            case 'neq': type = 'не равно'; break;
            case 'like': type = 'содержит'; break;
            case 'nlike': type = 'не содержит'; break;
            case 'gt': type = 'больше'; break;
            case 'egt': type = 'больше или равно'; break;
            case 'lt': type = 'меньше'; break;
            case 'elt': type = 'меньше или равно'; break;
            case 'between': type = 'в промежутке'; break;
            default:
                type = filter.type;
        }
        // Значение
        if(filter._field && typeof filter._field === 'object') {
            var f = filter._field;
            if(f.sType) {
                if(f.sType === 'select') {
                    value = _table.find('th[data-name="'+filter.name+'"] > select:first [value="'+filter.value+'"]').text().toLowerCase();
                }
                if(f.sType === 'date') {
                    value = value.split('::').join(' - ');
                }
            }
        }
        return $('<div />').append($('#filterDescriptor').tmpl({
            name: name,
            type: type,
            value: value,
            formated: true
        })).html();
        //return [name, type, value].join(' ');
    };
}

function listviewController($wrapper, opts) {
    var self = this;
    var _wrapper = $wrapper;
    var _is_popup = null;
    
    
    this.isPopup = function(as_int) {
        if(null === _is_popup) {
            if(_wrapper.find('[data-role="popup_view"]').length) {
                _is_popup = parseInt(_wrapper.find('[data-role="popup_view"]').val());
            } else {
                _is_popup = 0;
            }
        }
        if(true === as_int) {
            return _is_popup;
        }
        return !(_is_popup === 0);
    };
    
    var _return_fields = [];
    
    this.getReturnFields = function(plain) {
        if(plain === true) {
            return self.getReturnFields().join(',');
        }
        return _return_fields;
    };
    
    if($('.fields_to_return', _wrapper).length > 0) {
        _return_fields = $('.fields_to_return', _wrapper).data('fields');
    }
    
    var _default_options = {
        useFolders: true,
        fnRedrawTable: function() {
            document.location.href = cleanBaseUrl();
        },
        setupButton: false,
        fnSetupCallback: function() {
            
        },
        configureState: false
    };
    var options = _.extend(_default_options, opts || {});
    
    $wrapper.data('controller', self);
    
    var _table = $wrapper.find('table.table:first');
    var _selected = $wrapper.find('.selected_models_wrapper');
    
    var _folders = [];
    var _selected_data = [];
    var _ep = new SlEntryPoint(_wrapper);
    
    var _aoCols = _.map($wrapper.find('.fieldsinfo:first span'), function(o){
        return $(o).data();
    }) || [];
    
    var _aoComps = _.map($wrapper.find('.comps_info:first span'), function(o){
        return $(o).data();
    }) || [];
    
    var _aaSorting = [[0, 'desc']];
    var _aOrderData = $('.order_data').data();
    _.each(_aoCols, function(col, i){
        if(col.sName === _aOrderData.field) {
            _aaSorting[0] = [i, _aOrderData.dir];
        }
    });
    
    var _aoColDef = _.filter(_aoCols, function(o) {
        return _.contains(o.aRoles, 'render');
    });
    
    this.redrawPage = options.fnRedrawTable;
    
    this.getTable = function() {
        return _table;
    };
    
    this.getFilters = function() {
        return _filters;
    };
    
    var _tableFn = {
        fnServerParams: function(aoData, return_result){
            // Даем возможность наполнить извне
            self.getTable().trigger('fn_serverparams.sf', {
                aoData: aoData
            });
            // Информация о колонках
            _.each(_aoCols, function(o){
                aoData.push({
                    name: 'cols['+o.sName+'][roles]',
                    value: o.aRoles
                });
                aoData.push({
                    name: 'cols['+o.sName+'][type]',
                    value: o.sType
                });
            });
            _.each(_aoComps, function(o, i){
                aoData.push({
                    name: 'comps['+i+'][type]',
                    value: o.type
                });
                aoData.push({
                    name: 'comps['+i+'][field]',
                    value: o.field
                });
                aoData.push({
                    name: 'comps['+i+'][value]',
                    value: o.value
                });
            });
            
            self.getTable().trigger('columns_changed.sl', {
                columns: self.getTable().dataTable().fnSettings().aoColumns
            });
            // Информация о попапе
            aoData.push({
                name: 'popup',
                value: self.isPopup(true)
            });
            // Информация о фильтрах
            _.each(self.getFilters().getFilters(false), function(filter, fieldname){
                aoData.push({
                    name: 'filters['+filter.type+']['+fieldname+']',
                    value: filter.value
                });
            });
            // Текущая "папка"
            if(_folders.length) {
                var name = '_default';
                try {
                    if(!_.some(self.getFolders(), function(f){
                        return f.active;
                    })) {
                        name = _.first(self.getFolders()).name;
                        self.setActiveFolder(name);
                    } else {
                        name = self.getActiveFolderName();
                    }
                } catch(e) {
                    console.log(e.message);
                }
                aoData.push({
                    name: 'filter',
                    value: name
                });
            }
            // Если кому-то понадобится то, что насобиралось
            if(return_result === true) {
                return aoData;
            }
        },
        fnDrawCallback: function(){
            this.trigger('tabledraw.sl', this);
            // TODO Убрать
            $('body').trigger('dtdraw', this);
        },
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
            // Даем возможность вмешаться
            this.trigger('rowcallback.sl', {
                table: this,
                nRow: nRow,
                aData: aData,
                iDisplayIndex: iDisplayIndex,
                iDisplayIndexFull: iDisplayIndexFull
            });
            
            var _metas = aData['_meta']; // Наполняется при обработке данных на сервере
            if(_metas) {
                // Переписываем все из 'data' в аттрибуты элемента
                if(_metas['data']) {
                    _.each(_metas['data'], function(o, i){
                        $(nRow).attr('data-'+i, o);
                    });
                }
                // Переписываем информацию по полям, если она есть
                if(_metas['_by_field']) {
                    _.each(_metas['_by_field'], function(o, i){
                        if(i === 'classes') {
                            _.each(o, function(classes, colname){
                                var ind = 1+$('thead tr:first [data-name="'+colname+'"]', this).prevAll('[data-name]').length;
                                $('td:nth-child('+ind+')', nRow).addClass(classes.join(' '));
                            }, this);
                        }
                    }, this);
                }
                // Назначаем классы
                if(_metas['classes']) {
                    $(nRow).addClass(_metas['classes'].join(' '));
                }
                // Dblclick
                $(nRow).dblclick(function(){
                    if($(this).attr('data-link')) {
                        document.location.href = $(this).attr('data-link');
                    }
                });
            }
            if(options.useFolders) {
                // Draggable
                var highlightDraggedRow = function(id, unhighlight) {
                    if(unhighlight === false) {
                        self.getTable().find('tbody tr[data-real-id="'+id+'"]').removeClass('dragging');
                    } else {
                        self.getTable().find('tbody tr[data-real-id="'+id+'"]').addClass('dragging');
                    }
                };
                $(nRow).draggable({
                    handle: 'td',
                    cursor: 'move',
                    cursorAt: {top: 0, left: 30},
                    revert: 'invalid',
                    helper: function(e, ui){
                        var $span = $('<span class="label" />').text('В папку');
                        if($(nRow).find('.toggle_selection:first').prop('checked')) {
                            switch(_.size(self.getSelected())) {
                                case 1:
                                    $span.attr('data-id', self.getSelected().join(','));
                                    break;
                                default:
                                    $span.addClass('multi-drag');
                                    $span.attr('data-id', self.getSelected().join(','));
                                    $span.text($span.text()+' ('+_.size(self.getSelected())+')');
                            }
                            _.each(self.getSelected(), function(id){
                                highlightDraggedRow(id);
                            });
                            return $span;
                        } else {
                            highlightDraggedRow($(nRow).data('realId'));
                            return $span.attr('data-id', $(nRow).data('realId'));
                        }
                    },
                    stop: function(e, ui) {
                        if(ui.helper.hasClass('multi-drag')) {
                            _.each(ui.helper.attr('data-id').split(','), function(id) {
                                highlightDraggedRow(id, false);
                            });
                        } else {
                            highlightDraggedRow(ui.helper.attr('data-id'), false);
                        }
                    }
                });
            }
            return nRow;
        }
    };
    // Вызов переопределенных функций dataTable
    this.tableFn = function(name) {
        if(_tableFn[name]) {
            return _tableFn[name];
        }
        return null;
    };
    
    var _table = $wrapper.find('table.datatable:first');
    var _filters = new SlFilters(_table);
    
    var _folders_wrapper;
    
    this.getFoldersWrapper = function(){
        return _folders_wrapper;
    };
    
    this.getFolders = function() {
        return _folders;
    };
    
    this.addFolder = function(opts) {
        var o = _.extend({
            name: '',
            label: '',
            title: '',
            active: false
        }, opts);
        // У системных есть префикс _
        o.system = (o.name.toString().split('')[0] === '_');
        self.getFolders().push(o);
    };
    
    this.folderNotify = function() {
        _folders_wrapper.trigger('folders_changed.sl');
    };
    
    this.updateFolder = function(name, data, notify) {
        _.each(_folders, function(f, i){
            if(f.name === name) {
                _.extend(_folders[i], data);
                if(f.active) {
                    self.setActiveFolder(_folders[i].name);
                }
            }
        });
        if(notify === true) {
            self.folderNotify();
            self.redrawConfigure();
        }
    };
    
    this.findField = function(name) {
        var field = _.find(_aoCols, function(el) {
            return el.sName === name;
        });
        if(!field) {
            throw "Can\'t determine field";
        }
        return field;
    };
    
    this.recalcFolderDescriptions = function() {
        _.each(self.getFolders(), function(folder){
            var tFilters = new SlFilters(self.getTable());
            var t = '';
            var aT = [];
            if(folder.filters) {
                _.each(folder.filters, function(el, i){
                    tFilters.addFilter(i, el.value, el.type, self.findField(el.field), false);
                });
                _.each(tFilters.getFilters(), function(el) {
                    aT.push(tFilters.describeFilter(el));
                    t += tFilters.describeFilter(el)+"\r\n";
                });
            }
            folder._describedData = aT;
            folder._described = t + ((folder.title.length > 0)?("\r\n"+folder.title):'');
        });
    };
    
    _table.on('after_init.sl', function(){
        // Load described folders data
        self.recalcFolderDescriptions();
    });
    
    // Инициализация "папок"
    if(_wrapper.find('.filters_data span').length) {
        if(options.useFolders) {
            _wrapper.find('.filters_data span').each(function(){
                self.addFolder($(this).data());
            });
        } else {
            self.addFolder(_wrapper.find('.filters_data span[data-name="_default"]').data());
        }
        _wrapper.find('.filters_data').remove();
        _wrapper.find('.filter_selector').addClass('folders');
        _folders_wrapper = _wrapper.find('.folders');
        self.folderNotify();
    }
    
        $('body').on('click','.secondsetup', function(){
        $('body').find('.setup').click();
    });
    
    _table.dataTable({
        sDom: "Rrt<'clearfix'><<'pull-left'i><'pull-right'"+(self.isPopup()?"<'secondsetup btn btn-success btn-mini'<'icon icon-small icon-ok icon-white'>>":'')+"p>>",
        sPaginationType: 'bootstrap',
        bServerSide: true,
        // @TODO Переделать этот ужас
        iDisplayLength: $('#filterSelector').tmpl({canSave: false, options: options}).find('.length_select').val(),
        sAjaxSource: _ep.getUrl('ajaxfilters'),
        bStateSave: false,
        bProcessing: true,
        aoColumns: _aoColDef,
        fnServerParams: _tableFn.fnServerParams,
        fnServerData: function(sSource, aoData, fnCallback, oSettings) {
            // Стандартное поведение dataTables
            var ready = true;
            ready &= (_folders.length > 0);
            try {
                self.getActiveFolderName();
            } catch(e) {
                ready = false;
            }
            if(ready) {
                oSettings.jqXHR = $.ajax({
                    dataType: 'json',
                    type: "POST",
                    url: sSource,
                    data: aoData,
                    success: fnCallback
                });
            } else {
                fnCallback(_.extend({aaData: []}, aoData));
            }
        },
        fnDrawCallback: _tableFn.fnDrawCallback,
        aaSorting: _aaSorting,
        oColReorder: {
            iFixedColumns: _wrapper.find('.fixed_columns').val()
        },
        fnRowCallback: _tableFn.fnRowCallback,
        oLanguage: {
            sInfo: "_START_ - _END_; Всего: _TOTAL_",
            sInfoFiltered: ''
        },
        fnInfoCallback: function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
            if(_.isNaN(iStart) || _.isNaN(iEnd) || _.isNaN(iTotal)) {
                return '';
            }
            return (iStart+'-'+iEnd+' из '+iTotal);
        }
    });
    $(_table).data('controller', self);
    $(_table).trigger('after_init.sl');
    
    this.getWrapper = function() {
        return _wrapper;
    };
    
    this.setActiveFolder = function(name, notify) {
        _.each(_folders, function(f){
            f.active = (f.name == name);
        });
        if(notify !== false) {
            self.folderNotify();
        }
    };
    
    this.removeFolder = function(name, notify){
        _folders = _.filter(self.getFolders(), function(f){
            return (f.name != name);
        });
        if(notify !== false) {
            self.folderNotify();
        }
    };
    
    this.getTable = function() {
        return _table;
    };
    
    this.getEp = function() {
        return _ep;
    };
    
    this.getSelectedWrapper = function() {
        return _selected;
    };
    
    this.getSelectedDiv = function() {
        return self.getSelectedWrapper().find('div.selected_models:first');
    };
    
    var _last_selected_count = 0;
    this.notifySelected = function() {
        var _size = _.size(self.getSelected());
        if(_last_selected_count !== _size) {
            _last_selected_count = _size;
            self.getSelectedWrapper().trigger('selected-changed.sl');
        }
    };
    
    this.getSelected = function(){
        return _.uniq(_selected_data);
    };
    
    this.setSelected = function(sel) {
        _selected_data = sel;
        self.notifySelected();
    };
    
    this.pushSelected = function(id){
        _selected_data.push(id);
        _selected_data = _.uniq(_.compact(_selected_data), true);
        self.notifySelected();
    };
    
    this.deleteSelected = function(id) {
        _selected_data = _.reject(_selected_data, function(o){ return o === id; });
        self.notifySelected();
    };
    
    this.addSelectedModel = function(id, string, extra_options) {
        if(_.contains(self.getSelected(), id)) {
            $('[data-id="'+id+'"]', self.getSelectedWrapper()).remove();
        }
        self.getSelectedDiv().append($('#listviewSelected').tmpl({
            id: id,
            attrs: extra_options || {},
            content: string
        }));
        var $tr = self.getTable().find('tr[data-real-id="'+id+'"]');
        if($tr && $tr.length) {
            var $input = $tr.find('input[type="checkbox"], input[type="radio"]');
            if($input && $input.length && !$input.prop('checked')) {
                $input.attr('checked', 'checked');
            }
        }
        self.pushSelected(id);
        
        if(self.getSelected().length > 0) {
            self.getSelectedWrapper().addClass('had-selected');
            
        }
    };
    
    this.removeAllSelectedModel = function() {
        self.removeSelectedModel(self.getSelected());
    };
    
    this.removeSelectedModel = function(id){
        if(_.isArray(id)) {
            _.each(id, function(id){
                self.removeSelectedModel(id);
            });
        } else {
            $('[data-id="'+id+'"]', self.getSelectedWrapper()).remove();
            $('tr[data-real-id="'+id+'"]', self.getTable())
                    .find('input[type="checkbox"], input[type="radio"]')
                    .removeAttr('checked');
            self.deleteSelected(id);
        }
        
        if(self.getSelected().length === 0) {
            self.getSelectedWrapper().removeClass('had-selected');
        }
    };
    
    this.getSelectedStrings = function(ids, close_popup) {
        if(!_.isArray(ids)) {
            // Нужно передавать нормальные данные
            throw "Wrong ids param given";
        }
        ids = _.compact(ids); // Дабы исключить nullable-значения
        if(_.size(ids) === 0) {
            // Нечего делать
            return;
        };
        _.each(ids, function(id){
            self.addSelectedModel(id, '<i class="icon icon-loading"></i>');
        });
        var extensions = ['url;alias'];
        if(self.getReturnFields() && self.getReturnFields().length > 0) {
            extensions[extensions.length] = 'fields:'+self.getReturnFields(true);
        }
        
        var data_ids=[];
        $.each(ids, function( index, val_id ) {           
            var url_ajaxdetailed =  $('tr[data-real-id="'+val_id+'"]').attr('data-alias') ;            
            data_ids[data_ids.length] = [url_ajaxdetailed, val_id];
        });
        $.post(self.getEp().getUrl('ajaxdetailed'), {ids: ids, extended: extensions.join(';'), data_ids:data_ids}, function(data) {
            if(data.result) {
                $.each(data.objects, function(id, string) {
                    self.addSelectedModel(id, string, data.extra[id]);
                });
                // TODO Переделать
                if(close_popup) {
                    $('#popup_div .setup').click();
                }
            } else {
                $.alert(data.description);
            }
        });
        
    };
    
    this.redrawTable = _.throttle(function() {
        self.getTable().dataTable().fnDraw(true);
    }, 500, {
        leading: false
    });
    
    this.getAlias = function() {
        return self.getEp().getAlias();
    };
    this.getGroupActions = function(){
    	var actions = [];
    	self.getSelectedWrapper().find('.groupbtn.input-append > *').each(function(i, obj){
    		actions[i]={html:$(obj)[0].outerHTML}
    	});
    	return actions;	
    };
    this.redrawFolders = function() {
        // Чистим то, что было
        self.getFoldersWrapper().html('');
        // Строим из шаблона
        $('#filterSelector').tmpl({
            filters: self.getFolders(),
            groupactions: self.getGroupActions(),
            canSave: _.size(self.getFilters().getFilters()) > 0,
            options: options
        }).appendTo(self.getFoldersWrapper());
        
        if(options.configureState) {
            self.getFoldersWrapper().find('.configure').trigger('click');
        }
        
        $('.item', self.getFoldersWrapper()).each(function(){
            var $this = $(this);
            $this.droppable({
                accept: function(el){
                    if($this.is('.active')) {
                        return false;
                    }
                    return el.is('[data-id]');
                },
                hoverClass: 'btn-warning',
                drop: function(e, ui) {
                    // Добавляем в папку конкретный id
                    setTimeout(function(){
                        $.ajax({
                            type: 'POST',
                            cache: false,
                            url: '/auth/setting/ajaxaddtofolder',
                            data: {
                                alias: self.getAlias(),
                                folder: $this.attr('data-name'),
                                id: ui.helper.data('id')
                            },
                            success: function(data) {
                                if(data.result) {
                                    if($this.is('.active')) {
                                        self.redrawTable();
                                    }
                                } else {
                                    $.alert(data.description);
                                }
                            }
                        });
                        $this.blink({
                            items: {
                                opacity: '0.3'
                            }
                        });
                    }, 0);
                }
            });
        });
    };

    this.getActiveFolderName = function() {
        var f = _.find(_folders, function(f){
            return f.active;
        });
        return f?f.name:'';
    };

    this.removeActiveFolder = function(){
        self.removeFolder(self.getActiveFolderName());
    };

    this.disableFolderSave = function(){
        self.getFoldersWrapper().find('.save').addClass('disabled');
    };

    this.enableFolderSave = function(){
        self.getFoldersWrapper().find('.save').removeClass('disabled');
    };

    this.saveFolder = function(data, okCb, errorCb) {
        var eCb = errorCb || function(){};
        var cb = okCb || function(){};
        var exists = (undefined !== _.find(self.getFolders(), function(el){
            return el.name === data.name;
        }));
        var simple = !data.filter || typeof data.filter !== 'object';
        $.ajax({
            type: 'POST',
            cache: false,
            url: '/auth/setting/ajaxsavefolders',
            data: {
                alias: self.getAlias(),
                parent: 'filters/'+self.getActiveFolderName(),
                path: 'filters/'+(simple?data._name:data.name),
                data: data,
                simple: simple
            },
            success: function(d) {
                if(d.result) {
                    if(!simple) {
                        if(!exists) {
                            self.addFolder({
                                name: d.data.name,
                                label: d.data.name,
                                title: d.data.description,
                                active: false
                            });
                        }
                        self.setActiveFolder(d.data.name, false);
                        self.getFilters().cleanFilters();
                    }
                    cb(d);
                } else {
                    eCb();
                }
            },
            error: eCb
        });
    };
        
    this.saveState = function(cb, eCb) {
        var callback = cb || function() {};
        var eCallback = eCb || function() {};
        $.ajax({
            type: 'POST',
            cache: false,
            url: '/auth/setting/ajaxpushstate',
            data: {
                alias: self.getAlias(),
                cols: _.map(self.getTable().dataTable().fnSettings().aoColumns, function(col){
                    return {
                        name: col.sName,
                        label: col.sLabel,
                        width: col.sWidth,
                        roles: col.aRoles
                    };
                })
            },
            success: function(data) {
                if(data.result) {
                    callback(data);
                } else {
                    eCallback(data);
                }
            },
            error: eCb
        });
    };
    
    this.redrawConfigure = function() {
        self.getFoldersWrapper().find('.configure-div').empty();
        if(options.configureState) {
            var cur = _.find(self.getFolders(), function(f){
                return f.active;
            });
            var data = {
                folder: cur,
                filters: _.map(self.getFilters().getFilters(), function(filter) {
                    return _.extend(filter._field || {}, filter, {
                        _described: self.getFilters().describeFilter(filter)
                    });
                }),
                options: options,
                canSave: _.size(self.getFilters().getFilters()) > 0,
                isSystem: cur?cur.system:false
            };
            $('#foldersConfigure').tmpl(data).appendTo(self.getFoldersWrapper().find('.configure-div'));
        }
    };
    
    self.getFoldersWrapper().on('click', selected_els_count_btn_selector+':not(.disabled)', function(){

            self.getSelectedWrapper().toggleClass('hidden');
    });

    self.getFoldersWrapper().on('folders_changed.sl', function() {
        self.recalcFolderDescriptions();
    });
    
    // Временно, пока есть конфликты со старым listview
    $('body').off('change', '.datatable tbody input[type="checkbox"]:not(.select_all), .datatable tbody input[type="radio"]:not(.select_all)')
    // Выбор чего-то
    $(_table).on('change', '.toggle_selection', function(e) {
        var id = $(this).parents('tr:first').attr('data-real-id');
        var close_popup = false;
        if (id > 0) {
            if($(this).is(':checked')) {
                if($(this).is('input[type="radio"]')) {
                    self.removeAllSelectedModel();
                    close_popup = true;
                }
                self.getSelectedStrings([id], close_popup);
            } else {
                self.removeSelectedModel(id);
            }
        }
    });
    // Слушаем изменения и перерисовываем табличку
    self.getTable().on('filter_changed.sl', function(){
        self.redrawTable();
    });
	// Стандартный поиск
    $('thead', self.getTable()).on('keyup', 'input[data-role="simple"]', function() {
        var $this = $(this);
        var field = self.findField($this.parents('th:first').attr('data-name'));
        if($this.val().length > 0) {
            self.getFilters().addFilter(field.sName, $this.val(), null, field);
        } else {
            self.getFilters().removeFilter(field.sName);
        }
    });
    $('thead input[data-role="simple"]', self.getTable()).each(function() {
        if($(this).val()) {
            $(this).keyup();
        }
    });
    // Поиск по дате
    $('thead .wrapper.date', self.getTable()).each(function(){
        var $wrapper = $(this);
        var field = self.findField($wrapper.parents('th:first').attr('data-name'));
        var fieldname = field.sName;
        var $input = $wrapper.find('input[data-role="real"]');
        $wrapper.on('click', 'i', function(){
            $(this).find('input:first').focus();
        });

        $wrapper.on('change', 'input[data-role="fake"]', function(){
            if($(this).val().length) {
                $(this).parents('i:first').addClass('date_selected');
                $(this).parents('i:first').attr('title', $(this).val());
            } else {
                $(this).parents('i:first').removeClass('date_selected');
                $(this).parents('i:first').attr('title', null);
            }
            var new_val = _.map($wrapper.find('input[data-role="fake"]'), function(o){
                return $(o).val() || '';
            });
            $input.val(new_val.join('::'));
            if(new_val[0].length && new_val[1].length) {
                self.getFilters().addFilter(fieldname, new_val.join('::'), 'between', field);
            } else if(new_val[0].length) {
                self.getFilters().addFilter(fieldname, new_val[0], 'gt', field);
            } else if(new_val[1].length) {
                self.getFilters().addFilter(fieldname, new_val[1], 'lt', field);
            } else {
                self.getFilters().removeFilter(fieldname);
            }
        });
        if($('input[data-role="fake"]', $wrapper).val().length) {
            $('input[data-role="fake"]', $wrapper).change();
        }
    });
    // Select-ы
    $('thead', self.getTable()).on('change', 'select[data-role="simple"]', function() {
        var $this = $(this);
        var field = self.findField($this.parents('th:first').attr('data-name'));
        if($this.val() !== '-1') {
            self.getFilters().addFilter(field.sName, $this.val(), 'eq', field);
        } else {
            self.getFilters().removeFilter(field.sName);
        }
    });
    $('thead select[data-role="simple"]', self.getTable()).each(function(){
        $(this).change();
    });
    // Events END
    
    // Синхронизация таблицы со списком выбора
    self.getTable().on('filter_changed.sl', function(){
        _.each(self.getSelected(), function(id){
            var $tr = self.getTable().find('tr[data-real-id="'+id+'"]');
            if($tr && $tr.length) {
                var $input = $tr.find('input[type="checkbox"], input[type="radio"]');
                if($input && $input.length && !$input.prop('checked')) {
                    $input.attr('checked', 'checked');
                }
            }
        });
    });
    /*
    // Синхронизация списка выбранного с таблицей
    self.getSelectedWrapper().on('selectedchange.sl', function(){
        _.each(self.getSelected(), function(id){
            var $tr = self.getTable().find('tr[data-real-id="'+id+'"]');
            if($tr && $tr.length) {
                var $input = $tr.find('input[type="checkbox"], input[type="radio"]');
                if($input && $input.length && !$input.prop('checked')) {
                    $input.attr('checked', 'checked');
                }
            }
        });
    });
    */
    // Синхронизация списка выбранного с таблицей
    self.getTable().on('tabledraw.sl', function(){
        _.each(self.getSelected(), function(id){
            var $tr = self.getTable().find('tr[data-real-id="'+id+'"]');
            if($tr && $tr.length) {
                var $input = $tr.find('input[type="checkbox"], input[type="radio"]');
                if($input && $input.length && !$input.prop('checked')) {
                    $input.attr('checked', 'checked');
                }
            }
        });
    });
    
    // Load initial data
    if(self.getWrapper().find('.selectedinfo span').length) {
        self.getTable().one('tabledraw.sl', function(){
            self.getSelectedStrings(_.map(self.getWrapper().find('.selectedinfo span'), function(o){
                return $(o).attr('data-id');
            }));
            self.getWrapper().find('.selectedinfo').remove();
        });
    }
    
    self.getTable().on('filter_changed.sl', function(){
        self.redrawFolders();
    });
    
    self.getFoldersWrapper().on('folders_changed.sl', function(){
        self.redrawFolders();
    });

    self.getFoldersWrapper().on('folders_changed.sl', function(){
        self.redrawTable();
    });
    // Сброс фильтров
    self.getWrapper().on('click', '.clean_filters:not(.disabled)', function(){
        self.getFilters().cleanFilters();
    });
    self.getTable().on('filter_changed.sl', function(){
        if(_.size(self.getFilters().getFilters()) > 0) {
            self.getFoldersWrapper().find('.clean_filters').removeClass('disabled');
        } else {
            self.getFoldersWrapper().find('.clean_filters').addClass('disabled');
        }
    });
    
    self.getTable().one('tabledraw.sl', function() {
        if(self.getActiveFolderName().toString().split('')[0] === '_') {
            self.getFoldersWrapper().find('.delete').addClass('disabled');
        } else {
            self.getFoldersWrapper().find('.delete').removeClass('disabled');
        }
    });
    
    self.getFoldersWrapper().on('folders_changed.sl', function(){
        if(self.getActiveFolderName().toString().split('')[0] === '_') {
            self.getFoldersWrapper().find('.delete').addClass('disabled');
        } else {
            self.getFoldersWrapper().find('.delete').removeClass('disabled');
        }
    });
    
    self.getFoldersWrapper().on('click', '.item:not(.active)', function(){
        self.setActiveFolder($(this).attr('data-name'));
    });
    
    self.getFoldersWrapper().on('click', '.item.active', function(){
        self.setActiveFolder('_default');
    });
    
    self.getFoldersWrapper().on('click', '.update:not(.disabled)', function(){
        var folder = _.find(self.getFolders(), function(el){
            return el.name === self.getActiveFolderName();
        });
        if(!folder) {
            throw 'No active folder - can\'t save';
        }
        var data = {
            name: folder.name,
            description: folder.title,
            filter: {
                type: 'multi',
                comps: _.map(self.getFilters().getFilters(), function(f, name){
                    return {
                        field: name,
                        type: f.type,
                        value: f.value
                    };
                })
            }
        };
        self.saveFolder(data, function(d){
            var cur = _.find(self.getFolders(), function(el){
                return el.name === self.getActiveFolderName();
            });
            if(cur) {
                cur.filters = d.data.filter.comps._user.comps._custom.comps;
            }
            self.folderNotify();
        });
    });
    
    self.getFoldersWrapper().on('click', '.save:not(.disabled)', function(){
        $.dialog({
            title: 'Сохранение фильтра',
            body: {
                template: '#folderSave',
                data: {
                    filters: _.map(self.getFilters().getFilters(), function(filter) {
                        return _.extend(filter._field || {}, filter, {
                            _described: self.getFilters().describeFilter(filter)
                        });
                    })
                }
            },
            buttons: {
                ok: {
                    name: 'Ok',
                    click: function($div, opts){
                        // Отправляем на сохранение и добавляем кнопку.
                        var name = $div.find('[name="name"]').val();
                        var desc = $div.find('[name="desc"]').val();
                        var errors = [];
                        
                        if(!name || !name.length) {
                            errors.push("Поле 'Название' обязательно для заполнения");
                        }
                        if(errors.length > 0) {
                            _.each(errors, function(message){
                                opts.fn.error(message);
                            });
                            return false;
                        }
                        var data = {
                            name: name,
                            description: desc,
                            filter: {
                                type: 'multi',
                                comps: _.map(self.getFilters().getFilters(), function(f, name){
                                    return {
                                        field: name,
                                        type: f.type,
                                        value: f.value
                                    };
                                })
                            }
                        };
                        self.saveFolder(data, function(d){
                            var cur = _.find(self.getFolders(), function(el){
                                return el.name === self.getActiveFolderName();
                            });
                            if(cur) {
                                cur.filters = d.data.filter.comps._user.comps._custom.comps;
                            }
                            self.folderNotify();
                        });
                    }
                }
            },
            after: function($div, opts) {
                $div.on('change', '[name="name"]', function(){
                    var val = $(this).val();
                    var new_val = val.replace(/([^-_a-zA-Z0-9\.:])/g, '');
                    if(new_val.length > 5) {
                        new_val = new_val.substring(0, 5);
                    }
                    if(val.length != new_val.length) {
                        opts.fn.error('Только латиница или символы ":", ".", "_", "-".'+"\r\n"+'Также название не должно превышать 5 символов.', {
                            type: 'info'
                        });
                    } else {
                        $div.find('.alert.alert-info').remove();
                    }
                    $(this).val(new_val);
                });
            }
        });
    });
    
    self.getFoldersWrapper().on('click', '.rename', function(){
        var cur = _.find(self.getFolders(), function(f){
            return f.active;
        });
        $.dialog({
            title: 'Редактирование названия/описания фильтра',
            body: {
                template: '#folderSave',
                data: {
                    filters: _.map(self.getFilters().getFilters(), function(filter) {
                        return _.extend(filter._field || {}, filter, {
                            _described: self.getFilters().describeFilter(filter)
                        });
                    }),
                    name: cur.label,
                    description: cur.title
                }
            },
            buttons: {
                ok: {
                    name: 'Ok',
                    click: function($div, opts){
                        // Отправляем на сохранение и добавляем кнопку.
                        var name = $div.find('[name="name"]').val();
                        var desc = $div.find('[name="desc"]').val();
                        var errors = [];
                        
                        if(!name || !name.length) {
                            errors.push("Поле 'Название' обязательно для заполнения");
                        }
                        if(errors.length > 0) {
                            _.each(errors, function(message){
                                opts.fn.error(message);
                            });
                            return false;
                        }
                        var data = {
                            name: name,
                            description: desc,
                            _name: cur.name
                        };
                        self.saveFolder(data, function(){
                            self.updateFolder(cur.name, {
                                name: name,
                                label: name.toUpperCase(),
                                title: desc
                            }, true);
                        });
                    }
                }
            },
            after: function($div, opts) {
                $div.on('change', '[name="name"]', function(){
                    var val = $(this).val();
                    var new_val = val.replace(/([^-_a-zA-Z0-9\.:])/g, '');
                    if(new_val.length > 5) {
                        new_val = new_val.substring(0, 5);
                    }
                    if(val.length != new_val.length) {
                        opts.fn.error('Только латиница или символы ":", ".", "_", "-".'+"\r\n"+'Также название не должно превышать 5 символов.', {
                            type: 'info'
                        });
                    } else {
                        $div.find('.alert.alert-info').remove();
                    }
                    $(this).val(new_val);
                });
            }
        });
    });
    
    // Удаление папки
    self.getFoldersWrapper().on('click', '.delete:not(.disabled)', function(){
        $.confirm({
            title: 'Удаление папки',
            message: 'Вы уверены, что хотите удалить папку?',
            yesCallback: function(){
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: '/auth/setting/ajaxclean',
                    data: {
                        alias: self.getAlias(),
                        path: 'filters/'+self.getActiveFolderName()
                    },
                    success: function(data) {
                        if(data.result) {
                            self.removeActiveFolder();
                        } else {
                            $.alert(data.description);
                        }
                    }
                });
            }
        });
    });
    
    self.getFoldersWrapper().on('click', '.setup', options.fnSetupCallback);
    
    self.getFoldersWrapper().on('click', '.configure', function(){
        var $this = $(this);
        options.configureState = !$this.is('.active');
        if($this.is('.active')) {
            self.getFoldersWrapper().find('.configure-div').empty();
        } else {
            self.redrawConfigure();
        }
    });
    
    self.getTable().on('filter_changed.sl', function(){
        self.redrawConfigure();
    });
    
    self.getTable().on('filter_changed.sl', function(){
        if(_.size(self.getFilters().getFilters()) > 0) {
            self.enableFolderSave();
        } else {
            self.disableFolderSave();
        }
    });
    
    self.getTable().on('column-reorder resized.sl', function(){
        self.saveState();
    });
    
    self.getWrapper().on('change', '.length_select', function() {
        self.getTable().dataTable().fnSettings()._iDisplayLength = $(this).val();
        self.redrawTable();
    });
    
    // Архивные
    self.getFilters().addFilter('archived', '0', 'eq');
    $('.archived_switcher[data-alias="'+self.getAlias()+'"]').each(function(){
        $(this).click(function(){
            switch($(this).attr('data-value')) {
                case '-1':
                    self.getFilters().addFilter('archived', '0', 'eq');
                    break;
                case '1':
                    self.getFilters().addFilter('archived', '1', 'eq');
                    break;
                case '0':
                    self.getFilters().removeFilter('archived');
                    break;
            }
        });
    });
    
    $('.export[data-alias="'+self.getAlias()+'"]').each(function(){
        $(this).click(function(){
            var limit = parseInt($('[data-role="export_confirm_limit"]').val());
            var need_confirm = false;
            if (limit && limit > 0) {
                if (self.getTable().dataTable().fnSettings()._iRecordsDisplay != undefined) {
                    if (self.getTable().dataTable().fnSettings()._iRecordsDisplay > limit) {
                        need_confirm = true;
                    }
                }
            }

            var export_function = function() {
                var params = self.getTable().oApi._fnAjaxParameters(self.getTable().dataTable().fnSettings());
                                        params = self.tableFn('fnServerParams')(params, true);

                var $form = $('<form action="'+self.getEp().getUrl('export')+'" method="POST" />');
                for (var i in params) {
                    var $input = $('<input type="hidden" />').attr(params[i]).appendTo($form);
                }
                $form.appendTo($('body'));
                $form.submit();
            };

            if (need_confirm) {
                $.confirm('Подтвердите, пожалуйста ...', 'Запрос содержит более '+limit+' записей и может долго обрабатываться.', null, function(){
                    export_function();
                });
            } else {
                export_function();
            }
        });
    });
    $('.export_page[data-alias="'+self.getAlias()+'"]').each(function(){
        $(this).click(function(){
            var params = self.getTable().oApi._fnAjaxParameters(self.getTable().dataTable().fnSettings());
            params = self.tableFn('fnServerParams')(params, true);

            var $form = $('<form action="'+self.getEp().getUrl('export')+'" method="POST" />');
            for (var i in params) {
                $('<input type="hidden" />').attr(params[i]).appendTo($form);
            }
            $('<input type="hidden" name="page_only" value="1" />').appendTo($form);

            _.each($('tbody tr[data-real-id]', self.getTable()), function(el){
                $('<input type="hidden" name="export_ids[]" value="'+$(el).attr('data-real-id')+'" />').appendTo($form);
            });
            $form.appendTo($('body'));
            $form.submit();
        });
    });
    
    $(self.getTable()).on('click', '.select_all',function(){
        self.getSelectedStrings(_.map(self.getTable().find('tbody tr[data-real-id]'), function(tr){
            return $(tr).attr('data-real-id');
        }));
    });
    
    $(self.getWrapper()).on('click','.deselect_all', function(){
        self.removeAllSelectedModel();
    });
    
    $(self.getWrapper()).on('click','.hide_selected', function(){
        _.each(self.getSelected(), function(id){
            self.removeSelectedModel(id);
            self.getTable().find('tr[data-real-id="'+id+'"]').remove();
        });
    });
    
    self.getWrapper().on('click', '.hide_columns', function(){
        $.ajax({
            type: 'POST',
            cache: false,
            url: '/auth/setting/ajaxgetcolumns',
            data: {
                alias: self.getAlias(),
                popup: self.isPopup(true)
            },
            success: function(data) {
                var fields = {};
                _.each(data.fields, function(field, name){
                    fields[name] = {
                        sName: name,
                        sLabel: field.label || ''
                    };
                });
                var fieldsets = {};
                _.each(data.fieldsets, function(fs, name){
                    if(name.split('')[0] !== '_') {
                        fieldsets[name] = fs;
                    }
                });
                
                function _save($div, data, cb, errorCb) {
                    $('.loading', $div).removeClass('hidden');
                    var _cb = cb || function(){ };
                    var _eCb = errorCb || function() { };
                    $.ajax({
                        type: 'POST',
                        cache: false,
                        url: '/auth/setting/ajaxsavefieldset',
                        data: {
                            alias: self.getAlias(),
                            path: 'fieldsets/'+data.name,
                            popup: self.isPopup(true),
                            data: data
                        },
                        success: function(d) {
                            $('.loading', $div).addClass('hidden');
                            if(d.result) {
                                _cb(d);
                            } else {
                                _eCb(d);
                            }
                        },
                        error: function() {
                            $('.loading', $div).addClass('hidden');
                            _eCb();
                        }
                    });
                }
                
                $.dialog({
                    title: 'Выбор колонок',
                    canClose: false,
                    body: {
                        template: '#columnsSelector',
                        data: {
                            available: fields,
                            fieldsets: fieldsets,
                            current: data.state,
                            fields: fields
                        }
                    },
                    headControls: {
                        template: '#columnsSelectorControls',
                        data: {
                            current: data.state
                        }
                    },
                    buttons: {
                        ok: {
                            name: 'Ok',
                            click: function($div) {
                                if($('.save:not(.disabled)', $div).length) {
                                    var $cur = $div.find('.tab-panel.active');
                                    _save($div, {
                                        name: $cur.attr('id'),
                                        label: $cur.data('label'),
                                        fields: _.map($cur.find('li'), function(el) { return $(el).data('name'); })
                                    }, function(d) {
                                        self.redrawPage();
                                    });
                                } else {
                                    self.redrawPage();
                                }
                            }
                        },
                        cancel: {
                            name: 'Cancel',
                            click: 'hide'
                        }
                    },
                    after: function($div) {
                        $('.add', $div).click(function(){
                            var num = 'new'+$('.nav a[href^="#new"]', $div).length;
                            var $li = $('#columnsSelectorTabNavItem').tmpl({
                                current: $('.default', $div).attr('data-current'),
                                name: num,
                                label: 'New'
                            }).appendTo($('.nav', $div));
                            initEditable($li.find('a'));

                            var $tab = $('#columnsSelectorTabListItem').tmpl({
                                current: $('.default', $div).attr('data-current'),
                                name: num,
                                label: 'New',
                                fields: []
                            }).appendTo($('.tab-content', $div));

                            initSortable($tab.find('ul:first'));
                        });

                        var pos;

                        $('.available', $div).sortable({
                            revert: true,
                            cursor: 'move',
                            connectWith: '.fieldset_fields',
                            dropOnEmpty: true,
                            remove: function(e, ui) {
                                $('.available li:nth-child('+pos+')').after(ui.item.clone().hide());
                            },
                            start: function(e, ui){
                                pos = $(ui.item).prevAll('li').length;
                            },
                            receive: function(e, ui) {
                                if($('.available [data-name="'+ui.item.data('name')+'"]', $div).length > 1) {
                                    $('.available [data-name="'+ui.item.data('name')+'"]:first', $div).remove();
                                }
                            }
                        });

                        var getHash = function($this) {
                            var d = _.map($('li', $this), function(el){
                                return $(el).data('name');
                            });
                            d.push($this.parents('.tab-panel:first').data('label'));
                            return d.join(':');
                        };

                        var initEditable = function(element) {
                            $(element).dblclick(function(){
                                var v = $(this).val() || $(this).text() || $(this).html();
                                $(this).html(v);
                            }).editable({
                                touch: false, // Whether or not to support touch (default true)
                                toggleFontSize: false, // Whether or not it should be possible to change font size (default true),
                                closeOnEnter: true, // Whether or not pressing the enter key should close the editor (default false)
                                event: 'dblclick', // The event that triggers the editor (default dblclick)
                                callback: function(data) {
                                    // Callback that will be called once the editor is blurred
                                    if (data.content) {
                                        var id = data.$el.attr('href');
                                        $(id).data('label', data.content);
                                        $(data.$el).trigger('labelchanged.sl');
                                    }
                                }
                            });
                        };

                        var initSortable = function(element) {
                            var $this = $(element);
                            var hash = getHash($this);
                            $this.data('hash', hash);
                            $this.sortable({
                                revert: true,
                                connectWith: '.available',
                                update: function(e, ui) {
                                    var id = $this.parents('.tab-panel:first').attr('id');
                                    if($this.data('hash') !== getHash($this)) {
                                        $('.save', $div).removeClass('disabled');
                                    } else {
                                        $('.save', $div).addClass('disabled');
                                    }
                                }
                            });
                        };

                        $('.fieldset_fields', $div).each(function(){
                            initSortable($(this));
                        });

                        $div.on('click labelchanged.sl', '.nav a', function(e){
                            e.preventDefault();
                            var selector = $(this).attr('href');
                            $('.tab-panel', $div).hide();
                            $(selector, $div).show();
                            if(selector.split('').slice(1).join('') === $('.default', $div).attr('data-current')) {
                                $('.default', $div)
                                        .addClass('disabled')
                                        .find('.icon')
                                            .attr('title', 'Используется по-умолчанию')
                                            .addClass('icon-star')
                                            .removeClass('icon-star-empty');
                            } else {
                                $('.default', $div)
                                        .removeClass('disabled')
                                        .find('.icon')
                                            .attr('title', 'Установить по-умолчанию')
                                            .addClass('icon-star-empty')
                                            .removeClass('icon-star');
                            }
                            $('i.is_default', $div).remove();
                            $('.nav a', $div).each(function() {
                                if($(this).attr('href').split('').slice(1).join('') === $('.default', $div).attr('data-current')) {
                                    $(this).before('<i class="is_default icon icon-small icon-star pull-right"></i>');
                                }
                            });
                            $('.available li', $div).each(function(){
                                if($(selector+' [data-name="'+$(this).data('name')+'"]', $div).length > 0) {
                                    $(this).hide();
                                } else {
                                    $(this).show();
                                }
                            });
                            var current_tab = $('.tab-panel.active ul:first', $div);
                            var canSave = false;
                            
                            $('.delete', $div).addClass('disabled');

                            if(current_tab && current_tab.length) {
                                var $panel = current_tab.parents('.tab-panel:first');
                                if(current_tab.data('hash') !== getHash(current_tab)) {
                                    canSave = true;
                                }
                                if($panel.attr('id').split('')[0] === '_') {
                                    canSave = false;
                                } else {
                                    $('.delete', $div).removeClass('disabled');
                                }
                            }

                            if(canSave) {
                                $('.save', $div).removeClass('disabled');
                            } else {
                                $('.save', $div).addClass('disabled');
                            }
                        });

                        $('.nav a', $div).each(function(ind, el){
                            initEditable(el);
                            if($(el).parents('li:first').is('.active')) {
                                $(el).trigger('click');
                            }
                        });

                        $div.on('click', '.delete:not(.disabled)', function(){
                            var $cur = $('.tab-panel:visible');

                            var remover = function(id) {
                                $('.nav a[href="#'+id+'"]', $div).parents('li:first').remove();
                                $('#'+id, $div).remove();
                                $('.save, .delete').addClass('disabled');
                            };

                            if($cur) {
                                if($cur.is('[id^="#new"]') && ($cur.find('ul').children().length == 0)) {
                                    remover($cur.attr('id'));
                                } else {
                                    $.confirm('Подтверждение', 'Вы уверены, что хотите удалить текущий набор полей?', null, function(){
                                        _remove($cur.attr('id'), function(){
                                            remover($cur.attr('id'));
                                        });
                                    });
                                }
                            }
                        });

                        function _setDefault(name, cb, errorCb) {
                            $('.loading', $div).removeClass('hidden');
                            var _cb = cb || function(){ };
                            var _eCb = errorCb || function() { };
                            $.ajax({
                                type: 'POST',
                                cache: false,
                                url: '/auth/setting/ajaxsavestate',
                                data: {
                                    alias: self.getAlias(),
                                    path: self.isPopup()?'state/popup_fieldset':'state/fieldset',
                                    data: name
                                },
                                success: function(d) {
                                    $('.loading', $div).addClass('hidden');
                                    if(d.result) {
                                        _cb(d);
                                    } else {
                                        _eCb(d);
                                    }
                                },
                                error: function() {
                                    $('.loading', $div).addClass('hidden');
                                    _eCb();
                                }
                            });
                        }

                        function _remove(name, cb, errorCb) {
                            $('.loading', $div).removeClass('hidden');
                            var _cb = cb || function(){ };
                            var _eCb = errorCb || function() { };
                            $.ajax({
                                type: 'POST',
                                cache: false,
                                url: '/auth/setting/ajaxclean',
                                data: {
                                    alias: self.getAlias(),
                                    path: 'fieldsets/'+name,
                                },
                                success: function(d) {
                                    $('.loading', $div).addClass('hidden');
                                    if(d.result) {
                                        _cb(d);
                                    } else {
                                        _eCb(d);
                                    }
                                },
                                error: function() {
                                    $('.loading', $div).addClass('hidden');
                                    _eCb();
                                }
                            });
                        }

                        $div.on('click', '.save:not(.disabled)', function(){
                            var $cur = $('.tab-panel:visible');
                            if($cur && ($cur.length > 0)) {
                                _save($div, {
                                    name: $cur.attr('id'),
                                    label: $cur.data('label'),
                                    fields: _.map($cur.find('li'), function(el) { return $(el).data('name'); })
                                }, function(d) {
                                    if(d.data.name !== $cur.attr('id')) {
                                        $('.nav a[href="#'+$cur.attr('id')+'"]').attr('href', d.data.name);
                                        $cur.attr('id', d.data.name);
                                    }
                                    $cur.find('ul:first').data('hash', getHash($cur.find('ul:first')));
                                    $('.nav a[href="#'+d.data.name+'"]', $div).trigger('labelchanged.sl');
                                });
                            }
                        });

                        $div.on('click', '.default:not(.disabled)', function(){
                            var $this = $(this);
                            var $cur = $('.tab-panel:visible');
                            if($cur && ($cur.length > 0)) {
                                _setDefault($cur.attr('id'), function(){
                                    $this.attr('data-current', $cur.attr('id'));
                                    $('.nav a[href="#'+$cur.attr('id')+'"]').trigger('labelchanged.sl');
                                });
                            }
                        });
                    }
                });
            }
        });
    });
};
// Калькуляции
$('table.table.datatable').on('tabledraw.sl', function() {
    var table = this;
    var $calculate_fields = $('thead > tr.titles th[data-calc]', table);
    if ($calculate_fields.length) {
        var $tr = $('<tr />').attr('data-type', 'calcrow');
        $('thead > tr.titles > th', table).each(function(index) {
            var fieldName = $(this).attr('data-name');
            var $th = $('<th>').attr('data-name', fieldName);

            if ($(this).data('calc')) {
                var calculate = $(this).data('calc');

                var sum = 0;

                var length = 0;
                var i = index + 1;

                $('td:nth-child(' + i + ')', table).each(function() {
                    if ($(this).text().length) {
                        sum += parseFloat($(this).text());
                        length++;
                    }

                });
                switch (calculate) {
                    case 'sum':
                        $th.html(' &Sigma; ' + Math.round(sum * 100) / 100);
                        break;
                    case 'average' :
                        $th.html('&Xi; ' + (length ? Math.round(sum / length * 100) / 100 : '0'));
                        break;
                }
            }

            $th.appendTo($tr);
        });
        $('tr[data-type="calcrow"]', table).remove();
        $tr.appendTo($(table).find('tbody'));
    }
});
