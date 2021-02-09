import style from './x-messages.inline.scss';

customElements.define('x-message', class extends HTMLElement {

  constructor() {
    super();
    this.shadow = this.attachShadow({mode: 'open'});
  }

  render() {
    const message = this.message;
    if (message.length) {
      this.removeAttribute('hidden')
      this.shadow.innerHTML = `<style>${style}</style><ul><li>${message.join('<li>')}</ul>`;
    }
    else {
      this.setAttribute('hidden', 'hidden');
      this.shadow.innerHTML = '';
    }
  }

  static get observedAttributes() {
    return ['message'];
  }

  get message() {
    let attr = this.getAttribute('message').trim();
    if (attr[0] === '{' || attr[0] === '[') {
      const data = JSON.parse(attr);
      const messages = [];
      for (let i in data) {
        if (data.hasOwnProperty(i)) {
          messages.push(data[i])
        }
      }
      return messages;
    }
    else {
      return attr ? [attr] : [];
    }

  }

  set message(message) {
    if (typeof message !== 'string') {
      message = JSON.stringify(message);
    }
    this.setAttribute('message', message);
  }

  attributeChangedCallback() {
    this.render();
  }

});
