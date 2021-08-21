//language
function __(key) {
    if (typeof(language) !== 'undefined' && language[key]) {
        return language[key];
    }
    return key;
}

// unblock when ajax activity stops
$(document).ajaxStop($.unblockUI);
// don't use default css
$.blockUI.defaults.css = {};
$.blockUI.defaults.ignoreIfBlocked = true;
$.blockUI.defaults.message   = __('processing');
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

$(function() {

    var permission = $('select#permission').val();
    if (permission.length > 0) {
        var text = $( "select#permission option:selected" ).text();
        var perm = permission;
        var role = $('#role').val();
        var acts = $('#actions').attr('value');

        //display corresponding input block
        $.ajax({
            url : base_url + "admin/roleperm/ajaxload_actions",
            type: "POST",
            data: ({perm: text, pid: perm, rid: role, acts: acts}),
            async: true,
            success: function(res) {
                $("#actions").empty();
                $("#actions").append(res);
            }
        });
    }

    // change permission
    $('#permission').change(function(){
        //block page until ajax call completes
        //$.blockUI();

        var text = $( "#permission option:selected" ).text();
        var perm = $(this).val();
        var role = $('#role').val();
        var acts = '';
        var priperm = $('#actions').attr('prim-perm');

        if (text === priperm) {
            location.reload();
        }

        //display corresponding input block
        $.ajax({
            url : base_url + "admin/roleperm/ajaxload_actions",
            type: "POST",
            data: ({perm: text, pid: perm, rid: role, acts: acts, priperm: priperm}),
            async: true,
            success: function(res) {
                $("#actions").empty();
                $("#actions").append(res);
            }
        });
    });

    // Role - permission
    $(document).on('click', '.checkbox-act', function() {
        var perm = $('#permission').val();
        var role = $('#role').val();
        var i = 0;
        var n = $(".checkbox-act:checked").length;
        var final = 'a:'+n+':{';
        $('.checkbox-act:checked').each(function(){
            var value = $(this).val();
            final += 'i:' + i + ';' + 'i:' + value + ';';
            i = i + 1;
        });
        final += '}';
        if (n === 0) {
            final = '';
        }
        $("#act").val('');
        $("#act").val(final);
    });
});

