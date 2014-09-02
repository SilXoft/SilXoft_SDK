var oSlDefaultTreeTheme = {                           
    theme : 'default',
    dots : false,
    icons: true,
    url : '/css/jstree/default/style.css'
};

$(document).ready(function(){
    $(filters_tree_selector).jstree({
        plugins: ["themes", "html_data", "ui", "dnd", "crrm", "types", "contextmenu"],
        contextmenu: {
            items: fnSlFiltersConxextMenu
        },
        crrm: {
            move: {
                always_copy: 'multitree',
                check_move: function(move) {
                    // Нельзя вставлять до и после корня
                    if(move.rt._get_type(move.r) === 'root') {
                        if(-1 !== $.inArray(move.p, ['after', 'before'])) return false;
                    }
                    // Нельзя вставлять связи. Только поля
                    if(move.ot._get_type(move.o) === 'relation') {
                        return false;
                    }
                    // Если есть родитель, то вставлять можно только внутрь
                    if(move.rt._get_parent(move.r)) {
                        if(-1 !== $.inArray(move.p, ['after', 'before'])) return false;
                    }
                    return true;
                }
            }
        },
        types: {
            types: {
                condition: {
                    'icon' : {
						image : '/img/glyphicons-halflings.png',
                        position: '-432px 0'
					}
                },
                term : {
                    'icon' : {
						image : '/img/glyphicons-halflings.png',
                        position: '0 -96px'
					}
                },
                root: {
                    'icon' : {
						image : '/img/glyphicons-halflings.png',
                        position: '-408px -144px'
					}
                }
            }
        },
        core: {
            html_titles: true
        },
        themes: oSlDefaultTreeTheme
    }).bind("move_node.jstree", function (e, data) { // Логика перетаскивания нод
        var move = data.args[0]; // Оъект перемещения
        // Вместо .correct_state
        if($(move.o).find('ul:first').is(':empty')) {
            // Если узел возомнил, что он дерево, обрезаем ему крылья
            $(move.o)   .removeClass('jstree-open')
                        .removeClass('jstree-closed')
                        .addClass('jstree-leaf')
                        .find('ul')
                            .remove();
        }
        // Перетащили из другого дерева - значит это условие
        if(move.oc && (-1 === $.inArray(move.ot._get_type(move.oc), ['root'], ['term'], ['condition']))) {
            $.jstree._reference(move.oc).set_type('condition', move.oc);
        }
        // Внутренние перемещения - ничего не делаем
        if($(move.o).data('noprocess') === true) {
            $(move.o).data('noprocess', null);
            return;
        } else if(!move.cy) {
            return;
        }
        // Диалог добавления условия
        fnSlConditionDialog('add', move, function(){
            // Перенесли на ....
            switch(move.rt._get_type(move.r)) {
                case 'root':
                    // Если это первое поле - все нормально.
                    // Если поля уже есть, нужно переместить
                    if($(move.r).find('ul > li').length > 1) {
                        // Добавляем родительский term
                        move.rt.create(move.r, 'first', {
                            attr: {
                                'data-type': 'and'
                            },
                            data: "И"
                        }, function(o_created){
                            $.jstree._reference(o_created).set_type('term', o_created);
                            // Переносим в него всех соседей
                            $(o_created).siblings().data('noprocess', true);
                            $.jstree._reference(o_created).move_node($(o_created).siblings(), o_created);
                            $(document).trigger('filter_changed.sl');
                        }, true);
                    }
                    break;
                case 'term':
                    // Переместили внутри дерева
                    //$(document).trigger('filter_changed.sl');
                    break;
                case 'condition':
                    // Переместили на такое-же условие - групируем все под один term
                    move.rt.create(move.r, 'after', {
                        attr: {
                            'data-type': 'and'
                        },
                        data: "И"
                    }, function(o_created){
                        $.jstree._reference(o_created).set_type('term', o_created);
                        // Переносим в него то, на что навели
                        move.rt.move_node(move.oc, o_created);
                        move.rt.move_node(move.r, o_created);
                        $(document).trigger('filter_changed.sl');
                    }, true);
                    break;
            }
        }, function(){
            // Отмена - откатываем состояние
            $.jstree.rollback(data.rlbk);
        });
    }).bind("loaded.jstree", function(e, data) { // Создаем root
        data.inst.create(null, -1, {
            attr: {
                id: 'root'
            },
            data: 'Новый фильтр ...'
        }, function(o){
            data.inst.set_type('root', o);
        }, true);
    }).bind("delete_node.jstree", function (e, data) {
        $(document).trigger('filter_changed.sl');
    });
    
    $('#fields-tree').jstree({
        plugins: ["themes", "html_data", "ui", "dnd", "crrm", "types", "contextmenu", "silenca"],
        types: {
            types: {
                field: {
                    'icon' : {
						image : '/img/glyphicons-halflings.png',
                        position: '-239px -95px'
					}
                },
                relation: {
                    'icon' : {
						image : '/img/glyphicons-halflings.png',
                        position: '-263px -22px'
					},
                    drag_start: false
                }
            }
        },
        contextmenu: {
            items: function() {
                return {
                    checktype: {
                        label: 'Тип',
                        action: function(o){
                            alert($.jstree._reference(o)._get_type(o));
                        }
                    },
                    checkloaded: {
                        label: 'Состояние',
                        action: function(o){
                            alert($.jstree._reference(o)._is_loaded(o));
                        }
                    }
                };
            }
        },
        crrm: {
            move: {
                check_move: function(move) {
                    // Пытаемся перемещать связь, а нельзя ...
                    if(move.ot._get_type(move.o) === 'relation') {
                        return false;
                    }
                }
            }
        },
        core: {
            html_titles: true
        },
        themes: oSlDefaultTreeTheme
    }).bind("move_node.jstree", function (e, data) {
        
    }).bind('open_node.jstree', function(e, data){
        var field = data.args[0];
        if($(field).data('loaded') !== true) {
            $.ajax({
                type: 'POST',
                cache: false,
                url: '/home/main/ajaxdescribemodel',
                data: {
                    model: $(field).data('model'),
                    path: $(field).data('name')
                },
                success: function(data) {
                    if(data.result) {
                        $(field).data('loaded', true);
                        for(var i in data.fields) {
                            var is_relation = (data.fields[i].type === 'relation');
                            var attrs = {
                                id: $(field).data('name')+'.'+data.fields[i].name,
                                "data-type": data.fields[i].type,
                                "data-name": $(field).data('name')+'.'+data.fields[i].name
                            };
                            if(is_relation) {
                                for(var j in data.fields[i]) {
                                    if(j === 'name') {
                                        continue;
                                    }
                                    attrs['data-'+j] = data.fields[i][j];
                                }
                            }
                            $('#fields-tree').jstree('create', field, 'last', {
                                attr: attrs,
                                data: data.fields[i].label,
                                state: is_relation?'closed':null
                            }, function(o){
                                $.jstree._reference(o).set_type(is_relation?'relation':'field', o);
                            }, true);
                        }
                    } else {
                        alert(data.description);
                    }
                },
                error: function(a, b) {
                    
                }
            });
        }
    });
    
    $.ajax({
        type: 'POST',
        cache: false,
        url: '/home/main/ajaxdescribemodel',
        data: {
            model: model_alias
        },
        success: function(data) {
            if(data.result) {
                for(var i in data.fields) {
                    var is_relation = (data.fields[i].type === 'relation');
                    var attrs = {
                        id: data.fields[i].name,
                        "data-type": data.fields[i].type,
                        "data-name": data.fields[i].name
                    };
                    if(is_relation) {
                        for(var j in data.fields[i]) {
                            attrs['data-'+j] = data.fields[i][j];
                        }
                    }
                    $('#fields-tree').jstree('create', null, 'last', {
                        attr: attrs,
                        data: data.fields[i].label,
                        state: is_relation?'closed':null
                    }, function(o){
                        $.jstree._reference(o).set_type(is_relation?'relation':'field', o);
                    }, true);
                }
            } else {
                console.log(data.description);
            }
        }
    });
});

