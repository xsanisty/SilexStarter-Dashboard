/**
 * {{User}} manager function.
 *
 * require jQuery, datatable, toaster
 */
function TableManager(options) {
    this.options = $.extend(true, {
        restApi : window.location.href,
        csrfToken : null,
        datatableApi : null,
        entity : null,
        dom : null,
        scrollX : false,
        scrollY : false,
        columnDefs : [],
        renderIdAsButton: true, //id should be placed at last cols
        buttonColumnWidth : '150px',
        buttonRenderer : null,
        immediateInit : true,
        on : {},
    }, options);

    if (!this.options.entity) {
        throw new Error('Entity name not defined!');
    }

    this.snakeCasedEntity = this.options.entity.replace(/-/g, '_');
    this.kebabCasedEntity = this.options.entity.replace(/_/g, '-');
    this.baseTable      = $('#' + this.snakeCasedEntity + '_table');
    this.datatable      = null;
    this.event          = this.initEventDispatcher();
    this.options.dom    = this.options.dom || "<'row'<'col-md-12'<'box box-primary'<'box-header with-border'<'row'<'col-md-6'<'#" + this.snakeCasedEntity + "_button_wrapper'>><'col-md-3 hidden-xs'l><'col-md-3 hidden-xs'f>>> <'box-body no-padding'tr><'box-footer clearfix'<'col-sm-5'i><'col-sm-7'p>>>>>";

    if (window.toastr) {
        this.notifier   = toastr;
    } else {
        this.notifier   = {
            success : alert,
            error   : alert,
            warning : alert,
            info    : alert
        }
    }
    for (var ev in this.options.on) {
        this.event.listen(ev, this.options.on[ev]);
    }

    if (this.options.immediateInit) {
        this.init();
    }
}

TableManager.fn = TableManager.prototype;

TableManager.fn.init = function() {
    var self = this;

    this.event.dispatch('before_init');

    if (this.options.renderIdAsButton) {
        var renderer = this.options.buttonRenderer ? this.options.buttonRenderer : function (data, type, row, meta) {
            var button = '<button href="#' + self.kebabCasedEntity + '-edit" class="btn btn-xs btn-default btn-' + self.kebabCasedEntity + '-edit" data-id="' + data + '"><i class="fa fa-fw fa-pencil"></i> edit</button>';
                button += '<button href="#' + self.kebabCasedEntity + '-delete" class="btn btn-xs btn-danger btn-' + self.kebabCasedEntity + '-delete" data-id="' + data + '" style="margin-left: 5px"><i class="fa fa-fw fa-trash"></i> delete</button>';

            return button;
        };

        this.options.columnDefs.push({
            targets     : -1,
            className   : 'text-center',
            orderable   : false,
            data        : 'id',
            searchable  : false,
            width       : this.options.buttonColumnWidth,
            render      : renderer
        });
    }

    $(document).ready(function(){
        self.event.dispatch('table.before_init', {table : self.baseTable});

        self.initDatatable()
        self.baseTable.on('click', '.btn-' + self.kebabCasedEntity + '-edit', function(e){
            e.preventDefault();

            var button = $(this).prop('disabled', true);
            var entityId = button.attr('data-id');

            button.find('i').attr('class', 'fa fa-fw fa-spin fa-spinner');

            self.edit(entityId).always(function() {
                button.prop('disabled', false).html('<i class="fa fa-fw fa-pencil"></i> edit');
            });
        });

        self.baseTable.on('click', '.btn-' + self.kebabCasedEntity + '-delete', function(e){
            e.preventDefault();

            if(confirm('Are you sure want to delete this ' + self.snakeCasedEntity.replace(/_/g, ' ') + '?')){
                var button = $(this).prop('disabled', true);
                var entityId = button.attr('data-id');

                button.find('i').attr('class', 'fa fa-fw fa-spin fa-spinner');

                self.delete(entityId).always(function() {
                    button.prop('disabled', false).html('<i class="fa fa-fw fa-trash"></i> delete');
                });
            }
        });

        $('#btn_' + self.snakeCasedEntity + '_save').click(function(e) {
            e.preventDefault();

            var button      = $(this).prop('disabled', true).text('saving...');
            var entityData  = $('#' + self.snakeCasedEntity + '_form').serializeJSON();
            var method      = $('#' + self.snakeCasedEntity + '_method_field').val();
            var enableBtn   = function() {
                button.prop('disabled', false).text('save');
            }

            if (method == 'PUT') {
                self.update(entityData[self.snakeCasedEntity + '_data']).always(enableBtn);
            } else {
                self.store(entityData[self.snakeCasedEntity + '_data']).always(enableBtn);
            }
        });

        $('#btn_' + self.snakeCasedEntity + '_create').appendTo('#' + self.snakeCasedEntity + '_button_wrapper').click(function(e){
            e.preventDefault();
            self.create();
        });

        self.event.dispatch('table.after_init', {table: self.baseTable});
    });

    this.notifier.options = {
        "closeButton": true,
        "newestOnTop": true,
        "positionClass": "toast-top-right",
    }

    this.event.dispatch('after_init');
}

