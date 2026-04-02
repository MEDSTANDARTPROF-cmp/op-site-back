window.Clipboard = (function (window, document, navigator) {
    function copyToClipboard(text) {
        var selected,
            el = document.createElement('textarea');

        el.value = text;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        selected = document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false;
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        if (selected) {
            document.getSelection().removeAllRanges();
            document.getSelection().addRange(selected);
        }
    }

    var copy = function (text) {
        try {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
            } else if (window.clipboardData) {  // IE
                window.clipboardData.setData('text', text);
            } else {  // other browsers, iOS, Mac OS
                copyToClipboard(text);
            }
        } catch (e) {
            console.log(e);
        }
    };

    return {
        copy: copy
    };
})(window, document, navigator);