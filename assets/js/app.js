$(function(){
  // after any ajax success, refresh csrfHash if server returns new token (optional)
  $(document).ajaxComplete(function(event, xhr, settings){
    var csrf = xhr.getResponseHeader('X-CSRF-Hash');
    if (csrf) { csrfHash = csrf; }
  });
});