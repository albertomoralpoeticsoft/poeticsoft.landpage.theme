export default () => {

  const $ = jQuery
  const $privacylinks = $('[href="/politica-privacidad"]')

  $privacylinks.on(
    'click',
    function() {
      
      const $this = $(this)
      const contenturl = $this.attr('href') 
    
      $('body').append('<div id="TermsDialog">Cargando...</div>')

      fetch(contenturl)
      .then(result => result.text()
        .then(content => {

          const $body = $(content).find('.wp-block-post-content')
          
          $('#TermsDialog').html($body)
        })
      )           

      $('#TermsDialog').dialog({
        dialogClass: 'privacy',
        modal: true,
        title: 'AVE™ Auditoría Visual Estratégica'
      }); 
      $('#TermsDialog').dialog('open')

      return false;
    }
  )

  return false
}