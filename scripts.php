<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>
    (function($) {
    $.fn.tablesorter = function() {
        var $table = this;
        this.find('th').click(function() {
            var idx = $(this).index();
            var direction = $(this).hasClass('sort_asc');
            $table.tablesortby(idx, direction);
        });
        return this;
    };
    $.fn.tablesortby = function(idx, direction) {
        var $rows = this.find('tbody tr');

        function elementToVal(a) {
            var $a_elem = $(a).find('td:nth-child(' + (idx + 1) + ')');
            var a_val = $a_elem.attr('data-sort') || $a_elem.text();
            return (a_val == parseInt(a_val) ? parseInt(a_val) : a_val);
        }
        $rows.sort(function(a, b) {
            var a_val = elementToVal(a),
                b_val = elementToVal(b);
            return (a_val > b_val ? 1 : (a_val == b_val ? 0 : -1)) * (direction ? 1 : -1);
        })
        this.find('th').removeClass('sort_asc sort_desc');
        $(this).find('thead th:nth-child(' + (idx + 1) + ')').addClass(direction ? 'sort_desc' : 'sort_asc');
        for (var i = 0; i < $rows.length; i++)
            this.append($rows[i]);
        this.settablesortmarkers();
        return this;
    }
    $.fn.retablesort = function() {
        var $e = this.find('thead th.sort_asc, thead th.sort_desc');
        if ($e.length)
            this.tablesortby($e.index(), $e.hasClass('sort_desc'));

        return this;
    }
    $.fn.settablesortmarkers = function() {
        this.find('thead th span.indicator').remove();
        this.find('thead th.sort_asc').append('<span class="indicator">&darr;<span>');
        this.find('thead th.sort_desc').append('<span class="indicator">&uarr;<span>');
        return this;
    }

})(jQuery);

