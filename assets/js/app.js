import '../css/app.scss';
import 'bootstrap';
import DropUpload from 'drop-upload';
import MarkdownIt from 'markdown-it';


($ => {
    "use strict";

    $(document).on('drop-upload:error', '.markdown-textarea', (e) => {
        $('.status').text($('.status').data('error'));
        setTimeout(() => {
            $('.status').text($('.status').data('default'));
        }, 5000);
    })

    $(document).on('drop-upload:start', '.markdown-textarea', (e) => {
        $('.status').text($('.status').data('uploading'));
    })

    $(document).on('drop-upload:success', '.markdown-textarea', (e) => {
        $('.status').text($('.status').data('uploaded'));
        setTimeout(() => {
            $('.status').text($('.status').data('default'));
        }, 2000);
    })

    let md = new MarkdownIt();
    let tId;
    $(document).on('change', '.markdown-textarea', (e) => {
        clearTimeout(tId);

        tId = setTimeout(() => {

            let html = md.render($(e.target).val());
            $('.markdown-target').html(html);
        }, 2000);
    })

    DropUpload.options.decodeResponseCallback = (r) => {
        try {
            return JSON.parse(r).fileName;
        } catch (err) {
            return false;
        }
    }
    // DropUpload.options.uploadPath = '/fds';
    DropUpload(document, '.markdown-textarea');

    let html = md.render($('.markdown-textarea').val());
    $('.markdown-target').html(html);

})(jQuery);
