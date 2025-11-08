(function ($) {
function renderColumn(stage) {
var column = $('<div>', { 'class': 'investcrm-kanban-column' });
column.append($('<h3>').text(stage.name));
var list = $('<ul>', { 'class': 'investcrm-kanban-list', 'data-stage': stage.id });
stage.opportunities.forEach(function (card) {
var item = $('<li>', { 'class': 'investcrm-kanban-card', 'data-id': card.id });
item.append($('<strong>').text(card.title));
item.append($('<span>').text('R$ ' + card.value.toLocaleString()));
list.append(item);
});
column.append(list);
return column;
}

function renderBoard(data) {
var board = $('#investcrm-kanban').empty();
if (!data.length) {
board.text('Nenhum dado no pipeline.');
return;
}
data.forEach(function (stage) {
board.append(renderColumn(stage));
});
}

function fetchPipeline() {
$('#investcrm-kanban').text(investcrmSettings.messages.loading);
wp.apiFetch({
url: investcrmSettings.apiUrl,
headers: { 'X-WP-Nonce': investcrmSettings.nonce }
}).then(function (response) {
renderBoard(response);
}).catch(function () {
$('#investcrm-kanban').text('Erro ao carregar pipeline.');
});
}

$(document).ready(function () {
if ($('#investcrm-kanban').length) {
fetchPipeline();
}
});
})(jQuery);
