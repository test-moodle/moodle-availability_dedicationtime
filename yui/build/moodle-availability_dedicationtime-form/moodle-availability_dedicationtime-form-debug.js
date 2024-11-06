YUI.add('moodle-availability_dedicationtime-form', function (Y, NAME) {

// eslint-disable-next-line camelcase
M.availability_dedicationtime = M.availability_dedicationtime || {};

M.availability_dedicationtime.form = Y.Object(M.core_availability.plugin);

M.availability_dedicationtime.form.initInner = function(param) {
    this.params = param;
};

M.availability_dedicationtime.form.getNode = function(json) {
    var node = Y.Node.create('<span>' + this.params + '</span>');
    if (json.dedicationtime === undefined) {
        json.dedicationtime = '';
    }
    if (json.unit === undefined) {
        json.unit = 'hours';
    }
    var dtime = json.dedicationtime;
    var dunit = json.unit;
    var dinput = node.one('input[name=availability_dedicationtime_value]');
    dinput.set('value', dtime);
    var dselect = node.one('select[name=availability_dedicationtime_unit]');
    dselect.set('value', dunit);
    if (!M.availability_dedicationtime.form.addedEvents) {
        M.availability_dedicationtime.form.addedEvents = true;
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('click', function() {
            M.core_availability.form.update();
        }, '.availability_dedicationtime select');
        root.delegate('change', function() {
            M.core_availability.form.update();
        }, '.availability_dedicationtime input[name=availability_dedicationtime_value]');
    }

    return node;
};

M.availability_dedicationtime.form.fillValue = function(value, node) {
    value.dedicationtime = node.one('input[name=availability_dedicationtime_value]').get('value');
    value.unit = node.one('select[name=availability_dedicationtime_unit]').get('value');
};

M.availability_dedicationtime.form.fillErrors = function(errors, node) {
    var dedicationtime = Number(node.one('input[name=availability_dedicationtime_value]').get('value'));
    if (isNaN(dedicationtime) || dedicationtime == 0) {
        errors.push('availability_dedicationtime:error_invalidnumber');
    }
};

}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
