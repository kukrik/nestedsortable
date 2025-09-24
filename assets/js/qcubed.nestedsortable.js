function renderMenuTree(menuArray) {
    const container = $('.sortable.ui-sortable');

    container.empty();

    let html = '';
    let currentDepth = 0;
    let counter = 0;

    function getRenderCellHtml(item) {
        let btns = '';
        const id = item.id;
        const status = item.status === 1 ? 'enable' : 'disable';

        // Status button (except id==1)
        if (id != 1) {
            btns += `<button type="button" class="btn ${status === 'enable' ? 'btn-white' : 'btn-success'} btn-xs" data-status="change" data-id="${id}">
                ${status === 'enable' ? 'Disable' : 'Enable'}
                </button> `;
        }
        // Edit button (always)
        btns += `<button type="button" class="btn btn-darkblue btn-xs" data-toggle="tooltip" data-placement="top" title="Edit" data-id="${id}">
            <i class="fa fa-pencil" aria-hidden="true"></i>
        </button> `;
        // Delete button (except id==1)
        if (id != 1) {
            btns += `<button type="button" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="Delete" data-id="${id}">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </button> `;
        }
        return `<section class="menu-btn-body center-button">${btns}</section>`;
    }

    function getRoutingInfo(item, allItems) {
        // Collect all double redirects (is_redirect===2 && content_type===7)
        const doubleRedirectIds = allItems
            .filter(param => param.is_redirect === 2 && param.content_type === 7)
            .map(param => param.selected_page_id);

        // Check how many times a given id is among redirects
        const count = doubleRedirectIds.filter(id => id === item.id).length;

        if (count === 1) {
            return ' - Redirected to this page: <span style="color: #2980b9;">'
                + (item.menu_text || '') + '</span>';
        } else if (count > 1) {
            return ' - Redirected to this page | <span style="color: #ff0000;">'
                + 'Warning, double redirection: </span><span style="color: #2980b9;">'
                + (item.menu_text || '') + '</span>';
        }
        return '';
    }

    // Main cycle
    for (let i = 0; i < menuArray.length; i++) {
        const item = menuArray[i];
        const id = item.id;
        const depth = item.depth;
        const left = item.left;
        const right = item.right;
        const menuText = item.menu_text || '';
        const status = item.status === 1 ? 'enable' : 'disable';
        const typeObj = item.content_type_object ? ` Type: ${item.content_type_object}` : ' Type: NULL';
        const routingInfo = item.content_type === 8 ? ' - <span style="color: #2980b9;">' + (item.external_url || "") + '</span>' : '';
        const doubleRoutingInfo = getRoutingInfo(item, menuArray);

        // Maintaining ul/li hierarchy:
        if (depth === currentDepth) {
            if (counter > 0) html += '</li>';
        } else if (depth > currentDepth) {
            html += '<ul>';
            currentDepth += (depth - currentDepth);
        } else if (depth < currentDepth) {
            html += '</li>' + '</ul></li>'.repeat(currentDepth - depth);
            currentDepth -= (currentDepth - depth);
        }

        html += `<li id="c2_${id}" class="${left + 1 === right ? 'mjs-nestedSortable-leaf' : 'mjs-nestedSortable-expanded'}">`;

        // DIV, menu-text, type, routinginfo, buttons
        html += (id == 1
                ? `<div class="menu-row-highlight ${status}"><span class="reorder"><i class="fa fa-bars"></i></span>
            <span class="disclose"><span></span></span><section class="menu-body">${menuText}<span class="separator">&nbsp;</span>${typeObj}${routingInfo}${doubleRoutingInfo}`
                : `<div class="menu-row ${status}"><span class="reorder"><i class="fa fa-bars"></i></span>
            <span class="disclose"><span></span></span><section class="menu-body">${menuText}<span class="separator">&nbsp;</span>${typeObj}${routingInfo}${doubleRoutingInfo}`
        );

        html += '</section>';
        html += getRenderCellHtml(item);
        html += '</div>';
        counter++;
    }

    if (currentDepth > 0) {
        html += '</li>' + '</ul></li>'.repeat(currentDepth);
    }

    html += '</ul>';

    container.html(html);
}

/*function fetchAndRenderSortable() {
    $.getJSON('../assets/php/nestedsortable-json.php', function (data) {
        renderMenuTree(data);

        console.log(data);

    });
}*/

function fetchAndRenderSortable() {
    fetch("../assets/php/nestedsortable-json.php")
        .then((response) => {
            if(!response.ok){ // Before parsing (i.e. decoding) the JSON data,
                // check for any errors.
                // In case of an error, throw.
                throw new Error("Something went wrong!");
            }
            return response.json(); // Parse the JSON data.
        })
        .then((data) => {
            renderMenuTree(data);
        });
}

$(document).ready(function() {
    fetchAndRenderSortable();
});


// $(document).on('click', '.js-btn-save', function(){
//     const id = $(this).data('id');
//     // Ava modaal jne...
//     //alert(id);
//     fetchAndRenderSortable();
// });

// // Events/Actions
$(document).on('click', '.btn-darkblue', function(){
    const id = $(this).data('id');
    // Ava modaal jne...


    //alert(id);
});

$(document).on('click', '.js-btn-add', function() {
    $('.sortable').nestedSortable('disable');
    $('.js-btn-add').attr('disabled', true);
    $('.js-btn-collapse').attr('disabled', true);
    $('.js-btn-expand').attr('disabled', true);


    // Eesmärk vähendada php kontrollide arvu
    // Need ei tööta veel õigesti...
    // const textbox = $('.js-textbox');
    // console.log(textbox);
    //
    // if (textbox.length === 0) {
    //     $('.js-btn-save').attr('disabled', true);
    //     $('.js-btn-cancel').attr('disabled', true);
    // } else {
    //     $('.js-btn-save').attr('disabled', false);
    //     $('.js-btn-cancel').attr('disabled', false);
    // }
});

$(document).on('click', '.js-btn-cancel, .js-btn-save', function() {
    $('.sortable').nestedSortable('enable');
    $('.js-btn-add').attr('disabled', false);
    $('.js-btn-collapse').attr('disabled', false);
    $('.js-btn-expand').attr('disabled', false);

});

// Collapse/expanse
$(document).on('click', '.js-btn-collapse', function() {
    $('.sortable').find('li.mjs-nestedSortable-expanded').removeClass('mjs-nestedSortable-expanded').addClass('mjs-nestedSortable-collapsed');
});

$(document).on('click', '.js-btn-expand', function() {
    $('.sortable').find('li.mjs-nestedSortable-collapsed').removeClass('mjs-nestedSortable-collapsed').addClass('mjs-nestedSortable-expanded');
});

$(document).on('click', '.disclose', function() {
    $(this).closest('li').toggleClass('mjs-nestedSortable-expanded').toggleClass('mjs-nestedSortable-collapsed');
});