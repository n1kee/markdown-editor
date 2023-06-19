$(function() {

    function normalizeText(query) {
        return query.trim().toLowerCase();
    }

    const selectorColumnFilters = ['author', 'status', 'tool'];

    $('table').bind('dynatable:preinit', function(e, dynatable) {
        dynatable.utility.textTransform.custom = text => {
            text = text.toLocaleLowerCase();
            return dynatable.utility.textTransform.lowercase(text).trim(); 
        };
    }).bind('dynatable:init', function(e, dynatable) {
        selectorColumnFilters.forEach(columnName => {
            dynatable.queries.functions[ columnName ] = (record, queryValue) => {
                return normalizeText(record[ columnName ]) === normalizeText(queryValue);
            };
        });
        dynatable.queries.functions.search = (record, queryValue) => {
            queryValue = normalizeText(queryValue);

            const searchColumns = [ 'title', 'url', 'content'];

            return !!searchColumns.find(searchColumnName => {
                const recordValue = normalizeText(record[ searchColumnName ]);
                const hasMatch = recordValue.includes(queryValue);
                return hasMatch;
            });
        };
    }).dynatable({
      table: {
        defaultColumnIdStyle: 'custom'
      },
      features: {
        paginate: false,
        search: false,
        recordCount: false
      },
      inputs: {
        queries: $('.search-query'),
      }
    });

    const textEditor = new toastui.Editor({
        el: $('#editor-container')[0],
        previewStyle: 'vertical',
        height: '500px',
        initialValue: ''
    });
    

    $(".agent-list").on('click', '.article-edit-btn', function(event) {
        event.preventDefault();
        this.blur();
        textEditor.setMarkdown('');
        const $target = $(this);
        const articleUrl = $target.data('article-url');
        const $elem = $("#editor-modal");

        fetch("/?article-url=" + articleUrl)
            .then(res => res.text())
            .then(text => {
                textEditor.setMarkdown(text);
                textEditor.articleUrl = articleUrl;
                $elem.modal();
            })
            .catch(error => $("#error-modal").modal());
    });

    $('#editor-modal-save').click(function(event) {
        event.preventDefault();
        fetch("/", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
                'article-url': textEditor.articleUrl,
                'article-content': textEditor.getMarkdown()
            }),
        })
        .then(res => { })
        .catch(error => $("#error-modal").modal());
    });
});