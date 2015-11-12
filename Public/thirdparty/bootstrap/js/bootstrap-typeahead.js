/* =============================================================
 * bootstrap-typeahead.js v2.3.2
 * http://twbs.github.com/bootstrap/javascript.html#typeahead
 * =============================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function($){

  "use strict"; // jshint ;_;


 /* TYPEAHEAD PUBLIC CLASS DEFINITION
  * ================================= */

  var Typeahead = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.typeahead.defaults, options)
    this.matcher = this.options.matcher || this.matcher
    this.sorter = this.options.sorter || this.sorter
    this.highlighter = this.options.highlighter || this.highlighter
    this.updater = this.options.updater || this.updater
    this.source = this.options.source
    this.$menu = $(this.options.menu)
    this.shown = false
    this.listItemHandler = this.options.listItemHandler
    this.instantTrigger = this.options.instantTrigger
    this.afterSelect = this.options.afterSelect
    this.keyName = this.options.keyName || 'key'
    this.textName = this.options.textName || 'text'
    this.allowNullQuery = this.options.allowNullQuery || false
    this.listen()
  }

  Typeahead.prototype = {

    constructor: Typeahead

  , select: function () {
	  var text = this.$menu.find('.active').attr('data-text');
      this.$element.val(text);
	  
      var val = this.$menu.find('.active').attr('data-value');
      this.$element.prev().val(val);
      
      if(this.afterSelect) this.afterSelect(val);
      this.$element.attr("typeahead", true);
      
      return this.hide();
    }

  , updater: function (item) {
      return item
    }

  , show: function () {
      var pos = $.extend({}, this.$element.position(), {
        height: this.$element[0].offsetHeight
      })

      this.$menu
        .width(this.$element.outerWidth())
        .insertAfter(this.$element)
        .css({
          top: pos.top + pos.height
        , left: pos.left
        })
        .show()

      this.shown = true;
      return this
    }

  , hide: function () {
      this.$menu.hide()
      this.shown = false
      return this
    }

  , lookup: function (event) {
	  if(this.$element.prop("readonly")) return;
	  
      var items
      if(event.type == "paste") {
    	  var clipboardData = event.originalEvent.clipboardData 
    	  || window.clipboardData;
          if(clipboardData) {
        	  this.query = clipboardData.getData("text");
          }
      } else if(event.type == "drop" && event.originalEvent.dataTransfer) {
    	  var isEmpty = this.$element.val() === "";
    	  this.$element.val("");
    	  this.query = event.originalEvent.dataTransfer.getData("text");
    	  if(!isEmpty) this.$element.val(this.query);//如果不加这一句，当文本框为空时，drop后会显示两遍内容
      } else {
    	  this.query = this.$element.val();
      }
      
      if (this.allowNullQuery === false &&(!this.query || this.query.length < this.options.minLength)) {
        return this.shown ? this.hide() : this;
      }

      items = $.isFunction(this.source) ? this.source(this.query, $.proxy(this.process, this)) : this.source;

      return items ? this.process(items) : this;
    }

  , process: function (items) {
      var that = this

     /* items = $.grep(items, function (item) {
        return that.matcher(item)
      })

      items = this.sorter(items)*/

      if (!items.length) {
        return this.shown ? this.hide() : this
      }

      return this.render(items.slice(0, this.options.items)).show()
    }

  , matcher: function (item) {
      return ~item.toLowerCase().indexOf(this.query.toLowerCase())
    }

  , sorter: function (items) {
      var beginswith = []
        , caseSensitive = []
        , caseInsensitive = []
        , item

      while (item = items.shift()) {
        if (!item.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(item)
        else if (~item.indexOf(this.query)) caseSensitive.push(item)
        else caseInsensitive.push(item)
      }

      return beginswith.concat(caseSensitive, caseInsensitive)
    }

  , highlighter: function (item) {
      var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
      return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
        return '<strong>' + match + '</strong>'
      })
    }

  , render: function (items) {
      var that = this

      items = $(items).map(function (i, item) {
        i = $(that.options.item).attr('data-value', item[that.keyName])
        						.attr('data-text', item[that.textName]);
        
        var text = item[that.textName];
        if(that.listItemHandler && _.isFunction(that.listItemHandler)) {
        	text = that.listItemHandler(item);
        }
        i.find('a').html(that.highlighter(text));
        return i[0];
      })

      items.first().addClass('active');
      this.$menu.html(items);
      return this;
    }

  , setScrollTop: function (element) {
	  var scrollTop = element.offset().top + $(".typeahead").scrollTop() 
		- $(".typeahead").offset().top - $(".typeahead").height();
	  $(".typeahead").scrollTop(scrollTop > 0 ? scrollTop + 20 : 0);
  }

  , next: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , next = active.next();

      if (!next.length) {
        next = $(this.$menu.find('li')[0]);
      }

      next.addClass('active');
      this.setScrollTop(next);
    }

  , prev: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , prev = active.prev()

      if (!prev.length) {
        prev = this.$menu.find('li').last()
      }

      prev.addClass('active')
      this.setScrollTop(prev);
    }
  , showTypeAhead: function(e){
		$(e.target).focus();
		this.lookup(e);
	}
  , listen: function () {
      this.$element
        .on('focus',    $.proxy(this.focus, this))
        .on('blur',     $.proxy(this.blur, this))
        .on('keypress', $.proxy(this.keypress, this))
        .on('keyup',    $.proxy(this.keyup, this))
        .on('drop',     $.proxy(this.drop, this))
        .on('paste',    $.proxy(this.paste, this))
        .on('show',     $.proxy(this.showTypeAhead, this))
        
      if (this.eventSupported('keydown')) {
        this.$element.on('keydown', $.proxy(this.keydown, this))
      }

      var me = this;
      this.$menu
        .on('click', $.proxy(this.click, this))
        .on('mouseenter', function() {me.mousedover = true})
        .on('mouseleave', function() {me.mousedover = false})
        .on('scroll', function() {me.$element.focus()})
        .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
    }

  , eventSupported: function(eventName) {
      var isSupported = eventName in this.$element
      if (!isSupported) {
        this.$element.setAttribute(eventName, 'return;')
        isSupported = typeof this.$element[eventName] === 'function'
      }
      return isSupported
    }

  , move: function (e) {
      if (!this.shown) return

      switch(e.keyCode) {
        case 9: // tab
        case 13: // enter
        case 27: // escape
          e.preventDefault()
          break

        case 38: // up arrow
          e.preventDefault()
          this.prev()
          break

        case 40: // down arrow
          e.preventDefault()
          this.next()
          break
      }

      e.stopPropagation()
    }

  , drop: function (e) {
	  this.lookup(e);
  }

  , paste: function (e) {
	  this.lookup(e);
  }

  , keydown: function (e) {
      this.suppressKeyPressRepeat = ~$.inArray(e.keyCode, [40,38,9,13,27])
      this.move(e)
    }

  , keypress: function (e) {
      if (this.suppressKeyPressRepeat) return
      this.move(e)
    }

  , keyup: function (e) {
	  this.$element.prev().val("");//输入时将hidden中值清空
      switch(e.keyCode) {
        case 40: // down arrow
        case 38: // up arrow
        case 16: // shift
        case 17: // ctrl
        case 18: // alt
          break

        case 9: // tab
        case 13: // enter
          if (!this.shown) return
          this.select()
          break

        case 27: // escape
          if (!this.shown) return
          this.hide()
          break

      	case 39: // right arrow
          if(!this.instantTrigger) this.lookup(e)
        default:
          if(this.instantTrigger) this.lookup(e)
      }

      e.stopPropagation()
      e.preventDefault()
  }

  , focus: function (e) {
      this.focused = true
    }

  , blur: function (e) {
      this.focused = false;
      if (!this.mousedover && this.shown) this.hide()
    }

  , click: function (e) {
      e.stopPropagation();
      e.preventDefault();
      this.select();
      this.$element.focus();
    }

  , mouseenter: function (e) {
      this.$menu.find('.active').removeClass('active')
      $(e.currentTarget).addClass('active')
    }
  }

  /* TYPEAHEAD PLUGIN DEFINITION
   * =========================== */

  var old = $.fn.typeahead

  $.fn.typeahead = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('typeahead')
        , options = typeof option == 'object' && option
      if (!data) $this.data('typeahead', (data = new Typeahead(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.typeahead.defaults = {
    source: []
  , items: 100
  , menu: '<ul class="typeahead dropdown-menu" style="overflow-y:auto;max-height:220px;"></ul>'
  , item: '<li><a href="#"></a></li>'
  , minLength: 1
  }

  $.fn.typeahead.Constructor = Typeahead


 /* TYPEAHEAD NO CONFLICT
  * =================== */

  $.fn.typeahead.noConflict = function () {
    $.fn.typeahead = old
    return this
  }


 /* TYPEAHEAD DATA-API
  * ================== */

  $(document).on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e) {
    var $this = $(this)
    if ($this.data('typeahead')) return
    $this.typeahead($this.data())
  })

}(window.jQuery);