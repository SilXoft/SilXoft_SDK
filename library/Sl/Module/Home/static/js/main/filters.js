var     debug,                                      // Флаг дебага
        fnSlConditionDialog,                        // Функция управления диалогом добавления сравнения
        filters_tree_selector = '#filters-tree',    // Селектор дерева фильтров
        fnSlBuildData = function(){};               // Построение информации о фильтре
        
       
var mDataRender;
var table_selected_data = {};



$(document).ready(function(){
    // Определение дебага
    debug = ($('#debug').length > 0);
    // Инициализация таблицы и связанных данных
    $('.table-wrapper').each(function(){
        var ctrl = new listviewController($(this));
    });
});

var slEnrtyPoint;
$(document).ready(function(){
    slEnrtyPoint = new SlEntryPoint();
});
