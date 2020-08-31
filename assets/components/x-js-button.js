customElements.define('x-js-button', class extends HTMLButtonElement {
  connectedCallback() {
    this.addEventListener('click', (e) => {
      e.preventDefault();
      this.onClick();
    });
    this.addEventListener('keypress', (e) => {
      if ('Enter' === e.key) {
        this.onClick()
      }
    });
  }

  onClick() {
    const request = {
      method: 'POST',
      headers: {
        'Csrf-token': this.dataset.csrf,
      }
    }
    if (this.dataset.value) {
      request.body = this.dataset.value;
    }
    if (this.dataset.key) {
      request.headers['Content-Type'] = 'application/json';
      request.body = JSON.stringify({[this.dataset.key]: this.dataset.value});
    }
    fetch(this.dataset.url, request)
        .then(resp => resp.json())
        .catch(resp => resp.json())
        .then(data => {
          if (data.reload) {
            window.location.reload();
          }
        })
  }
}, {extends: 'button'});
