require.config({
    paths: {
        highlight: '/scripts/highlight.pack'
    },
    shim: {
        'highlight': {
            exports: 'hljs'
        }
    }
});

require([
    "highlight"
], function (hljs) {
    'use strict';

    hljs.initHighlighting();

});