$(function() {
    var XSRF = (document.cookie.match('(^|; )_sfm_xsrf=([^;]*)') || 0)[2];
    var MAX_UPLOAD_SIZE = <?php echo $MAX_UPLOAD_SIZE ?>;
    var searchParams = new URLSearchParams(window.location.search);
    var existing_file_names = [];
    var $tbody = $('#list');

    $(window).on('hashchange', list).trigger('hashchange');
    $('#table').tablesorter();

    $('#table, #item_popup').on('click', '.delete', function(data) {
        $.post("", {
            'do': 'delete',
            file: $(this).attr('data-file'),
            xsrf: XSRF
        }, function(response) {
            $('#item_popup').hide();
            list();
        }, 'json');
        return false;
    });


    $('body').on('click', '#create_new_folder', function(e) {
        $('#myForm').removeClass('d-none');
        $('#dirname').val('');
        $('.error_messages').text('');
        $('.overlay').removeClass('d-none');
    });

    $('body').on('click', '#rename_item', function(e) {
        $('#folder_rename_form').removeClass('d-none');
        $('#folder_rename').attr('data-file', $(this).data('file'));
        $('.overlay').removeClass('d-none');
    });

    $('body').on('click', '.breadcrumb_value, .item', function(e) {
        $('.search_input').val('');
        searchParams.delete('search')
    });

    $('.close_folder_creation_form, .close_icon').on('click', function(e) {
        closeFolderCreationForm();
    });

    $('.close_folder_rename_form, .close_icon_rename_form').on('click', function(e) {
        closeFolderRenameForm();
    });

    // Prevent the default context menu
    $('body').on('contextmenu', '.item', function (e) {
        e.preventDefault();
        let data_file = $(this).attr('data-file');
        let is_dir = $(this).attr('data-is-dir');

        $('#folder_rename').attr('data-file', data_file);
        $('#rename_item').toggleClass('d-none', is_dir !== 'true');
        $('#download_item').attr('href', '?do=download&file=' + data_file);
        $('#delete_item').attr('data-file', data_file);
        $("#item_popup").css({
            left:e.pageX + 'px',
            top:e.pageY + 'px'
        }).show();
    });

    $('#search').on('input', function (e) {
        //$('#loading_roller').removeClass('d-none');
        searchParams.set('search', $(this).val());
        searchParams.set('search_scope', $('#search_scope').find(':selected').val());
        list();
        //$('#loading_roller').addClass('d-none');
    });

    $('#refresh_button').on('click', function () {
        let currentUrl = window.location.href.split('?')[0];
        window.history.replaceState({}, '', currentUrl);
        window.location.reload();
    });

    // Hide the popup when clicking outside of it
    $(document).on('click', function () {
        $('#item_popup').hide();
    });

    $('body').on('submit', 'form#folder_rename', (function(e) {
        let hashval = decodeURIComponent(window.location.hash.substr(1));
        let $dir = $(this).find('[name=new_folder_name]');
        let oldname = $(this).attr('data-file');
        $('.error_messages').empty();

        $dir.val().length && $.post('?', {
            'do': 'folder_rename',
            name: $dir.val(),
            old_name: oldname,
            xsrf: XSRF,
            file: hashval
        }, function(data) {
            if (data.success) {
                closeFolderRenameForm();
                list();
                $dir.val('');
            } else {
                $('.error_messages').text(data.message);
            }
        }, 'json');

        return false;
    }));

    $('#mkdir').submit(function(e) {
        var hashval = decodeURIComponent(window.location.hash.substr(1)),
            $dir = $(this).find('[name=name]');
        e.preventDefault();
        $dir.val().length && $.post('?', {
            'do': 'mkdir',
            name: $dir.val(),
            xsrf: XSRF,
            file: hashval
        }, function(data) {
            if (data.success) {
                closeFolderCreationForm();
                list();
                $dir.val('');
            } else {
                $('.error_messages').text(data.message);
            }
        }, 'json');

        return false;
    });

    <?php if ($allowUpload): ?>
        $('input[type=file]').change(function(e) {
            e.preventDefault();
            $.each(this.files, function(k, file) {
                uploadFile(file);
            });
        });

        function closeFolderCreationForm() {
            $('#myForm').addClass('d-none');
            $('.overlay').addClass('d-none');
        };

        function closeFolderRenameForm() {
            $('.error_messages').empty();
            $('#folder_rename_form').addClass('d-none');
            $('.overlay').addClass('d-none');
        };

        function uploadFile(file) {
            if (existing_file_names.includes(file.name)) {
                if(!confirm(`${file.name} already exists. Do you want to replace it?`)) {
                    return;
                }
            }

            var folder = decodeURIComponent(window.location.hash.substr(1));

            if (file.size > MAX_UPLOAD_SIZE) {
                var $error_row = renderFileSizeErrorRow(file, folder);
                $('#upload_progress').append($error_row);
                window.setTimeout(function() {
                    $error_row.fadeOut();
                }, 5000);
                return false;
            }

            var $row = renderFileUploadRow(file, folder);
            $('#upload_progress').append($row);
            var fd = new FormData();
            fd.append('file_data', file);
            fd.append('file', folder);
            fd.append('xsrf', XSRF);
            fd.append('do', 'upload');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '?');
            xhr.onload = function() {
                //let error_responses =  JSON.parse(xhr.responseText);
                //console.log(error_responses.error.msg);
                if (xhr.status == 200) {
                    $row.remove();
                    list();
                    return;
                }
            };
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    $row.find('.progress').css('width', (e.loaded / e.total * 100 | 0) + '%');
                }
            };
            xhr.send(fd);
        }

        function renderFileUploadRow(file, folder) {
            return $row = $('<div/>')
                .append($('<span class="fileuploadname" />').text((folder ? folder + '/' : '') + file.name))
                .append($('<div class="progress_track"><div class="progress"></div></div>'))
                .append($('<span class="size" />').text(formatFileSize(file.size)))
        };

        function renderFileSizeErrorRow(file, folder) {
            return $row = $('<div class="error" />')
                .append($('<span class="fileuploadname" />').text('Error: ' + (folder ? folder + '/' : '') + file.name))
                .append($('<span/>').html(' file size - <b>' + formatFileSize(file.size) + '</b>' +
                    ' exceeds max upload size of <b>' + formatFileSize(MAX_UPLOAD_SIZE) + '</b>'));
        }
    <?php endif; ?>

    function list() {
        let hashval = window.location.hash.substr(1);
        let searchterm = searchParams.get('search') ?? '';
        let searchscope = searchParams.get('search_scope') ?? '';
        $.get('?do=list&file=' + hashval + '&search=' + searchterm + '&search_scope=' + searchscope, function(data) {
            $tbody.empty();
            $('#breadcrumb').empty().html(renderBreadcrumbs(hashval));
            if (data.success) {
                existing_file_names = [];
                $.each(data.results, function(k, v) {
                    existing_file_names.push(v.name);
                    $tbody.append(renderFileRow(v));
                });
                !data.results.length && $tbody.append('<tr><td class="empty" colspan=5>This folder is empty</td></tr>')
                data.is_writable ? $('body').removeClass('no_write') : $('body').addClass('no_write');
            } else {
                console.warn(data.error.msg);
            }
            $('#table').retablesort();
        }, 'json');
    }

    function renderFileRow(data) {
        var $link = $(`<a class="name item" onclick="${!data.is_dir ? 'return false' : ''}"/>`)
            .attr('href', data.is_dir ? '#' + encodeURIComponent(data.path) : './' + encodeURIComponent(data.path))
            .attr('data-is-dir', data.is_dir)
            .attr('data-file', data.path)
            .text(data.name);
        var allow_direct_link = <?php echo $allowDirectLink ? 'true' : 'false'; ?>;
        if (!data.is_dir && !allow_direct_link) $link.css('pointer-events', 'none');
        var $dl_link = $('<a/>').attr('href', '?do=download&file=' + encodeURIComponent(data.path))
            .addClass('download').text('download');
        // var $delete_link = $('<a href="#" />').attr('data-file', data.path).addClass('delete').text('delete');
        var perms = [];
        if (data.is_readable) perms.push('read');
        if (data.is_writable) perms.push('write');
        if (data.is_executable) perms.push('exec');
        var $html = $('<tr />')
            .addClass(data.is_dir ? 'is_dir' : '')
            .append($('<td class="first" />').append($link))
            .append($('<td/>').attr('data-sort', data.is_dir ? -1 : data.size)
                .html($('<span class="size" />').text(formatFileSize(data.size))))
            .append($('<td/>').attr('data-sort', data.mtime).text(formatTimestamp(data.mtime)))
            // .append($('<td/>').append($dl_link).append(data.is_deleteable ? $delete_link : ''))
        return $html;
    }

    function renderBreadcrumbs(path) {
        var base = "",
            $html = $('<div class="breadcrumb_items"/>').append($('<a class="breadcrumb_value" href=#>Home</a></div>'));
        $.each(path.split('%2F'), function(k, v) {
            if (!v) {
                return;
            }

            let v_as_text = decodeURIComponent(v);
            if (v_as_text !== '.') {
                $html.append($('<span/>').text(' ▸ '))
                    .append($('<a class="breadcrumb_value"/>').attr('href', '#' + base + v).text(v_as_text));
                base += v + '%2F';
            }
        });

        return $html;
    }

    function formatTimestamp(unix_timestamp) {
        var m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var d = new Date(unix_timestamp * 1000);
        return [m[d.getMonth()], ' ', d.getDate(), ', ', d.getFullYear(), " ",
            (d.getHours() % 12 || 12), ":", (d.getMinutes() < 10 ? '0' : '') + d.getMinutes(),
            " ", d.getHours() >= 12 ? 'PM' : 'AM'
        ].join('');
    }

    function formatFileSize(bytes) {
        var s = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
        for (var pos = 0; bytes >= 1000; pos++, bytes /= 1024);
        var d = Math.round(bytes * 10);
        return pos ? [parseInt(d / 10), ".", d % 10, " ", s[pos]].join('') : bytes + ' bytes';
    }
})
</script>