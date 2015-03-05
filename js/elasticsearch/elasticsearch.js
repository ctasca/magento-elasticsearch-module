/**
 * Quick Search form client model
 */
Elasticsearch = {};

Elasticsearch.Magento = Class.create();

Elasticsearch.Magento.prototype = {
    initialize: function(url) {
        this.baseurl = url;
    },

    getBaseUrl: function() {
        return this.baseurl;
    }
};

Elasticsearch.Search = Class.create();

Elasticsearch.Search.prototype = {
    initialize : function(form, field, emptyText, store){
        this.form   = $(form);
        this.field  = $(field);
        this.emptyText = emptyText;
        this.store = store;
        Event.observe(this.form,  'submit', this.submit.bind(this));
        Event.observe(this.field, 'focus', this.focus.bind(this));
        Event.observe(this.field, 'blur', this.blur.bind(this));
        this.blur();
    },

    submit : function(event){
        if (this.field.value == this.emptyText || this.field.value == ''){
            Event.stop(event);
            return false;
        }
        return true;
    },

    focus : function(event){
        if(this.field.value==this.emptyText){
            this.field.value='';
        }

    },

    blur : function(event){
        if(this.field.value==''){
            this.field.value=this.emptyText;
        }
    },

    initAutocomplete : function(url, destinationElement){
        new Ajax.Autocompleter(
            this.field,
            destinationElement,
            url,
            {
                paramName: this.field.name,
                //indicator: 'elasticsearch-indicator',
                method: 'get',
                minChars: 2,
                //updateElement: this._selectAutocompleteItem.bind(this),x
                onShow : function(element, update) {
                    if(!update.style.position || update.style.position=='absolute') {
                        update.style.position = 'absolute';
                        Position.clone(element, update, {
                            setHeight: false,
                            offsetTop: element.offsetHeight
                        });
                    }
                    Effect.Appear(update,{duration:0});
                }
            }
        );
    },

    _selectAutocompleteItem : function(element){
        if(element.title){
            //this.field.value = element.title;
        }

        //this.form.submit();
    },

    gotoSuggestionPath: function (path) {
        var baseurl = this.store.getBaseUrl();
        top.location.href = baseurl + path;
    }
};