function fnSlFiltersConxextMenu(oClicked) {
    var addTerm = function() {
        return {
            addterm: {
                label: 'Добавить',
                action: function() {
                    var $tree = $.jstree._reference(oClicked);
                    $tree.create(oClicked, 'last' , {
                        attr: {
                            'data-type': 'and'
                        },
                        data: "И"
                    }, function(o_created){
                        $.jstree._reference(o_created).set_type('term', o_created);
                    }, true);
                }
            }
        };
    };
    
    var switchItem = function(){
        return {
            toggle: {
                label: 'И/ИЛИ',
                action: function() {
                    var dataType = '';//($(oClicked).attr('data-type') === 'and')?'or':'and';
                    var name = '';
                    switch($(oClicked).attr('data-type')) {
                        case 'or':
                            dataType = 'and';
                            name = 'И';
                            break;
                        case 'and':
                        default:
                            dataType = 'or';
                            name = 'ИЛИ';
                            break;
                    }
                    $.jstree._reference(oClicked).rename_node(oClicked, name);
                    $(oClicked).attr('data-type', dataType);
                }
            }
        };
    };

    var editItem = function() {
        return {
            addterm: {
                label: 'Изменить',
                action: function() {
                    fnSlConditionDialog('edit', oClicked, function(){
                        $.alert('Ok');
                    }, function(){
                        $.alert('Error');
                    });
                }
            }
        };
    };

    var deleteItem = function(){
        return {
            delete: {
                label: 'Удалить',
                action: function() {
                    $.jstree._reference(oClicked).remove(oClicked);
                }
            }
        };
    };

    var renameItem = function() {
        return {
            rename: {
                label: 'Переименовать',
                action: function(){
                    $.jstree._reference(oClicked).rename(oClicked);
                }
            }
        };
    };

    var checkType = function() {
        if(!debug) return {};
        return {
            checktype: {
                label: 'Тип',
                action: function() {
                    alert($.jstree._reference(oClicked)._get_type(oClicked));
                }
            }
        };
    };
    
    switch($.jstree._reference(oClicked)._get_type(oClicked)) {
        case 'root':
            return $.extend({}, renameItem(), checkType());
            break;
        case 'term':
            return $.extend({}, addTerm(), switchItem(), deleteItem(), checkType());
            break;
        case 'condition':
            return $.extend({}, editItem(), deleteItem(), checkType());
            break;
        default:
            return $.extend({}, deleteItem(), checkType());
            break;
    }
}