TableManager.fn.initEventDispatcher = function() {
    var self = this;
    /**
     * @author mrdoob / http://mrdoob.com/
     */

    function EventDispatcher() {}

    Object.assign(EventDispatcher.prototype, {
        listen: function (type, listener) {
            if (this._listeners === undefined) this._listeners = {};

            var listeners = this._listeners;
            if (listeners[type] === undefined) {
                listeners[type] = [];
            }

            if (listeners[type].indexOf(listener) === - 1) {
                listeners[type].push(listener);
            }
        },

        hasListener: function (type, listener) {
            if (this._listeners === undefined) return false;

            return this._listeners[type] !== undefined && this._listeners[type].indexOf(listener) !== - 1;
        },

        removeListener: function (type, listener) {
            if (this._listeners === undefined) return;

            var listenerArray = this._listeners[type];

            if (listenerArray !== undefined) {
                var index = listenerArray.indexOf(listener);

                if (index !== - 1) {
                    listenerArray.splice(index, 1);
                }
            }
        },

        dispatch: function (eventName, eventData) {
            if (this._listeners === undefined) return;

            eventData = eventData || {};
            var listeners = this._listeners[eventName];

            if (listeners !== undefined) {
                eventData.target = self;

                var listeners = listeners.slice(0);
                for (var i = 0, l = listeners.length; i < l; i ++) {
                    listeners[i].call(this, eventData);
                }
            }
        }
    });

    return new EventDispatcher();
}

TableManager.fn.reload = function() {
    this.datatable.ajax.reload(null, false);
}

/**
 * Fetch entity data from the server
 */
TableManager.fn.fetch = function(entityId) {
    return $.ajax({
        'method' : 'GET',
        'url' : this.options.restApi + entityId
    });
}

/**
 * Display edit form
 */
TableManager.fn.edit = function(entityId) {
    var self = this;

    return this.fetch(entityId)
        .done(function(data, status, xhr) {
            self.event.dispatch('edit_form.before_shown', {entity : data});

            $('#' + self.snakeCasedEntity + '_form')[0].reset();

            var elem;
            for(var a in data){
                elem = $('#' + self.snakeCasedEntity + '_'+a+'_field');
                elem.val(data[a]);
            }

            $('#' + self.snakeCasedEntity + '_form_title').text('Edit ' + self.snakeCasedEntity.replace(/_/g, ' '));
            $('#' + self.snakeCasedEntity + '_method_field').val('PUT');
            $('#' + self.snakeCasedEntity + '_token_field').val(self.options.csrfToken);
            $('#' + self.snakeCasedEntity + '_form_modal').modal('show');

            self.event.dispatch('edit_form.after_shown');
        })
        .fail(function(xhr, status, error) {
            self.showErrorResponse(xhr);
        });
}

/**
 * Send entity data to server for update
 */
TableManager.fn.update = function(entity) {
    var self = this;

    entity._token   = this.options.csrfToken;
    fieldGroup      = self.snakeCasedEntity + '_data';
    entityData      = {};

    entityData[fieldGroup] = entity;

    return $.ajax({
        method  : 'PUT',
        url     : self.options.restApi + entity.id,
        data    : entityData
    }).done(function(data, status, xhr){
        self.notifier.success(data.content);

        $('#btn_' + self.snakeCasedEntity + '_save').prop('disabled', false).text('save');
        $('#' + self.snakeCasedEntity + '_form_modal').modal('hide');
        self.reload();
    }).fail(function(hxr, status, error){
        $('#btn_' + self.snakeCasedEntity + '_save').prop('disabled', false).text('save');
        self.showErrorResponse(hxr);
    });
}

