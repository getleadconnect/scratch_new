$('#search').suggestionBox({
    filter: true,
    widthAdjustment: -8,
    leftOffset: 4,
    topOffset: 0,
    fadeIn: true,
    showNoSuggestionsMessage: true,
    noSuggestionsMessage: 'No Lead / Deals Found',
    url : '../user/global-search' 
})
// .loadSuggestions('../backend/search-box/suggestions.json');
