customElements.define('x-class-toggler', class extends HTMLButtonElement {
  connectedCallback() {
    this.addEventListener('click', (e) => {
          e.preventDefault();
          this.onClick()
        }
    );
    if (this.dataset.persist) {
      let state = this.state;
      if (state !== undefined) {
        this.onClick(state);
      }
    }
  }

  onClick(value) {
    const element = this.dataset.for ? document.querySelector('#' + this.dataset.for) :
        this.dataset.closest ? this.closest(this.dataset.closest) : document.querySelector('body');
    element.classList.toggle(this.dataset.toggle, value);
    if (this.dataset.persist) {
      this.state = element.classList.contains(this.dataset.toggle);
    }
  }

  get storageKey() {
    return 'x-class-toggler__' + this.dataset.toggle;
  }

  get state() {
    const sli = localStorage.getItem(this.storageKey)
    if (sli) {
      try {
        return JSON.parse(sli);
      }
      catch (e) {
      }
    }
    return undefined;
  }

  set state(b) {
    localStorage.setItem(this.storageKey, JSON.stringify(b))
  }
}, {extends: 'button'});