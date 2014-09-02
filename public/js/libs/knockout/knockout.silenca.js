ko.bindingHandlers.fadeDuration = {};
ko.bindingHandlers.fadeEasing = {};
ko.bindingHandlers.fadeVisible = {
    update: function(element, valueAccessor, all) {
        var value = valueAccessor();
        var opts = {
            duration: all.has('fadeDuration')?all.get('fadeDuration'):'200',
            easing: all.has('fadeEasing')?all.get('fadeEasing'):'linear'
        };
        $(element).stop();
        ko.unwrap(value)?$(element).fadeIn(opts):$(element).fadeOut(opts);
    }
};