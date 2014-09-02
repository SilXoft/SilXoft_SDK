$(document).ready(function(){
    // Настройки AJAX по-умолчанию
    $.ajaxSetup({
        type: 'POST',
        cache: false,
        url: '/home/admin/ajaxgetoptions',
        error: function(){
            $(document).trigger('error.ajax');
        },
        done: function(){
            $(document).trigger('done.ajax');
        }
    });
    var _mapping = {
        extend:{
            'items': function(items){
                items = ko.observableArray($.map(items, function(o){
                    return ko.viewmodel.fromModel(o, _mapping);
                }));
                //user.isDeleted = ko.observable(false);
            }
        }
    };
    // Узел дерева
    function viewTree(d, p) {
        var data = d || {};
        var parent = p || null;
        
        var _self = this;
        // Кастомизация "unserialize-a"
        var mapping = {
            items: {
                create: function(opts) {
                    return new viewTree(opts.data, opts.parent);
                }
            }
        };
        
        // Родитель
        this.parent = ko.observable(parent);
        // Название
        this.label = ko.observable();
        // Значение
        this.value = ko.observable();
        // Узлы
        this.items = ko.observableArray();
        // Тип узла
        this.type = ko.observable('option');
        // Добавление подчиненного узла
        this.add = function(){
            _self.items.push(new viewTree({
                name: 'Test',
                value: 'Test value',
                type: _self._calcSubType(_self.type())
            }));
        };
        // Оперделение типа подчиненного узла
        this._calcSubType = function(type) {
            switch(type) {
                case 'module':
                case 'model':
                    return 'section';
                case 'section':
                default :
                    return 'option';    
            }
        };
        // "Загруженность" узла
        this.stateLoad = ko.observable(false);
        // Наличие значения
        this.hasValue = ko.computed(function(){
            switch(this.type()) {
                case 'option':
                    // У настроек либо значение либо узлы
                    if(this.stateLoad()) {
                        // Все загружено
                        return !(this.items() && (this.items().length > 0));
                    } else {
                        // Данные еще не загружены
                        // Предполагаем худшее ....
                        return false;
                    }
                    break;
                case 'module':
                case 'model':
                case 'section':
                case 'subsection':
                default:
                    return false;
            }
        }, this);
        // "Открытость" узла
        this.stateOpened = ko.observable();
        // "Закрытость" узла. Вычисляется на основании "открытости"
        this.stateClosed = ko.computed(function(){
            return !this.stateOpened();
        }, this);
        // Флаг обработки данных
        this._stateLoading = ko.observable(0);
        // И его обработка
        this.stateLoading = ko.computed({
            read: function() {
                return this._stateLoading() !== 0;
            },
            write: function(value) {
                if(value) {
                    this._stateLoading(1+this._stateLoading());
                } else {
                    this._stateLoading(this._stateLoading()-1);
                }
            },
            owner: this
        });
        // "Открываем" узел
        this.toggleOpen = function() {
            _self.toggleLoad().stateOpened(_self.stateClosed());
        };
        
        this.toggleLoad = function() {
            if(_self.stateLoad()) return _self;
            _self.stateLoading(true);
            _self.__loadData(_self, {
                done: function(){
                    _self.stateLoad(true);
                    _self.stateLoading(false);
                },
                error: function(message){
                    _self.stateLoad(true);
                    _self.stateLoading(false);
                    console.log(message);
                }
            });
            return _self;
        };
        // Флаг отображения панели управления
        this._show_controls = ko.observable();
        // И его обработка
        this.showControls = ko.computed({
            read: function() {
                return this._show_controls();
            },
            write: function(value) {
                this._show_controls(!this._show_controls());
            },
            owner: this
        });
        // Путь к узлу
        this.path = ko.computed(function(){
            return this.parent()?(this.parent().path()+'|'+this.value()):'';
        }, this);
        // Проверка возможности добавления узла
        this.canAdd = ko.computed(function(){
            switch(this.type()) {
                case 'option':
                    // Только если это уже массив
                    return !this.hasValue();
                    break;
                case 'section':
                case 'subsection':
                    return true;
                case 'module':
                case 'model':
                default :
                    return false;
            }
        }, this);
        // Проверка возможности открытия узла
        this.canOpen = ko.computed(function(){
            switch(this.type()) {
                case 'option':
                    return !this.hasValue();
                case 'module':
                case 'model':
                case 'section':
                case 'subsection':
                default:
                    if(this.stateLoad()) {
                        return (this.items() && (this.items().length > 0));
                    }
                    return true;
            }
        }, this);
        // Класс иконки
        this.iconClass = ko.computed(function(){
            if(this.stateLoading()) return 'icon-loading';
            switch(this.type()) {
                case 'option':
                    return 'icon-tag';
                case 'module':
                    return 'icon-folder-open';
                case 'model':
                    return 'icon-book';
                case 'section':
                case 'subsection':
                    return 'icon-tags';
                default:
                    return this.stateOpened()?'icon-arrow-right':'icon-arrow-down';
            }
        }, this);
        
        this.collapsibleClass = ko.computed(function(){
            return this.canOpen()?'collapsible':'';
        }, this);
        
        this.getClasses = ko.computed(function(){
            return [
                this.iconClass(),
                this.collapsibleClass(),
            ].join(' ');
        }, this);
        // Загрузка данных об узле
        this.__loadData = function(node, o) {
            var options = $.extend({
                done: function() {},
                error: function() {}
            }, o);
            $.ajax({
                data: {
                    label: node.label(),
                    value: node.value(),
                    type: node.type(),
                    ptype: node.parent()?node.parent().type():'',
                    path: node.path()
                },
                success: function(data) {
                    if(data.result) {
                        //ko.mapping.fromJS({items: data.data}, mapping, node);
                        _self.load({items: data.data}, _self, _self);
                        options.done();
                    } else {
                        options.error(data.description);
                    }
                },
                error: function() {
                    options.error();
                }
            });
        };
        this.load = function(data, parent, view) {
            var __self = view || this;
            for(var i in data) {
                if(i == 'items') {
                    __self.items([]);
                    $.map(data[i], function(o){
                        __self.items.push(new viewTree(o, __self));
                    });
                } else if(ko.isWriteableObservable(this[i])) {
                    this[i](data[i]);
                }
            }
        };
        this.load(data, parent);
    }
    
    viewTree.prototype = {
        constructor: viewTree
    };
    
    var view = {
        config: new viewTree({
            name: 'root',
            items: $.map($('#modules_data input'), function(o){
                return {
                    label: $(o).attr('name'),
                    value: $(o).val(),
                    type: 'module'
                };
            })
        })
    };
    
    ko.applyBindings(view);
});