/**
 * Delete entity data from server
 */
TableManager.fn.delete = function(entityId) {
    var self = this;

    return $.ajax({
        method  : 'DELETE',
        data    : {_token : self.options.csrfToken},
        url     : self.options.restApi + entityId
    }).done(function(data, status, xhr){
        self.notifier.success(data.content);
        self.reload();
    }).fail(function(xhr, status, error){
        self.showErrorResponse(xhr);
    });
}

/**
 * Display create form
 */
TableManager.fn.create = function() {
    this.event.dispatch('create_form.before_shown');

    $('#' + this.snakeCasedEntity + '_form_title').text('Create new ' + this.snakeCasedEntity.replace(/_/g, ' '));
    $('#' + this.snakeCasedEntity + '_form')[0].reset();

    $('#' + this.snakeCasedEntity + '_method_field').val('POST');
    $('#' + this.snakeCasedEntity + '_token_field').val(this.options.csrfToken);
    $('#' + this.snakeCasedEntity + '_id_field').val('');

    $('#' + this.snakeCasedEntity + '_form_modal').modal('show');

    this.event.dispatch('create_form.after_shown');
}

/**
 * Send data to server for create
 */
TableManager.fn.store = function(entity) {
    var self = this;

    entity._token   = this.options.csrfToken;
    fieldGroup      = self.snakeCasedEntity + '_data';
    entityData      = {};

    entityData[fieldGroup] = entity;

    return $.ajax({
        method  : 'POST',
        url     : self.options.restApi,
        data    : entityData
    }).done(function(data, status, xhr){
        $('#' + self.snakeCasedEntity + '_form_modal').modal('hide');
        self.notifier.success(data.content);
        self.reload();
    }).fail(function(xhr, status, error){
        self.showErrorResponse(xhr);
    });
}

/**
 * Display notification on error
 */
TableManager.fn.showErrorResponse = function(response) {
    if(response.status == 401){
        this.notifier.error('Your session is expired!\n\nYou will be redirected to the login page shortly.');
        setTimeout(function() {
            window.location.reload();
        }, 1000)
    } else {
        var message = 'Unknown error occured';

        if (response.responseJSON && response.responseJSON.message) {
            message = '<strong>' + response.responseJSON.message + '</strong>'
        }

        if (response.responseJSON && response.responseJSON.errors) {
            var errors = response.responseJSON.errors;

            message += '<ul style="list-style-type:none; padding-left:0">';

            for (var e in errors) {
                if (typeof errors[e] == 'string') {
                    message += '<li>' + e + ' : ' + errors[e] + '</li>';
                } else {
                    var detailedMessage = '';

                    for (var d in errors[e]) {
                        detailedMessage += '<li>' + errors[e][d] + '</li>';
                    }

                    message += '<li><strong>' + e + ' :</strong> <ul style="padding-left: 20px">' + detailedMessage + '</ul></li>';
                }
            }

            message += '</ul>';
        }

        this.notifier.error(message);
    }
}

/**
 * Initialize datatable
 */
TableManager.fn.initDatatable = function() {
    var self = this;

    this.datatable = self.baseTable.DataTable({
        processing  : true,
        scrollX     : this.options.scrollX,
        scrollY     : this.options.scrollY,
        serverSide  : true,
        responsive  : true,
        dom         : this.options.dom,
        /** column definition for action column */
        columnDefs  : self.options.columnDefs,
        autoWidth   : false,
        language    : {
            paginate: {
                previous: '&laquo;',
                next    : '&raquo;'
            }
        },
        ajax: {
            url     : self.options.datatableApi,
            type    : 'POST',
            data    : {
                _token : self.options.csrfToken
            },
            error   : function(resp){
                self.showErrorResponse(resp);
            }
        }
    });

    return this.datatable;
}
