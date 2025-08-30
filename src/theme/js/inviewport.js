export default () => {

  const init = () => {

    const els = document.querySelectorAll('.ps-visual')

    if (!els.length) return

    const options = {
      root: null,
      rootMargin: '0px',
      threshold: 0
    }

    const observer = new IntersectionObserver(
      entries => {

        entries.forEach(entry => {

          const el = entry.target
          el.classList.remove('ps-in-viewport', 'ps-not-in-viewport')

          if (entry.isIntersecting) { el.classList.add('ps-in-viewport') } 
          else { el.classList.add('ps-not-in-viewport') }
        })
      }, 
      options
    )

    els.forEach(el => observer.observe(el))
  }

  init()
}
