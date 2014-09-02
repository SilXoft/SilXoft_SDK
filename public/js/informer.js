var w_informer_selector = '#btn_informer_w';
var ctrl_selector = w_informer_selector + '> .i-ctrl';
var informer_selector = '#btn_informer';
var informer_autoload_event = 'informer_autoload';
var informer_request_url = '/main/ajaxinformer';
var informer_autoload_data;
var informer_request_timeout = 30000;
$(function() {
	
	var $i = $(informer_selector);
	if ($i.length && $i.children().length) {
		var $iw = $(w_informer_selector);
		
		$iw.show();
		
		$iw.click(function(e){
			if (e.target == this){
				if (!$i.children('.open').length) $(this).toggleClass('open');
			}
		});
		
		$i.on('click', ' > * > .shortview', function() {
			$(informer_selector + ' > *').not($(this).parent()).removeClass('open');
			var $inf_item = $(this).parent(); 
			
			$inf_item.toggleClass('open').trigger('informer.item.click', this);

		});
		$i.on('click', '.content-wrapper > .close', function() {

			$(this).parents(informer_selector + ' > *').toggleClass('open');
		});
		
		$i.click(function(){
			
			if ($(this).children('.open').length){
				$iw.addClass('open');
			} else {
				$iw.removeClass('open');
			}
		});
		
		
		
	}

	/*
	 $i.mouseenter(function() {
	 if ($(this).is(':not(:has(.open))') && $(this).position().left == -25)
	 $(this).animate({left: 0}, 500)
	 });
	 $i.mouseleave(function() {
	 if ($(this).is(':not(:has(.open))') && $(this).position().left == 0)
	 $(this).animate({left: -25}, 500)
	 })
	 */

});
$(window).load(function() {
	informerUpdater();
});

var informer = {
	blocks : {},

	last_update : 0,
	onProcess : false,

	addInformerAutorequest : function(data) {
		if (!this.dataValid(data))
			return;

		//if (data.hasOwnProperty('period')){
		data['nextRequest'] = 0;
		//}
		this.blocks[data.id] = data;
	},

	update : function() {
		//console.log('Informer update start');
		if (this.onProcess) {
			console.log('The informer on update process. New update rejected;');
			return;
		}
		if (informer_ajax_update == undefined || !informer_ajax_update) {
			console.log('Resource home/main/ajaxinformer is not allowed. Update rejected;');
			return;
		}
		var time = this.now();
		
		var to_update = {};
		for (id in this.blocks) {
			if (this.blocks[id].hasOwnProperty('nextRequest') && this.blocks[id].nextRequest <= time) {
				
				to_update[id] = this.blocks[id].getRequestData;
			}
		}

		if (Object.keys(to_update).length) {
			this.onProcessBegin();
			var _this = this;
			$.post(informer_request_url, {
				informer_request : to_update
			}, function(data) {
				if (data.result) {
					if (data.hasOwnProperty('data')) {

						for (id in data.data) {
							if (to_update[id] != undefined && _this.blocks[id] != undefined) {
								_this.blocks[id].update(data.data[id]);
								if (_this.blocks[id].hasOwnProperty('period')) {
									_this.blocks[id].nextRequest = _this.getNextRequestTime(_this.blocks[id].period);
								}
							}
						}
					}

				} else {
					alert(data.description);
				}
			}).done(function() {
				_this.onProcessEnd();
			})
		}

	},

	dataValid : function(data) {
		var isValid = true;
		if (!data.hasOwnProperty('id')) {
			console.log('Informer data has no id property');
			isValid = false;

		}

		if (this.blocks[data.id] != undefined) {
			console.log('Informer data has not unique id. Please check data!');
			isValid = false;
		}
		if (!data.hasOwnProperty('update')) {
			console.log('Informer data has no update function');
			isValid = false;
		}

		if (!(data.hasOwnProperty('getRequestData'))) {
			console.log('Informer data has no getRequestData property');
			isValid = false;
		}

		if (!isValid)
			console.log(data);
		return isValid;
	},
	now : function() {
		return Math.round(+new Date() / 1000);
	},
	getNextRequestTime : function(period) {
		
		return parseInt(this.now()) + parseInt(period);
	},

	onProcessBegin : function() {
		$(informer_selector).trigger('informer.beforeupdate');
		this.onProcess = true;
	},

	onProcessEnd : function() {
		this.onProcess = false;
		$(informer_selector).trigger('informer.afterupdate');
	},
}

var informerUpdater = function() {
	informer.update();
	setTimeout(informerUpdater, informer_request_timeout);

}