function fnSlJstreeGetPath(node) {
    var name = [];
    var _func = function(el) {
        return {
            type: $(el).attr('rel'),
            name: $(el).text(),
            id: $(el).attr('id'),
            data: $(el).data(),
            el: $(el).get(0)
        }
    }
    if(!node) return name;
    node = $.jstree._reference(node)._get_node(node);
    $(node).parentsUntil('.jstree', 'li').each(function(){
        name.push(_func(this));
    });
    name.reverse();
    name.push(_func(node));
    return name;
}

function fnSlBuilNameAttr(node, p) {
    var prefix = p || 'filter';
    var path;
    try {
        path = fnSlJstreeGetPath(node);
        var name = '';
        $(path).each(function(ind, el){
            switch(el.type) {
                case 'root':
                    name += prefix;
                    break;
                case 'term':
                    if($(el.el).data('type') === undefined) return null;
                    name += '['+$(el.el).attr('data-type')+']';
                    break;
                default:
                    if(($(el.el).data('type') === undefined) || ($(el.el).data('name') === undefined)) return null;
                    name += '['+$(el.el).data('type')+']['+$(el.el).data('name')+']';
                    break;
            }
        });
        return name;
    } catch(e) {
        alert(e.message);
    }
}

function fnSlBuildData(selector, p) {
    var prefix = p || 'filter';
    var data = {};
    var key;
    var fields_counter = 0;
    $(selector).find('[rel="condition"]').each(function(ind, el){
        key = fnSlBuilNameAttr(el, prefix);
        if(!key) return null;
        var value = $(el).data('value')+'';
        if(value && value.split(';').length > 1) {
            try {
                $(value.split(';')).each(function(ind, element){
                    data[key+'['+ind+']'] = element;
                });
            } catch(e) {
                console.log(e.message);
            }
        } else {
            data[key] = value;
        }
        fields_counter++;
    });
    if(fields_counter > 0) {
        data[prefix+'[name]'] = $('[rel="root"]:first a:first', selector).text();
        return data;
    }
    return null;
}