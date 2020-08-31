import Toolpip from '../helpers/toolpip/toolpip';
import '../helpers/toolpip/toolpip.scss';

customElements.define('x-auto-save-button', class extends HTMLButtonElement {
  connectedCallback() {
    this.addEventListener('click', (e) => {
      e.preventDefault();
      this.onClick();
    });
    this._input = document.querySelector('#' + this.dataset.for);
    this._toolpip = new Toolpip(this._input);
    this._input.addEventListener('change', () => this.onChange());
    this._input.addEventListener('keypress', (e) => {
      if ('Enter' === e.key) {
        this.onClick()
      }
    });
  }

  onChange() {
    this._input.classList.remove('is-invalid', 'is-saved');
    this._input.classList.add('is-changed');
    this._toolpip.hide();
  }

  onClick() {
    this.disabled = this._input.disabled = true;
    this._toolpip.hide();
    const request = {
      method: 'POST',
      headers: {
        'Csrf-token': this.dataset.csrf,
      }
    }
    if ('file' === this._input.type) {
      const formData = new FormData();
      let name = this._input.name || ('file' + (this._input.hasAttribute('multiple') ? '[]' : ''))
      this._input.files.forEach(f => formData.append(name, f));
      request.body = formData;
    }
    else {
      if(this._input.name){
        request.headers['Content-Type'] = 'application/json';
        request.body = JSON.stringify({[this._input.name]: this._input.value})
      }
      else {
        request.body = this._input.value;
      }

    }
    fetch(this.dataset.save, request)
        .then(resp => resp.json())
        .catch(resp => resp.json())
        .then(data => {
          this.disabled = this._input.disabled = false;
          this._input.classList.remove('is-invalid', 'is-saved', 'is-changed');

          if ('error' === data.status) {
            this._input.focus();
            if (data.errors) {
              this._toolpip.updateContent(data.message, data.errors[0].message);
            }
            else {
              this._toolpip.updateContent('Error', data.message);
            }
            this._toolpip.show();
            this._input.classList.add('is-invalid')
          }
          else {
            this._input.classList.add('is-saved')
            if (data.value) {
              this._input.value = data.value;
            }
            if (data.reload) {
              window.location.reload();
            }
          }
        })
  }
}, {extends: 'button'});
